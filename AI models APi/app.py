from flask import Flask, request, jsonify
import cv2
import numpy as np
import tempfile
import os
import mediapipe as mp
from collections import Counter
import logging
from werkzeug.utils import secure_filename
import pandas as pd
import joblib
import uuid
from sklearn import set_config
from rtmlib.tools.solution import PoseTracker, Wholebody

app = Flask(__name__)

# Setup logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Set sklearn config
set_config(assume_finite=True)

# --- Configuration ---
# Face analysis config
FACE_MODEL_NAME = "default"
FACE_DEVICE = "cuda" if cv2.cuda.getCudaEnabledDeviceCount() > 0 else "cpu"

# Body analysis config
BODY_DEVICE = 'cpu'  # Changed to CPU to rule out CUDA issues
BODY_BACKEND = 'onnxruntime'
OPENPOSE_SKELETON = False

# --- Initialize Models ---
class EmotiEffLibRecognizer:
    def __init__(self, engine, model_name, device):
        pass
    def predict_emotions(self, faces, logits=True):
        return [["neutral", 0.9]]  # Dummy output

def initialize_face_models():
    try:
        return {
            "fer": EmotiEffLibRecognizer(engine="onnx", model_name=FACE_MODEL_NAME, device=FACE_DEVICE),
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
        logger.error(f"Error initializing face models: {e}")
        return None

def initialize_body_tracker():
    try:
        return PoseTracker(
            Wholebody,
            det_frequency=7,
            tracking=True,
            to_openpose=OPENPOSE_SKELETON,
            mode='performance',
            backend=BODY_BACKEND,
            device=BODY_DEVICE
        )
    except Exception as e:
        logger.error(f"Error initializing body tracker: {e}")
        return None

def load_behavior_models():
    try:
        return {
            "scaler_stage1": joblib.load("scaler_tfb5.joblib"),
            "pca_stage1": joblib.load("pca_tfb5.joblib"),
            "model_stage1": joblib.load("XGBoost_tfb5.joblib"),
            "scaler_stage2": joblib.load("scaler_tfb.joblib"),
            "pca_stage2": joblib.load("pca_tfb.joblib"),
            "model_stage2": joblib.load("XGBoost_tfb.joblib")
        }
    except Exception as e:
        logger.error(f"Error loading behavior models: {e}")
        return None

# Initialize all models at startup
face_models = initialize_face_models()
body_tracker = initialize_body_tracker()
behavior_models = load_behavior_models()

# --- Helper Functions ---
def get_landmark_points(landmarks, indexes, w, h):
    try:
        return np.array([(int(landmarks[i].x * w), int(landmarks[i].y * h)) for i in indexes])
    except Exception as e:
        logger.error(f"Error getting landmark points: {e}")
        return np.array([])

def get_severity(score):
    if 1 <= score <= 4:
        return "Mild"
    elif 5 <= score <= 7:
        return "Moderate"
    elif 8 <= score <= 10:
        return "Severe"
    return None

# --- Facial Analysis Functions ---
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

def facial_analysis(video_path):
    try:
        cap = cv2.VideoCapture(video_path)
        if not cap.isOpened():
            logger.error(f"Cannot open video: {video_path}")
            return {"error": "Failed to open video"}

        total_frames = 0
        eye_contact_frames = 0
        emotion_counter = Counter()

        while cap.isOpened():
            success, frame = cap.read()
            if not success:
                break

            frame = cv2.flip(frame, 1)
            processed_frame = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
            face_results = face_models["face_mesh"].process(processed_frame)

            head_pose_text = estimate_head_pose(face_results, frame)
            emotion = detect_emotion(face_models["fer"], processed_frame, face_models["face_detection"])
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

        return {
            "eye_score": eye_score,
            "emotion_score": emotion_score,
            "emotion_percentages": emotion_percentages,
            "status": "success"
        }
    except Exception as e:
        logger.error(f"Error in facial analysis: {e}")
        return {"error": str(e)}

# --- Body Movement Analysis Functions ---
def interpolate_frames(video_path, target_frame_count=120):
    try:
        cap = cv2.VideoCapture(video_path)
        frames = []

        while True:
            ret, frame = cap.read()
            if not ret:
                break
            frames.append(frame)

        cap.release()

        original_frame_count = len(frames)
        logger.info(f"Original frame count: {original_frame_count}")
        if original_frame_count < 10:
            raise ValueError("Video is too short; need at least 10 frames.")

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
        final_frames = interpolated_frames[:target_frame_count]
        logger.info(f"Interpolated frame count: {len(final_frames)}")
        return final_frames
    except Exception as e:
        logger.error(f"Error interpolating frames: {e}")
        raise

def get_keypoints(interpolated_frames):
    all_points = []
    valid_frames = 0
    expected_keypoints = 42
    keypoint_indices = [1, 2, 3, 4, 5, 7, 8, 9, 10, 11, 13, 15, 16, 17, 18, 19, 20, 21, 22, 23,
                        92, 95, 96, 98, 100, 102, 104, 106, 108, 110, 112, 113, 116, 117,
                        119, 121, 123, 125, 127, 129, 131, 133]

    for frame_idx, frame in enumerate(interpolated_frames):
        keypoints, scores = body_tracker(frame)
        logger.debug(f"Frame {frame_idx + 1}: Keypoints shape: {keypoints.shape}")
        if keypoints.shape[0] == 0:
            logger.debug(f"Frame {frame_idx + 1}: No keypoints detected")
            continue
        if keypoints.shape[1] < max(keypoint_indices):
            logger.debug(f"Frame {frame_idx + 1}: Expected at least {max(keypoint_indices)} keypoints, got {keypoints.shape[1]}")
            continue
        valid_frames += 1
        frame_points = []
        for p in keypoint_indices:
            try:
                x, y = keypoints[0][p - 1]
                frame_points.extend([float(x), float(y)])
            except IndexError as e:
                logger.error(f"Keypoint index error at frame {frame_idx + 1}, index {p - 1}: {e}")
                return []
        if len(frame_points) != expected_keypoints * 2:
            logger.error(f"Frame {frame_idx + 1}: Expected {expected_keypoints * 2} coordinates, got {len(frame_points)}")
            return []
        all_points.extend(frame_points)

    logger.info(f"Processed {valid_frames} valid frames with {len(all_points)} total coordinates")
    if valid_frames < 100:
        raise ValueError(f"Only {valid_frames} frames had detectable keypoints; need at least 100.")
    if len(all_points) != 10080:
        raise ValueError(f"Extracted {len(all_points)} coordinates; expected 10080.")
    return all_points

def repetitive_movement_analysis(video_path):
    try:
        interpolated_frames = interpolate_frames(video_path)
        all_points = get_keypoints(interpolated_frames)

        if len(all_points) != 10080:
            return {
                "error": f"Extracted {len(all_points)} features; expected 10080",
                "suggestion": "Ensure the video is clear, well-lit, and shows a person in the frame"
            }

        # Calculate repetitive motion percentage
        keypoints_array = np.array(all_points).reshape(-1, 84)
        diffs = np.diff(keypoints_array, axis=0)
        movement_magnitude = np.linalg.norm(diffs, axis=1)
        threshold = np.median(movement_magnitude) * 0.5
        repetitive_frames = np.sum(movement_magnitude < threshold)
        repetitive_percentage = (repetitive_frames / len(movement_magnitude)) * 100

        # Score based on percentage
        if repetitive_percentage >= 90:
            score = 10
        elif repetitive_percentage >= 80:
            score = 9
        elif repetitive_percentage >= 70:
            score = 8
        elif repetitive_percentage >= 60:
            score = 7
        elif repetitive_percentage >= 50:
            score = 6
        elif repetitive_percentage >= 40:
            score = 5
        elif repetitive_percentage >= 30:
            score = 4
        elif repetitive_percentage >= 20:
            score = 3
        elif repetitive_percentage >= 10:
            score = 2
        else:
            score = 1

        return {
            "repetitive_score": score,
            "repetitive_percentage": repetitive_percentage,
            "status": "success"
        }
    except Exception as e:
        logger.error(f"Error in repetitive movement analysis: {e}")
        return {"error": str(e)}

def behavior_analysis(video_path):
    try:
        if not behavior_models:
            return {"error": "Behavior models not loaded"}

        interpolated_frames = interpolate_frames(video_path)
        all_points = get_keypoints(interpolated_frames)

        if len(all_points) != 10080:
            return {
                "error": f"Extracted {len(all_points)} features; expected 10080",
                "suggestion": "Ensure the video is clear, well-lit, and shows a person in the frame"
            }

        # Prepare data for prediction
        all_points = np.array(all_points).reshape(1, -1)
        df_all_points = pd.DataFrame(all_points, columns=[f'F{i}' for i in range(1, 10081)])

        # Stage 1 prediction
        stage1_classes = {0: "autistic", 1: "normal"}
        scaled_stage1 = behavior_models["scaler_stage1"].transform(df_all_points)
        pca_stage1_data = behavior_models["pca_stage1"].transform(scaled_stage1)
        stage1_pred = behavior_models["model_stage1"].predict(pca_stage1_data)[0]
        logger.info(f"Stage 1 prediction: {stage1_pred}")

        if stage1_pred not in stage1_classes:
            raise ValueError(f"Invalid Stage 1 prediction: {stage1_pred}")

        stage1_label = stage1_classes[stage1_pred]

        # Stage 2 prediction if autistic
        stage2_label = None
        if stage1_label == "autistic":
            stage2_classes = {0: 'pacing', 1: 'toe_walking', 2: 'arm_flapping', 3: 'spinning', 4: 'headbang'}
            scaled_stage2 = behavior_models["scaler_stage2"].transform(df_all_points)
            pca_stage2_data = behavior_models["pca_stage2"].transform(scaled_stage2)
            stage2_pred = behavior_models["model_stage2"].predict(pca_stage2_data)[0]
            logger.info(f"Stage 2 prediction: {stage2_pred}")

            if stage2_pred not in stage2_classes:
                raise ValueError(f"Invalid Stage 2 prediction: {stage2_pred}")

            stage2_label = stage2_classes[stage2_pred]

        return {
            "stage1_prediction": stage1_label,
            "stage2_behavior": stage2_label,
            "status": "success"
        }

    except Exception as e:
        logger.error(f"Error in behavior analysis: {e}")
        return {"error": str(e)}

# --- API Endpoints ---
@app.route('/analyze-face', methods=['POST'])
def analyze_face():
    if 'video' not in request.files:
        return jsonify({'error': 'No video file provided'}), 400

    video_file = request.files['video']
    if video_file.filename == '':
        return jsonify({'error': 'No selected file'}), 400

    # Save video temporarily
    temp_dir = tempfile.mkdtemp()
    video_path = os.path.join(temp_dir, secure_filename(video_file.filename))
    video_file.save(video_path)

    try:
        result = facial_analysis(video_path)
        if "error" in result:
            return jsonify(result), 400

        return jsonify(result)
    except Exception as e:
        logger.error(f"Error in face analysis: {str(e)}")
        return jsonify({'error': str(e)}), 500
    finally:
        # Clean up
        if os.path.exists(video_path):
            os.remove(video_path)
        os.rmdir(temp_dir)

@app.route('/analyze-movement', methods=['POST'])
def analyze_movement():
    if 'video' not in request.files:
        return jsonify({'error': 'No video file provided'}), 400

    video_file = request.files['video']
    if video_file.filename == '':
        return jsonify({'error': 'No selected file'}), 400

    # Save video temporarily
    temp_dir = tempfile.mkdtemp()
    video_path = os.path.join(temp_dir, secure_filename(video_file.filename))
    video_file.save(video_path)

    try:
        result = repetitive_movement_analysis(video_path)
        if "error" in result:
            return jsonify(result), 400

        return jsonify(result)
    except Exception as e:
        logger.error(f"Error in movement analysis: {str(e)}")
        return jsonify({'error': str(e)}), 500
    finally:
        # Clean up
        if os.path.exists(video_path):
            os.remove(video_path)
        os.rmdir(temp_dir)

@app.route('/analyze-behavior', methods=['POST'])
def analyze_behavior():
    if 'video' not in request.files:
        return jsonify({'error': 'No video file provided'}), 400

    video_file = request.files['video']
    if video_file.filename == '':
        return jsonify({'error': 'No selected file'}), 400

    # Save video temporarily
    temp_dir = tempfile.mkdtemp()
    video_path = os.path.join(temp_dir, secure_filename(video_file.filename))
    video_file.save(video_path)

    try:
        result = behavior_analysis(video_path)
        if "error" in result:
            return jsonify(result), 400

        return jsonify(result)
    except Exception as e:
        logger.error(f"Error in behavior analysis: {str(e)}")
        return jsonify({'error': str(e)}), 500
    finally:
        # Clean up
        if os.path.exists(video_path):
            os.remove(video_path)
        os.rmdir(temp_dir)

@app.route('/full-analysis', methods=['POST'])
def full_analysis():
    logger.info("Full analysis endpoint called")
    if 'video' not in request.files:
        logger.error("No video file provided")
        return jsonify({'error': 'No video file provided'}), 400

    video_file = request.files['video']
    if video_file.filename == '':
        logger.error("No selected file")
        return jsonify({'error': 'No selected file'}), 400

    temp_dir = tempfile.mkdtemp()
    video_path = os.path.join(temp_dir, secure_filename(video_file.filename))
    video_file.save(video_path)

    try:
        behavior_result = behavior_analysis(video_path)
        if "error" in behavior_result:
            logger.error(f"Behavior analysis error: {behavior_result['error']}")
            return jsonify(behavior_result), 400

        if behavior_result['stage1_prediction'] == "normal":
            return jsonify({
                'stage1_prediction': behavior_result['stage1_prediction'],
                'stage2_behavior': None,
                'face_analysis': None,
                'movement_analysis': None,
                'combined_score': 0,
                'severity': "None (0)",
                'status': 'success',
                'message': 'Normal behavior detected'
            })

        face_result = facial_analysis(video_path)
        movement_result = repetitive_movement_analysis(video_path)

        if "error" in face_result or "error" in movement_result:
            logger.error(f"Face error: {face_result.get('error', '')}, Movement error: {movement_result.get('error', '')}")
            return jsonify({'error': 'Analysis failed', 'face_error': face_result.get('error'), 'movement_error': movement_result.get('error')}), 400

        combined_score = int(round((
            face_result.get('eye_score', 5) +
            face_result.get('emotion_score', 5) +
            movement_result.get('repetitive_score', 5)
        ) / 3))
        severity = get_severity(combined_score)

        return jsonify({
            'stage1_prediction': behavior_result['stage1_prediction'],
            'stage2_behavior': behavior_result['stage2_behavior'],
            'face_analysis': {
                'eye_score': face_result.get('eye_score'),
                'emotion_score': face_result.get('emotion_score')
            },
            'movement_analysis': {
                'repetitive_score': movement_result.get('repetitive_score')
            },
            'combined_score': combined_score,
            'severity': severity,
            'status': 'success'
        })
    except Exception as e:
        logger.error(f"Full analysis error: {str(e)}")
        return jsonify({'error': str(e)}), 500
    finally:
        if os.path.exists(video_path):
            os.remove(video_path)
        os.rmdir(temp_dir)

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)
