import axios from '../bootstrap';

export function setAccessToken(token) {
    if (token) {
        localStorage.setItem('canteen_access_token', token);
        axios.defaults.headers.common.Authorization = `Bearer ${token}`;
        return;
    }

    localStorage.removeItem('canteen_access_token');
    delete axios.defaults.headers.common.Authorization;
}

export default axios;
