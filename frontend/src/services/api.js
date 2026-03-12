import axios from 'axios';

// create axios instance with base URL and default headers
const api = axios.create({
    baseURL: '/api',
    timeout: 8000,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
});

// securiity interceptor

api.interceptors.request.use(config => {
    const token = localStorage.getItem('lm_token');
    
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    
    return config;
});

api.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            localStorage.removeItem('lm_token');
            localStorage.removeItem('lm_user');
        }

        return Promise.reject(error);
    }
);

export default api;