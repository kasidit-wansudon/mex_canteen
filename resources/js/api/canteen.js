import http from './http';

export function login(payload) {
    return http.post('/auth/login', payload);
}

export function logout() {
    return http.post('/auth/logout');
}

export function getProfile() {
    return http.get('/user/profile');
}

export function getDailyMealPlans(date) {
    return http.get('/meal-plans/daily', { params: { date } });
}

export function getReservations(params = {}) {
    return http.get('/reservations', { params });
}

export function getReservation(id) {
    return http.get(`/reservations/${id}`);
}

export function createReservation(payload) {
    return http.post('/reservations', payload);
}

export function updateReservation(id, payload) {
    return http.put(`/reservations/${id}`, payload);
}

export function deleteReservation(id) {
    return http.delete(`/reservations/${id}`);
}

export function getReservationQr(id) {
    return http.get(`/reservations/${id}/qr`);
}

export function adminSaveMealPlan(payload) {
    return http.post('/admin/meal-plans', payload);
}

export function adminValidateQr(payload) {
    return http.post('/admin/qr-validate', payload);
}

export function adminDashboardStats(params = {}) {
    return http.get('/admin/dashboard/stats', { params });
}

export function adminDailyReport(params = {}) {
    return http.get('/admin/reports/daily', { params });
}

export function adminMonthlyReport(params = {}) {
    return http.get('/admin/reports/monthly', { params });
}

export function adminWeeklyGrid(params = {}) {
    return http.get('/admin/reports/weekly-grid', { params });
}

export function adminUsers(params = {}) {
    return http.get('/admin/users', { params });
}

export function adminUpdateUserStatus(id, payload) {
    return http.patch(`/admin/users/${id}/status`, payload);
}

export function adminCreateVisitor(payload) {
    return http.post('/admin/users/visitor', payload);
}
