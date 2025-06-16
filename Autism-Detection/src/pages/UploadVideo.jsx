import FileUploader from "../components/sections/FileUploader";
import Button from "../components/common/Button";
import { useEffect, useState } from "react";
import axios from "axios";
import H2 from "../components/common/H2";

function UploadVideo() {
    const [isUploading, setIsUploading] = useState(false);
    const [files, setFiles] = useState([]);
    const [isUploaded, setIsUploaded] = useState(false);
    const [description, setDescription] = useState("");
    const [videoResult, setVideoResult] = useState(null);
    function handleFilesSelected(files) {
        console.log("Selected files:", files);
    }

    const handleUpload = async () => {
        if (files.length === 0) {
            alert("Please select a video to upload.");
            return;
        }
        console.log(files[0].name);
        setIsUploading(true);

        try {
            // Fetch CSRF token
            await axios.get("http://localhost:8000/sanctum/csrf-cookie");

            // Prepare form data
            const formData = new FormData();
            formData.append("video", files[0].file);
            formData.append("description", description);
            console.log(formData);
            // Send upload request
            const response = await axios.post(
                "http://localhost:8000/api/upload-video",
                formData,
                {
                    headers: {
                        "Content-Type": "multipart/form-data",
                        Authorization: `Bearer ${localStorage.getItem(
                            "token"
                        )}`,
                    },
                    withCredentials: true,
                }
            );
            console.log("Full API Response:", response.data);
            if (response.status === 200) {
                setIsUploaded(true);
                const videoData = response.data;
                console.log("Uploaded Video Metadata:", videoData);
                setVideoResult(videoData);
            }
            console.log(videoResult);
        } catch (error) {
            console.error(
                "Upload failed:",
                error.response?.data || error.message
            );
            alert(
                "Upload failed: " +
                    (error.response?.data?.message || error.message)
            );
        } finally {
            setIsUploading(false);
        }
    };

    // Log videoResult when it changes
    useEffect(() => {
        console.log("Updated videoResult:", videoResult);
    }, [videoResult]);

    return (
        <div className="pt-24 bg-[var(--primary)] font-nunito min-h-screen">
            <div className="font-nunito w-full bg-[var(--fifth)] flex justify-center items-center">
                <div className="w-[80%] h-svh pt-9 pb-15">
                    {isUploaded ? (
                        <>
  {/* Final Result Header */}
  <div className="mb-16 text-center">
    <H2 className="text-[48px] font-extrabold text-black tracking-wide">
      Final Result:{" "}
      <span
        className={`font-bold ${
          videoResult?.result_prediction === "autistic"
            ? "text-red-600"
            : "text-green-600"
        }`}
      >
        {videoResult?.result_prediction ?? "N/A"}
      </span>
    </H2>
    <p className="text-[20px] text-gray-600 mt-4">
      We’ve analyzed your video and compiled the key behavioral insights below.
    </p>
  </div>

  {/* Details Grid */}
  <div className="grid grid-cols-1 md:grid-cols-2 gap-10 text-[18px] text-gray-800 leading-[1.7]">
    {/* Classification */}
    <div className="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
      <h3 className="font-bold text-xl mb-2 text-gray-900">Class of autism</h3>
      <p className="font-bold text-green text-[22px] mb-1">
        {videoResult.class_prediction ?? "Normal"}
      </p>
      <p className="text-gray-600">
        Indicates the class of autism the child has.
      </p>
    </div>

    {/* Severity */}
    <div className="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
      <h3 className="font-bold text-xl mb-2 text-gray-900">Severity of autism</h3>
      <p
        className={`font-bold text-[22px] mb-1 ${
          videoResult.severity === "Mild"
            ? "text-green-600"
            : videoResult.severity === "Moderate"
            ? "text-black"
            : videoResult.severity === "Severe"
            ? "text-red-600"
            : "text-green"
        }`}
      >
        {videoResult.severity ?? "Normal"}
      </p>
      <p className="text-gray-600">
        Reflects how intense or concerning the detected behavior is.
      </p>
    </div>

    {/* Eye Contact */}
    <div className="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
      <h3 className="font-bold text-xl mb-2 text-gray-900">Eye Contact score</h3>
      <p
        className={`font-bold text-[22px] mb-1 ${
          +videoResult.face_analysis?.eye_score >=1&& +videoResult.face_analysis?.eye_score<=4
            ? "text-green-600"
            : +videoResult.face_analysis?.eye_score >=5&&+videoResult.face_analysis?.eye_score<= 7
            ? "text-black"
            : +videoResult.face_analysis?.eye_score >=8&&+videoResult.face_analysis?.eye_score<= 10
            ? "text-red-600"
            : "text-green-600"
        }`}
      >
        {videoResult.face_analysis?.eye_score ?? "Normal"}
      </p>
      <p className="text-gray-600">
        Measures how often and consistently the child maintains eye contact.
      </p>
    </div>

    {/* Facial Emotion */}
    <div className="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
      <h3 className="font-bold text-xl mb-2 text-gray-900">Facial Emotion score</h3>
      <p
        className={`font-bold text-[22px] mb-1 ${
          +videoResult.face_analysis?.emotion_score < 5
            ? "text-green-600"
            : +videoResult.face_analysis?.emotion_score > 5
            ? "text-red-600"
            : "text-black"
        }`}
      >
        {videoResult.face_analysis?.emotion_score ?? "Normal"}
      </p>
      <p className="text-gray-600">
        Indicates how clearly emotional expressions appear on the child’s face.
      </p>
    </div>

    {/* Repetitive Movements */}
    <div className="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
      <h3 className="font-bold text-xl mb-2 text-gray-900">Repetitive Movements score</h3>
      <p
        className={`font-bold text-[22px] mb-1 ${
          +videoResult.movement_analysis?.repetitive_score >=1&&+videoResult.movement_analysis?.repetitive_score <= 4
            ? "text-green-600"
            : +videoResult.movement_analysis?.repetitive_score >=5 &&+videoResult.movement_analysis?.repetitive_score <= 7
            ? "text-black"
            :+videoResult.movement_analysis?.repetitive_score >=8&&+videoResult.movement_analysis?.repetitive_score<=10
            ? "text-red-600"
            :"text-green-600"
        }`}
      >
        {videoResult.movement_analysis?.repetitive_score ?? "Normal"}
      </p>
      <p className="text-gray-600">
        Evaluates the frequency of repeated physical behaviors like hand flapping or spinning.
      </p>
    </div>

    {/* Overall Score */}
    <div className="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
      <h3 className="font-bold text-xl mb-2 text-gray-900">Overall Score</h3>
      <p
        className={`font-bold text-[24px] mb-1 ${
          +videoResult.combined_score>=1&& +videoResult.combined_score <= 4
            ? "text-green-600"
            : +videoResult.combined_score>=5&& +videoResult.combined_score <= 7
            ? "text-black"
            :+videoResult.combined_score>=8&&  +videoResult.combined_score<=10
            ? "text-red-600"
            :"text-green-600"
        }`}
      >
        {videoResult.combined_score ?? "Normal"}
      </p>
      <p className="text-gray-600">
        A final summary score combining all metrics above.
      </p>
    </div>
  </div>
</>


                    ) : (
                        <>
                            <div className="mb-[50px]">
                                <H2 className="text-[40px] font-bold">
                                    Upload files
                                </H2>
                                <p className="text-[30px] font-medium text-[#333333]">
                                    Select and upload the files of your choice
                                </p>
                            </div>
                            <div className="flex justify-between">
                                <div className="w-[47%]">
                                    <FileUploader
                                        onFilesSelected={handleFilesSelected}
                                        setFiles={setFiles}
                                        isUploaded={isUploaded}
                                        setIsUploaded={setIsUploaded}
                                        files={files}
                                    />
                                </div>
                                <div className="relative w-[47%] text-end">
                                    <div className="mt-16 text-start">
                                        <h2 className="text-[24px] font-bold">
                                            Description
                                        </h2>
                                        <textarea
                                            className="mt-9 text-[#303030] font-semibold text-[16px] border border-[#333333] w-full h-[142px] p-3 resize-none"
                                            placeholder="Write here your comment"
                                            value={description}
                                            onChange={(e) =>
                                                setDescription(e.target.value)
                                            }
                                        ></textarea>
                                    </div>
                                    <Button
                                        position="absolute bottom-0 right-0"
                                        padding="py-3 px-8"
                                        onClick={handleUpload}
                                        disabled={
                                            files.length === 0 || isUploading
                                        }
                                    >
                                        {isUploading
                                            ? "Uploading..."
                                            : "See Result"}
                                    </Button>
                                </div>
                            </div>
                        </>
                    )}
                </div>
            </div>
        </div>
    );
}

export default UploadVideo;
