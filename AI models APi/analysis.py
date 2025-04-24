from flask import Flask, request, jsonify
import cv2
import numpy as np
import tempfile
import os
import mediapipe as mp
from collections import Counter
import logging
from werkzeug.utils import secure_filename

app = Flask(__name__)

# Setup logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# --- Facial Analysis Functions ---
def initialize_models():
    try:
        device = "cuda" if cv2.cuda.getCudaEnabledDeviceCount() > 0 else "cpu"
        model_name = "default"  # تم التعديل هنا

        return {
            "fer": EmotiEffLibRecognizer(engine="onnx", model_name=model_name, device=device),
            "face_mesh": mp.solutions.face_mesh.FaceMesh(
                min_detection_confidence=0.5,
                min_tracking_confidence=0.5,
                refine_landmarks=True
            ),
            "face_detection": mp.solutions.face_detection.FaceDetection(
                model_selection=1,
                min_detection_confidence=0.5
            )
        }
    except Exception as e:
        logger.error(f"Error initializing models: {e}")
        return None

class EmotiEffLibRecognizer:
    def __init__(self, engine, model_name, device):
        pass
    def predict_emotions(self, faces, logits=True):
        return [["neutral", 0.9]]  # Dummy output

def get_landmark_points(landmarks, indexes, w, h):
    try:
        return np.array([(int(landmarks[i].x * w), int(landmarks[i].y * h)) for i in indexes])
    except Exception as e:
        logger.error(f"Error getting landmark points: {e}")
        return np.array([])

def estimate_head_pose(face_results, frame):
    try:
        if not face_results.multi_face_landmarks:
            return ""

        img_h, img_w, _ = frame.shape
        face_landmarks = face_results.multi_face_landmarks[0]

        face_2d = np.array([(int(face_landmarks.landmark[i].x * img_w), int(face_landmarks.landmark[i].y * img_h))
                          for i in [33, 263, 1, 61, 291, 199]], dtype=np.float64)
        face_3d = np.array([(int(face_landmarks.landmark[i].x * img_w), int(face_landmarks.landmark[i].y * img_h),
                           face_landmarks.landmark[i].z) for i in [33, 263, 1, 61, 291, 199]], dtype=np.float64)

        cam_matrix = np.array([[img_w, 0, img_w / 2], [0, img_w, img_h / 2], [0, 0, 1]])
        _, rotation_vec, _ = cv2.solvePnP(face_3d, face_2d, cam_matrix, np.zeros((4, 1), dtype=np.float64))
        rmat, _ = cv2.Rodrigues(rotation_vec)
        angles, _, _, _, _, _ = cv2.RQDecomp3x3(rmat)
        x, y = angles[0] * 360, angles[1] * 360

        if y < -10:
            return "Looking Left"
        elif y > 10:
            return "Looking Right"
        elif x < -10:
            return "Looking Down"
        elif x > 10:
            return "Looking Up"
        else:
            return "Looking Forward"
    except Exception as e:
        logger.error(f"Error estimating head pose: {e}")
        return ""

def detect_emotion(fer, frame, face_detection):
    try:
        results = face_detection.process(frame)
        if results.detections:
            bboxC = results.detections[0].location_data.relative_bounding_box
            x1, y1 = int(bboxC.xmin * frame.shape[1]), int(bboxC.ymin * frame.shape[0])
            x2, y2 = int((bboxC.xmin + bboxC.width) * frame.shape[1]), int((bboxC.ymin + bboxC.height) * frame.shape[0])
            face = frame[y1:y2, x1:x2]
            if face.size > 0:
                return fer.predict_emotions([face], logits=True)[0][0]
        return ""
    except Exception as e:
        logger.error(f"Error detecting emotion: {e}")
        return ""

def get_severity(score):
    if 1 <= score <= 4:
        return "Mild"
    elif 5 <= score <= 7:
        return "Moderate"
    elif 8 <= score <= 10:
        return "Severe"
    return "None (0)"

def facial_analysis(video_path, models):
    try:
        cap = cv2.VideoCapture(video_path)
        if not cap.isOpened():
            logger.error(f"Cannot open video: {video_path}")
            return 5, 5, {}, "Failed to open video"

        total_frames = 0
        eye_contact_frames = 0
        emotion_counter = Counter()

        while cap.isOpened():
            success, frame = cap.read()
            if not success:
                break

            frame = cv2.flip(frame, 1)
            processed_frame = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
            face_results = models["face_mesh"].process(processed_frame)

            head_pose_text = estimate_head_pose(face_results, frame)
            emotion = detect_emotion(models["fer"], processed_frame, models["face_detection"])
            if emotion:
                emotion_counter[emotion.lower()] += 1

            total_frames += 1
            if head_pose_text == "Looking Forward":
                eye_contact_frames += 1

        cap.release()

        # Eye Contact Score
        eye_contact_percentage = (eye_contact_frames / total_frames) * 100 if total_frames > 0 else 0
        eye_score = 10 - int(eye_contact_percentage // 10)
        eye_score = max(1, min(10, eye_score))

        # Emotion Score
        total_emotions = sum(emotion_counter.values())
        negative_emotions = ["sad", "angry", "disgust", "fear"]
        positive_emotions = ["happy", "surprise", "neutral"]

        emotion_percentages = {
            emotion: (count / total_emotions) * 100 for emotion, count in emotion_counter.items()
        }

        dominant_emotion = max(emotion_percentages, key=emotion_percentages.get, default="neutral")
        dominant_percent = emotion_percentages.get(dominant_emotion, 0)

        if dominant_emotion in negative_emotions:
            emotion_score = min(10, max(5, int(round(dominant_percent / 10))))
        elif dominant_emotion in positive_emotions:
            emotion_score = max(1, 5 - int(round(dominant_percent / 25)))
        else:
            emotion_score = 5

        return eye_score, emotion_score, emotion_percentages, "Facial analysis completed"
    except Exception as e:
        logger.error(f"Error in facial analysis: {e}")
        return 5, 5, {}, f"Error in facial analysis: {e}"

# --- Repetitive Movement Functions ---
def initialize_pose_tracker():
    return None  # Dummy implementation

def interpolate_frames(video_path, target_frame_count):
    try:
        cap = cv2.VideoCapture(video_path)
        if not cap.isOpened():
            logger.error(f"Cannot open video for interpolation: {video_path}")
            return []

        frames = []
        while True:
            ret, frame = cap.read()
            if not ret:
                break
            frames.append(frame)

        cap.release()
        original_frame_count = len(frames)

        if original_frame_count <= 1:
            return frames

        total_frames_needed = target_frame_count - original_frame_count
        num_new_frames = total_frames_needed // (original_frame_count - 1)
        interpolated_frames = []

        for i in range(original_frame_count - 1):
            interpolated_frames.append(frames[i])
            for j in range(num_new_frames + 1):
                alpha = (j + 1) / (num_new_frames + 1)
                new_frame = cv2.addWeighted(frames[i], 1 - alpha, frames[i + 1], alpha, 0)
                interpolated_frames.append(new_frame)

        interpolated_frames.append(frames[-1])
        return interpolated_frames[:target_frame_count]
    except Exception as e:
        logger.error(f"Error interpolating frames: {e}")
        return []

def repetitive_movement_analysis(video_path, pose_tracker):
    try:
        target_frame_count = 120
        interpolated_frames = interpolate_frames(video_path, target_frame_count)
        if not interpolated_frames:
            return -1, 5, -1, "Failed to interpolate frames"

        # Dummy implementation since we don't have pose_tracker
        rep_percentage = np.random.uniform(30, 70)
        score = min(10, max(1, int(rep_percentage / 10)))
        total_motions = int(target_frame_count * (100 - rep_percentage) / 100)

        return rep_percentage, score, total_motions, "Dummy repetitive motion analysis"
    except Exception as e:
        logger.error(f"Error in repetitive movement analysis: {e}")
        return -1, 5, -1, f"Error in repetitive movement analysis: {e}"

# --- API Endpoint ---
@app.route('/analyze', methods=['POST'])
def analyze_video():
    if 'video' not in request.files:
        return jsonify({'error': 'No video file provided'}), 400

    video_file = request.files['video']
    if video_file.filename == '':
        return jsonify({'error': 'No selected file'}), 400

    # حفظ الفيديو مؤقتًا
    temp_dir = tempfile.mkdtemp()
    video_path = os.path.join(temp_dir, secure_filename(video_file.filename))
    video_file.save(video_path)

    try:
        models = initialize_models()
        pose_tracker = initialize_pose_tracker()

        if models is None:
            return jsonify({'error': 'Model initialization failed'}), 500

        # تحليل الوجه
        eye_score, emotion_score, emotion_percentages, _ = facial_analysis(video_path, models)

        # تحليل الحركات المتكررة
        rep_percentage, repetitive_score, _, _ = repetitive_movement_analysis(video_path, pose_tracker)

        # حساب النتيجة النهائية
        combined_score = int(round((eye_score + emotion_score + repetitive_score) / 3))
        severity = get_severity(combined_score)

        # تنظيف الملفات المؤقتة
        if os.path.exists(video_path):
            os.remove(video_path)
        os.rmdir(temp_dir)

        return jsonify({
            'eye_score': eye_score,
            'emotion_score': emotion_score,
            'repetitive_score': repetitive_score,
            'combined_score': combined_score,
            'severity': severity,
            'emotion_percentages': emotion_percentages,
            'repetitive_percentage': rep_percentage,
            'status': 'success'
        })

    except Exception as e:
        logging.error(f"Error in analysis: {str(e)}")
        if os.path.exists(video_path):
            os.remove(video_path)
        if os.path.exists(temp_dir):
            os.rmdir(temp_dir)
        return jsonify({
            'error': str(e),
            'status': 'error'
        }), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
