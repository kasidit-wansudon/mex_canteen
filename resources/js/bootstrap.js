import axios from 'axios';

const token = localStorage.getItem('canteen_access_token');

window.axios = axios.create({
    baseURL: '/api',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        Accept: 'application/json',
    },
});

if (token) {
    window.axios.defaults.headers.common.Authorization = `Bearer ${token}`;
}

export default window.axios;
