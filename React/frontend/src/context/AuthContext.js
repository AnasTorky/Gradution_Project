import { createContext, useContext, useState, useEffect } from 'react';
import api from '../api/axios';

const AuthContext = createContext();

const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const storedUser = localStorage.getItem('user');
    if (storedUser) {
      setUser(JSON.parse(storedUser));
    }
    setLoading(false);
  }, []);

  const register = async (userData) => {
    try {
      console.log('بنبعت ريكويست ريجيستر بالداتا دي:', userData); // ضيف ده
      const response = await api.post('/register', userData);
      console.log('الريسبونس من الريجيستر:', response.data); // ضيف ده
      const { user, token } = response.data;

      localStorage.setItem('token', token);
      localStorage.setItem('user', JSON.stringify(user));
      setUser(user);

      return { success: true, user, token };
    } catch (error) {
      console.error('إيرور في الريجيستر:', error.response?.data || error.message); // ضيف الميسيدج كاملة
      return {
        success: false,
        error: error.response?.data?.errors || { message: 'التسجيل فشل' }
      };
    }
  };

  const login = async (credentials) => {
    try {
      const response = await api.post('/login', credentials);
      const { user, token } = response.data;

      localStorage.setItem('token', token);
      localStorage.setItem('user', JSON.stringify(user));
      setUser(user);

      return { success: true, user, token };
    } catch (error) {
      return {
        success: false,
        error: error.response?.data?.errors || { message: 'Login failed' }
      };
    }
  };

  const logout = async () => {
    try {
      await api.post('/logout');
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      setUser(null);
      return { success: true };
    } catch (error) {
      return {
        success: false,
        error: error.response?.data?.errors || { message: 'Logout failed' }
      };
    }
  };

  return (
    <AuthContext.Provider
      value={{
        user,
        loading,
        register,
        login,
        logout
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

const useAuth = () => useContext(AuthContext);

export { AuthProvider, useAuth };
