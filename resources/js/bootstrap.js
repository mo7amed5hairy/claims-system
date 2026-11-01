import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF Token refresh and 419 handling
let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}

// Handle 419 errors - refresh token and reload page instead of redirecting to login
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 419) {
            // Don't redirect to login on 419, instead refresh the page
            // This prevents users from being logged out due to CSRF token mismatch
            if (!window.location.pathname.includes('/login')) {
                // Refresh the CSRF token
                axios.get('/sanctum/csrf-cookie').then(() => {
                    // Reload the page after getting new token
                    window.location.reload();
                }).catch(() => {
                    // If refresh fails, just reload
                    window.location.reload();
                });
            }
        }
        return Promise.reject(error);
    }
);
