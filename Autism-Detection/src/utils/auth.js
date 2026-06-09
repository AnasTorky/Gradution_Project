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

  return { name: 'Admin User' };
};
