import React, { useState } from "react";
import LoginHeader from "../common/LoginHeader";
import InputForm from "../common/InputForm";
import Button from "../common/Button";
import SignUpInToggleBtn from "../common/SignUpInToggleBtn";
import api from "../../api"; // استيراد api

const inputs = [
    // تعريف مصفوفة inputs هنا
    {
        name: "email",
        placeholder: "Email",
        type: "email",
        required: true,
    },
    {
        name: "password",
        placeholder: "Password",
        type: "password",
        required: true,
    },
];

function SignInForm({
    handleHasAccount,
    hasAccount,
    handleChange,
    formData,
    setFormData,
}) {
    const [error, setError] = useState(null);

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            // 1. الحصول على CSRF token أولاً
            const csrfResponse = await fetch(
                "http://localhost:8000/sanctum/csrf-cookie",
                {
                    credentials: "include",
                }
            );

            // 2. إرسال بيانات تسجيل الدخول
            const response = await fetch("http://localhost:8000/api/login", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                credentials: "include",
                body: JSON.stringify({
                    email: formData.email,
                    password: formData.password,
                }),
            });

            if (response.status === 401) {
                const errorData = await response.json();
                throw new Error(errorData.error || "Invalid credentials");
            }

            if (response.status === 404) {
                throw new Error("API endpoint not found");
            }

            const data = await response.json();
            localStorage.setItem("token", data.token);
            window.location.reload();
        } catch (err) {
            console.error("Login error:", err);
            setError("Login failed. Please try again.");
        }
    };

    return (
        <div className="max-w-sm w-full text-gray-600 space-y-5">
            <LoginHeader>Log in to your account</LoginHeader>
            <form onSubmit={handleSubmit}>
                <InputForm
                    inputs={inputs}
                    onChange={handleChange}
                    values={formData}
                />
                {error && <div className="text-red-500">{error}</div>}
                <Button
                    margin="mt-14"
                    padding="py-2 px-3"
                    width="w-full"
                    type="submit"
                >
                    Sign in
                </Button>
            </form>
            <button className="w-full flex items-center justify-center gap-x-3 py-2.5 border text-sm font-medium bg-[#EEEEEE] hover:bg-gray-50 duration-150 active:bg-gray-100">
                {/* <!-- SVG for Google Sign In --> */}
                <img
                    src="https://raw.githubusercontent.com/sidiDev/remote-assets/7cd06bf1d8859c578c2efbfda2c68bd6bedc66d8/google-icon.svg"
                    alt="Google"
                    className="w-5 h-5"
                />
                {/* <!-- Comment: Google Icon SVG here --> */}
                Continue with Google
            </button>
            <SignUpInToggleBtn
                handleHasAccount={handleHasAccount}
                hasAccount={hasAccount}
            />
        </div>
    );
}

export default SignInForm;
