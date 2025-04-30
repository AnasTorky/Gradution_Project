import api from './axios';

export const register = async (userData) => {
    try {
      const response = await api.post('/register', userData);
      console.log('Server response:', response.data); // أضف هذا السطر
      return response.data;
    } catch (error) {
      console.error('Registration error:', error.response?.data);
      throw error.response?.data;
    }
  };

export const login = async (credentials) => {
  try {
    const response = await api.post('/login', credentials);
    return response.data;
  } catch (error) {
    throw error.response.data;
  }
};

export const logout = async () => {
  try {
    const response = await api.post('/logout');
    return response.data;
  } catch (error) {
    throw error.response.data;
  }
};
