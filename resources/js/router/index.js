import { createRouter, createWebHistory } from 'vue-router';

import MealSelectionPage from '../pages/user/MealSelectionPage.vue';
import ReservationPage from '../pages/user/ReservationPage.vue';
import HistoryPage from '../pages/user/HistoryPage.vue';

import AdminMenuPage from '../pages/admin/AdminMenuPage.vue';
import AdminPage from '../pages/admin/AdminPage.vue';
import AdminScannerPage from '../pages/admin/AdminScannerPage.vue';
import AdminDashboardPage from '../pages/admin/AdminDashboardPage.vue';
import AdminUsersPage from '../pages/admin/AdminUsersPage.vue';
import AdminReportsPage from '../pages/admin/AdminReportsPage.vue';
import AdminScanPage from '../pages/admin/AdminScanPage.vue';

const routes = [
    { path: '/', component: MealSelectionPage, meta: { requiresAuth: true } },
    { path: '/reservation', component: ReservationPage, meta: { requiresAuth: true } },
    { path: '/history', component: HistoryPage, meta: { requiresAuth: true } },
    { path: '/admin', component: MealSelectionPage },
    // { path: '/admin', component: AdminPage },
    { path: '/admin/scanner', component: AdminScanPage},
    { path: '/admin/menu', component: AdminMenuPage, meta: { requiresAdmin: true } },
    { path: '/admin/scan', component: AdminScannerPage, meta: { requiresAdmin: true } },
    { path: '/admin/dashboard', component: AdminDashboardPage, meta: { requiresAdmin: true } },
    { path: '/admin/users', component: AdminUsersPage, meta: { requiresAdmin: true } },
    { path: '/admin/reports', component: AdminReportsPage, meta: { requiresAdmin: true } },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const auth = JSON.parse(localStorage.getItem('canteen_auth') || '{}');

    if (to.meta.requiresAuth && !auth.token) {
        next('/admin');
        return;
    }

    if (to.meta.requiresAdmin && (!auth.token || auth.role !== 'admin')) {
        next('/admin');
        return;
    }

    if (to.path === '/admin' && auth.token && auth.role === 'admin') {
        next('/admin/dashboard');
        return;
    }

    next();
});

export default router;
