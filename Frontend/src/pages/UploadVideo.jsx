import React, { useState } from 'react';
import Header from '../layouts/Header';
import FileUploader from '../components/sections/FileUploader';
import Button from '../components/common/Button';
import api from '../api'; // تم التعديل هنا

function UploadVideo({ onShowSignIn }) {
  const [files, setFiles] = useState([]);
  const [isUploading, setIsUploading] = useState(false);
  const [isUploaded, setIsUploaded] = useState(false);

  const handleFilesSelected = (selectedFiles) => {
    console.log("Selected files:", selectedFiles);
  };

  const handleUpload = async () => {
    if (files.length === 0) return;

    setIsUploading(true);
    try {
      const formData = new FormData();
      formData.append('video', files[0].file);

      const response = await api.post('/upload-video', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });

      setIsUploaded(true);
      console.log('Upload successful:', response.data);
    } catch (error) {
      console.error('Upload error:', error);
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
          <div className="mb-[50px]">
            <h2 className="text-[40px] font-bold">Upload files</h2>
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
                <h2 className="text-[24px] font-bold">Description</h2>
                <textarea
                  className="mt-9 text-[#303030] font-semibold text-[16px] border border-[#333333] w-full h-[142px] p-3 resize-none"
                  placeholder="Write here your comment"
                ></textarea>
              </div>
              <Button
                position="absolute bottom-0 right-0"
                padding="py-3 px-8"
                onClick={handleUpload}
                disabled={files.length === 0 || isUploading}
              >
                {isUploading ? "Uploading..." : "See Result"}
              </Button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default UploadVideo;
