import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  withCredentials: true,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
});

// Interceptor لإدارة CSRF Token
api.interceptors.request.use(async (config) => {
  if (!document.cookie.match(/XSRF-TOKEN=[^;]+/)) {
    await axios.get('http://localhost:8000/sanctum/csrf-cookie', {
      withCredentials: true
    });
  }

  const csrfToken = document.cookie.replace(
    /(?:(?:^|.*;\s*)XSRF-TOKEN\s*\=\s*([^;]*).*$)|^.*$/,
    '$1'
  );

  if (csrfToken) {
    config.headers['X-XSRF-TOKEN'] = csrfToken;
  }

  return config;
});

const apiMethods = {
  // المستخدمون
  getUsers() {
    return api.get('/users');
  },
  updateUserRole(id, role) {
    return api.put(`/users/${id}/role`, { role });
  },
  deleteUser(id) {
    return api.delete(`/users/${id}`);
  },

  // الفئات
  getCategories() {
    return api.get('/categories');
  },
  createCategory(data) {
    return api.post('/categories', data);
  },
  updateCategory(id, data) {
    return api.put(`/categories/${id}`, data);
  },
  deleteCategory(id) {
    return api.delete(`/categories/${id}`);
  },

  // الأنشطة
  getActivities() {
    return api.get('/activities');
  },
  createActivity(data) {
    return api.post('/activities', data);
  },
  updateActivity(id, data) {
    return api.put(`/activities/${id}`, data);
  },
  deleteActivity(id) {
    return api.delete(`/activities/${id}`);
  },
};

export default apiMethods;
