import axios, { AxiosInstance, InternalAxiosRequestConfig } from 'axios';
import Cookies from 'js-cookie';

const BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000';

const api: AxiosInstance = axios.create({
  baseURL: `${BASE_URL}/api`,
  headers: { 'Content-Type': 'application/json' },
});

api.interceptors.request.use((config: InternalAxiosRequestConfig) => {
  const token = Cookies.get('access_token');
  if (token && config.headers) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

let isRefreshing = false;
let failedQueue: { resolve: (v: string) => void; reject: (e: unknown) => void }[] = [];

function processQueue(error: unknown, token: string | null) {
  failedQueue.forEach((p) => (error ? p.reject(error) : p.resolve(token!)));
  failedQueue = [];
}

api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const original = error.config;
    if (error.response?.status === 401 && !original._retry) {
      if (isRefreshing) {
        return new Promise((resolve, reject) => {
          failedQueue.push({ resolve, reject });
        }).then((token) => {
          original.headers.Authorization = `Bearer ${token}`;
          return api(original);
        });
      }
      original._retry = true;
      isRefreshing = true;
      const refresh = Cookies.get('refresh_token');
      if (!refresh) {
        isRefreshing = false;
        Cookies.remove('access_token');
        Cookies.remove('refresh_token');
        if (typeof window !== 'undefined') window.location.href = '/login';
        return Promise.reject(error);
      }
      try {
        const res = await axios.post(`${BASE_URL}/api/auth/token/refresh/`, { refresh });
        const newAccess = res.data.access;
        Cookies.set('access_token', newAccess, { expires: 1 / 96 }); // 15 min
        processQueue(null, newAccess);
        original.headers.Authorization = `Bearer ${newAccess}`;
        return api(original);
      } catch (err) {
        processQueue(err, null);
        Cookies.remove('access_token');
        Cookies.remove('refresh_token');
        if (typeof window !== 'undefined') window.location.href = '/login';
        return Promise.reject(err);
      } finally {
        isRefreshing = false;
      }
    }
    return Promise.reject(error);
  }
);

export default api;

// ---- Auth ----
export const authAPI = {
  login: (email: string, password: string) =>
    api.post('/auth/login/', { email, password }),
  logout: (refresh: string) =>
    api.post('/auth/logout/', { refresh }),
  profile: () => api.get('/auth/profile/'),
};

// ---- Companies ----
export const companiesAPI = {
  list: () => api.get('/companies/'),
  get: (id: number) => api.get(`/companies/${id}/`),
  create: (data: FormData) =>
    api.post('/companies/', data, { headers: { 'Content-Type': 'multipart/form-data' } }),
  update: (id: number, data: FormData) =>
    api.patch(`/companies/${id}/`, data, { headers: { 'Content-Type': 'multipart/form-data' } }),
  delete: (id: number) => api.delete(`/companies/${id}/`),
  regenerateToken: (id: number) => api.post(`/companies/${id}/regenerate-token/`),
};

// ---- Company Values ----
export const valuesAPI = {
  list: (companyId: number) => api.get(`/companies/${companyId}/values/`),
  create: (companyId: number, data: object) =>
    api.post(`/companies/${companyId}/values/`, data),
  update: (companyId: number, valueId: number, data: object) =>
    api.put(`/companies/${companyId}/values/${valueId}/`, data),
  delete: (companyId: number, valueId: number) =>
    api.delete(`/companies/${companyId}/values/${valueId}/`),
  bulkUpdate: (companyId: number, values: object[]) =>
    api.put(`/companies/${companyId}/values/bulk/`, { values }),
};

// ---- Assessments ----
export const assessmentsAPI = {
  list: () => api.get('/assessments/'),
  get: (id: number) => api.get(`/assessments/${id}/`),
  create: (data: object) => api.post('/assessments/', data),
  update: (id: number, data: object) => api.patch(`/assessments/${id}/`, data),
  delete: (id: number) => api.delete(`/assessments/${id}/`),
  scorecard: (id: number) => api.get(`/assessments/${id}/scorecard/`),
  pdfReport: (id: number) =>
    api.get(`/assessments/${id}/report/pdf/`, { responseType: 'blob' }),
};

// ---- Dashboard ----
export const dashboardAPI = {
  summary: () => api.get('/dashboard/summary/'),
};

// ---- Survey (no auth) ----
export const surveyAPI = {
  fetch: (token: string) =>
    axios.get(`${BASE_URL}/api/survey/${token}/`),
  submit: (token: string, ratings: { company_value: number; score: number }[]) =>
    axios.post(`${BASE_URL}/api/survey/${token}/submit/`, { ratings }),
};

// ---- Users ----
export const usersAPI = {
  list: () => api.get('/auth/users/'),
  create: (data: object) => api.post('/auth/users/', data),
  update: (id: number, data: object) => api.patch(`/auth/users/${id}/`, data),
  delete: (id: number) => api.delete(`/auth/users/${id}/`),
  companyUsers: (companyId: number) => api.get(`/auth/companies/${companyId}/users/`),
};
