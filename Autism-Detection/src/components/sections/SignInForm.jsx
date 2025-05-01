import React, { useState } from 'react';
import LoginHeader from '../common/LoginHeader';
import InputForm from '../common/InputForm';
import Button from '../common/Button';
import SignUpInToggleBtn from '../common/SignUpInToggleBtn';
import api from '../../api'; // استيراد api

const inputs = [ // تعريف مصفوفة inputs هنا
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
  setFormData
}) {
  const [error, setError] = useState(null);

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      // 1. الحصول على CSRF token أولاً
      const csrfResponse = await fetch('http://localhost:8000/sanctum/csrf-cookie', {
        credentials: 'include'
      });

      // 2. إرسال بيانات تسجيل الدخول
      const response = await fetch('http://localhost:8000/api/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'include',
        body: JSON.stringify({
          email: formData.email,
          password: formData.password
        })
      });

      if (response.status === 401) {
        const errorData = await response.json();
        throw new Error(errorData.error || 'Invalid credentials');
      }
      
      if (response.status === 404) {
        throw new Error('API endpoint not found');
      }

      const data = await response.json();
      localStorage.setItem('token', data.token);
      window.location.reload();
    } catch (err) {
      console.error('Login error:', err);
      setError(err.message || 'Login failed. Please try again.');
    }
  };

  return (
    <div className="max-w-sm w-full text-gray-600 space-y-5">
      <LoginHeader>Log in to your account</LoginHeader>
      <form onSubmit={handleSubmit}>
        <InputForm inputs={inputs} onChange={handleChange} values={formData} />
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
      <SignUpInToggleBtn
        handleHasAccount={handleHasAccount}
        hasAccount={hasAccount}
      />
    </div>
  );
}

export default SignInForm;
