import FileUploader from "../components/sections/FileUploader";
import Button from "../components/common/Button";
import Header from "../layouts/Header";
import { useState } from "react";
import axios from "axios";
import H2 from "../components/common/H2";

function UploadVideo({ onShowSignIn }) {
    const [isUploading, setIsUploading] = useState(false);
    const [files, setFiles] = useState([]);
    const [isUploaded, setIsUploaded] = useState(false);
    const [description, setDescription] = useState("");

    function handleFilesSelected(files) {
        console.log("Selected files:", files);
    }

    const handleUpload = async () => {
        if (files.length === 0) {
            alert("Please select a video to upload.");
            return;
        }

        setIsUploading(true);

        try {
            // Fetch CSRF token
            await axios.get("http://localhost:8000/sanctum/csrf-cookie");

            // Prepare form data
            const formData = new FormData();
            formData.append("video", files[0].file);
            formData.append("description", description);

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
                    withCredentials: true, // ضروري لـ Sanctum
                }
            );

            if (response.status === 200) {
                setIsUploaded(true);
                const videoData = response.data.video;
                console.log("Uploaded Video Metadata:", videoData);
                alert(
                    `Video Uploaded: ${videoData.name}\nSize: ${(
                        videoData.size /
                        (1024 * 1024)
                    ).toFixed(2)} MB\nDescription: ${videoData.description}`
                );
            }
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

    return (
        <div className="pt-24 bg-[var(--primary)] font-nunito min-h-screen">
            <section className="w-[1350px] mx-auto">
                <Header onShowSignIn={onShowSignIn} />
            </section>
            <div className="font-nunito w-full bg-[var(--fifth)] flex justify-center items-center">
                <div className="w-[80%] h-svh pt-9 pb-15">
                    {isUploaded ? (
                        <>
                            <div className="mb-[50px]">
                                <H2 className="text-[40px] font-bold">
                                    Result
                                </H2>
                                <p className="text-[30px] font-medium text-[#333333]">
                                    we hope the result help you
                                </p>
                            </div>
                            <h3 className="text-[24px] font-bold">
                                the result
                            </h3>
                            <textarea
                                className="mt-9 text-[#303030] font-semibold text-[16px] border border-[#333333] w-full h-[142px] p-3 resize-none"
                                placeholder="Write here your comment"
                            ></textarea>
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
