import axios from 'axios';

// create axios instance with base URL and default headers
const api = axios.create({
    baseURL: '/api',
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

export default api;