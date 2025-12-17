import axios from 'axios';
import Swal from 'sweetalert2';
import { createRouter, createWebHistory } from 'vue-router';
import NotFound from '../pages/NotFound.vue';
import ResetPassword from '../pages/ResetPassword.vue';
import admin_dashboard from '../pages/admin/dashboard.vue';
import admin_profile from '../pages/admin/profile.vue';
import admin_task from '../pages/admin/task_page.vue';
import developer_add_task from '../pages/developer/add_task.vue';
import developer_dashboard from '../pages/developer/dashboard.vue';
import developer_edit_task from '../pages/developer/edit_task.vue';
import developer_profile from '../pages/developer/profile.vue';
import developer_task from '../pages/developer/task_page.vue';
import login from '../pages/login.vue';
import my_applications from '../pages/user/my_applications.vue';
import client_profile from '../pages/user/profile.vue';
import register from '../pages/user/register.vue';
import user_home from '../pages/user/user_home.vue';
import verifier_dashboard from '../pages/verifier/dashboard.vue';
import verifier_profile from '../pages/verifier/profile.vue';

const routes = [
    // CLIENT ROUTER
    {
        path: '/client/home',
        name: 'user_home',
        component: user_home,
        meta: { requiresAuth: true, role: 'client' },
    },
    {
        path: '/client/register',
        name: 'register',
        component: register,
        meta: { requiresAuth: true, role: 'client' },
    },
    {
        path: '/',
        name: 'login',
        component: login,
        meta: { hideNavbar: true },
    },
    {
        path: '/client/profile',
        name: 'client_profile',
        component: client_profile,
        meta: { requiresAuth: true, role: 'client' },
    },
    {
        path: '/client/my_applications',
        name: 'my_applications',
        component: my_applications,
        meta: { requiresAuth: true, role: 'client' },
    },

    // ADMIN ROUTER
    {
        path: '/admin/dashboard',
        name: 'admin_dashboard',
        component: admin_dashboard,
        meta: { requiresAuth: true, role: 'admin' },
    },

    {
        path: '/admin/task',
        name: 'admin_task',
        component: admin_task,
        meta: { requiresAuth: true, role: 'admin' },
    },

    {
        path: '/admin/profile',
        name: 'admin_profile',
        component: admin_profile,
        meta: { requiresAuth: true, role: 'admin' },
    },
    // VERIFIER ROUTER
    {
        path: '/verifier/dashboard',
        name: 'verifier_dashboard',
        component: verifier_dashboard,
        meta: { requiresAuth: true, role: 'verifier' },
    },

    {
        path: '/verifier/profile',
        name: 'verifier_profile',
        component: verifier_profile,
        meta: { requiresAuth: true, role: 'verifier' },
    },
    // DEVELOPER ROUTER
    {
        path: '/developer/dashboard',
        name: 'developer_dashboard',
        component: developer_dashboard,
        meta: { requiresAuth: true, role: 'developer' },
    },
    {
        path: '/developer/task',
        name: 'developer_task',
        component: developer_task,
        meta: { requiresAuth: true, role: 'developer' },
    },
    {
        path: '/developer/task/add_task',
        name: 'developer_add_task',
        component: developer_add_task,
        meta: { requiresAuth: true, role: 'developer' },
    },

    {
        path: '/developer/edit_task/:id',
        name: 'developer_edit_task',
        component: developer_edit_task,
        meta: { requiresAuth: true, role: 'developer' },
    },
    {
        path: '/developer/profile',
        name: 'developer_profile',
        component: developer_profile,
        meta: { requiresAuth: true, role: 'developer' },
    },

    // RESET PASSWORD
    {
        path: '/reset_password',
        name: 'reset_password',
        component: ResetPassword,
        meta: { hideNavbar: true },
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'NotFound',
        component: NotFound,
        meta: { hideNavbar: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Navigation guard to protect routes
router.beforeEach(async (to, from, next) => {
    const userStr = sessionStorage.getItem('user');
    const user = userStr ? JSON.parse(userStr) : null;

    if (to.meta.requiresAuth) {
        if (!user) {
            // Not logged in
            return next('/');
        }

        // Role-based restriction
        if (to.meta.role && user.role !== to.meta.role) {
            if (user.role === 'admin') return next('/admin/dashboard');
            if (user.role === 'verifier') return next('/verifier/dashboard');
            if (user.role === 'client') return next('/client/home');
            if (user.role === 'developer') return next('/developer/dashboard');
        }

        // Blocks client from accessing /client/register if there is an approved registration
        if (to.path === '/client/register' && user.role === 'client') {
            try {
                const res = await axios.get(`/api/myApplication/${user.id}`);
                const applications = res.data || [];

                const hasNotRejected = applications.some((app) => app.status?.toLowerCase() !== 'rejected');

                if (hasNotRejected) {
                    return next({ name: 'my_applications' });
                }
            } catch (err) {
                console.error('Error checking applications:', err);
            }
        }

        if (to.path.startsWith('/client/edit/') && user.role === 'client') {
            try {
                const applicationId = to.params.id;
                const res = await axios.get(`/api/registration/${applicationId}`);
                const application = res.data;

                const hasPending = application.status?.toLowerCase() === 'pending';
                const isRejected = application.status?.toLowerCase() === 'rejected';

                if (!hasPending || isRejected) {
                    return next('/client/home');
                }
            } catch (err) {
                console.error('Error checking application:', err);
                return next('/client/home');
            }
        }

        if (to.path.startsWith('/admin/edit/') && user.role === 'admin') {
            try {
                const applicationId = to.params.id;
                const res = await axios.get(`/api/registration/${applicationId}`);
                const application = res.data;

                // Check if this specific application is verified
                if (application.status?.toLowerCase() !== 'verified') {
                    Swal.fire({
                        title: 'Error!',
                        text: res?.data?.message || 'Application not yet verified.',
                        icon: 'error',
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: 'OK',
                    });
                    return next('/admin/dashboard');
                }
            } catch (err) {
                console.error('Error checking application:', err);
                return next('/admin/dashboard');
            }
        }

        if (to.path.startsWith('/verifier/update_status/') && user.role === 'verifier') {
            try {
                const applicationId = to.params.id;
                const res = await axios.get(`/api/registration/${applicationId}`);
                const application = res.data;

                // Check if this specific application is approved
                if (application.status?.toLowerCase() === 'approved') {
                    console.log('Application is already approved, redirecting...');
                    return next('/verifier/dashboard');
                }
            } catch (err) {
                console.error('Error checking application:', err);
                return next('/verifier/dashboard');
            }
        }

        next();
    } else {
        next();
    }
});

export default router;
