export const setAuthToken = (token) => {
  if (token) {
    localStorage.setItem('token', token);
  } else {
    localStorage.removeItem('token');
  }
};

export const getAuthUser = () => {
  const token = localStorage.getItem('token');
  if (!token) return null;

  // يمكنك إضافة فك التشفير هنا إذا كان الـ token يحتوي على بيانات المستخدم
  return { name: 'Admin User' }; // مثال
};
