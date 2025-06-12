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
                            <div className="mb-[50px]">
                            <H2 className="text-[40px] font-bold text-green-600">Result</H2>
                            <p className="text-[30px] font-medium text-gray-700">
                                We hope the result helps you
                            </p>
                            </div>

                            <div className="bg-gray-50 p-8 rounded-lg shadow-md">
                            <h3 className="text-[24px] font-bold mb-6 text-green-600">
                                Analysis Results
                            </h3>

                            {videoResult && (
                                <div className="space-y-6">

                                <div className="p-4 bg-white rounded border border-gray-200">
                                    <p className="font-bold text-lg text-green-600">Main Prediction:</p>
                                    <p className="text-xl">
                                    <span className="font-semibold text-gray-800">Status:</span>{" "}
                                    <span className="text-green-600 font-bold">
                                        {videoResult.result_prediction ?? "N/A"}
                                    </span>
                                    </p>
                                </div>


                                <div className="p-4 bg-white rounded border border-gray-200">
                                    <p className="font-bold text-lg text-green-600">Classification:</p>
                                    <p className="text-gray-800">
                                    <span className="font-semibold">Class:</span>{" "}
                                    <span className="text-green-600 font-bold">
                                        {videoResult.class_prediction ?? "Normal"}
                                    </span>
                                    </p>
                                    <p className="text-gray-800">
                                    <span className="font-semibold">Severity Level:</span>{" "}
                                    <span className="text-green-600 font-bold">
                                        {videoResult.severity ?? "Normal"}
                                    </span>
                                    </p>
                                </div>


                                <div className="p-4 bg-white rounded border border-gray-200">
                                    <p className="font-bold text-lg text-green-600">Face Analysis:</p>
                                    {videoResult.face_analysis ? (
                                    <>
                                        <p className="text-gray-800">
                                        <span className="font-semibold">Eye Score:</span>{" "}
                                        <span className={videoResult.face_analysis.eye_score ? "text-green-600 font-bold" : "text-gray-800"}>
                                            {videoResult.face_analysis.eye_score ?? "Normal"}
                                        </span>
                                        </p>
                                        <p className="text-gray-800">
                                        <span className="font-semibold">Emotion Score:</span>{" "}
                                        <span className={videoResult.face_analysis.emotion_score ? "text-green-600 font-bold" : "text-gray-800"}>
                                            {videoResult.face_analysis.emotion_score ?? "Normal"}
                                        </span>
                                        </p>
                                    </>
                                    ) : (
                                    <p className="text-gray-800">Normal</p>
                                    )}
                                </div>


                                <div className="p-4 bg-white rounded border border-gray-200">
                                    <p className="font-bold text-lg text-green-600">Movement Analysis:</p>
                                    {videoResult.movement_analysis ? (
                                    <p className="text-gray-800">
                                        <span className="font-semibold">Repetitive Score:</span>{" "}
                                        <span className={videoResult.movement_analysis.repetitive_score ? "text-green-600 font-bold" : "text-gray-800"}>
                                        {videoResult.movement_analysis.repetitive_score ?? "Normal"}
                                        </span>
                                    </p>
                                    ) : (
                                    <p className="text-gray-800">Normal</p>
                                    )}
                                </div>

                                <div className="p-4 bg-white rounded border border-gray-200">
                                    <p className="font-bold text-lg text-green-600">Overall Assessment:</p>
                                    <p className="text-gray-800">
                                    <span className="font-semibold">Combined Score:</span>{" "}
                                    <span className="text-green-600 font-bold text-xl">
                                        {videoResult.combined_score ?? "Normal"}
                                    </span>
                                    </p>
                                </div>
                                </div>
                            )}
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
