<script setup>
import axios from 'axios';
import { LayoutDashboard, ListTodo, PanelRightClose, UserRoundPen } from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Avatar from '../avatar.vue';

const router = useRouter();

// Get user from sessionStorage
const user = computed(() => {
    const userStr = sessionStorage.getItem('user');
    return userStr ? JSON.parse(userStr) : null;
});

// Dynamic route names based on role
const dashboardRoute = computed(() => {
    const role = user.value?.role;
    if (role === 'admin') return 'admin_dashboard';
    if (role === 'developer') return 'developer_dashboard';
    if (role === 'client') return 'client_dashboard';
    return 'login';
});

const taskRoute = computed(() => {
    const role = user.value?.role;
    if (role === 'admin') return 'admin_task';
    if (role === 'developer') return 'developer_task';
    if (role === 'client') return 'client_task';
    return 'login';
});

const profileRoute = computed(() => {
    const role = user.value?.role;
    if (role === 'admin') return 'admin_profile';
    if (role === 'developer') return 'developer_profile';
    if (role === 'client') return 'client_profile';
    return 'login';
});

const roleLabel = computed(() => {
    const role = user.value?.role;
    return role ? role.charAt(0).toUpperCase() + role.slice(1) : 'User';
});

const taskLabel = computed(() => {
    return user.value?.role === 'developer' ? 'My Tasks' : 'Tasks';
});

const logout = () => {
    axios
        .post('/api/logout')
        .then(() => {
            sessionStorage.removeItem('user');
            router.push('/');
        })
        .catch((err) => {
            console.error('Logout failed:', err);
            sessionStorage.removeItem('user');
            router.push('/');
        });
};

const handleResize = () => {
    if (window.innerWidth >= 768) {
        // Handle responsive behavior
    }
};

onMounted(() => {
    window.addEventListener('resize', handleResize);
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', handleResize);
});
</script>

<template>
    <div class="drawer lg:drawer-open">
        <input id="my-drawer-4" type="checkbox" class="drawer-toggle" />
        <div class="drawer-content flex h-screen flex-col overflow-hidden">
            <!-- Navbar -->
            <nav class="navbar flex w-full flex-shrink-0 justify-between bg-base-300">
                <div class="flex items-center">
                    <label for="my-drawer-4" aria-label="open sidebar" class="btn btn-square btn-ghost">
                        <PanelRightClose />
                    </label>
                    <div class="px-4 text-[20px] font-semibold">Welcome, {{ user?.first_name || roleLabel }}</div>
                </div>

                <div><Avatar /></div>
            </nav>

            <!-- Page content here -->
            <div class="flex-1 overflow-y-auto p-4">
                <slot />
            </div>
        </div>

        <div class="drawer-side is-drawer-close:overflow-visible">
            <label for="my-drawer-4" aria-label="close sidebar" class="drawer-overlay"></label>
            <div class="flex min-h-full flex-col items-start bg-base-200 is-drawer-close:w-14 is-drawer-open:w-64">
                <!-- Dynamic Sidebar -->
                <ul class="menu w-full grow">
                    <li>
                        <RouterLink :to="{ name: dashboardRoute }" class="w-full">
                            <button
                                class="flex cursor-pointer items-center gap-2 is-drawer-close:tooltip is-drawer-close:tooltip-right"
                                data-tip="Dashboard"
                            >
                                <LayoutDashboard />
                                <span class="is-drawer-close:hidden">Dashboard</span>
                            </button>
                        </RouterLink>
                    </li>

                    <li>
                        <RouterLink :to="{ name: taskRoute }" class="w-full">
                            <button
                                class="flex cursor-pointer items-center gap-2 is-drawer-close:tooltip is-drawer-close:tooltip-right"
                                :data-tip="taskLabel"
                            >
                                <ListTodo />
                                <span class="is-drawer-close:hidden">{{ taskLabel }}</span>
                            </button>
                        </RouterLink>
                    </li>

                    <li>
                        <RouterLink :to="{ name: profileRoute }" class="w-full">
                            <button
                                class="flex cursor-pointer items-center gap-2 is-drawer-close:tooltip is-drawer-close:tooltip-right"
                                data-tip="Profile"
                            >
                                <UserRoundPen />
                                <span class="is-drawer-close:hidden">Profile</span>
                            </button>
                        </RouterLink>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
