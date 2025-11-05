
import axios from 'axios';


const axiosInstance = axios.create({
    baseURL: 'http://127.0.0.1:8000/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

axiosInstance.interceptors.request.use(
    (config) => {
        // Obtiene el token de seguridad almacenado (lo guardamos en localStorage en el Login)
        const token = localStorage.getItem('authToken');

        if (token) {
            // CRÍTICO: Añade el token al encabezado de Autorización
            // Este es el formato que Laravel Sanctum espera
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

export default axiosInstance;