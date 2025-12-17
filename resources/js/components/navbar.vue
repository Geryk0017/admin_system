<script setup>
import axios from 'axios';
import { FileUser, House, NotebookPen, PanelRightClose, UserRoundPen } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import Avatar from './avatar.vue';

const router = useRouter();
const menuOpen = ref(false);
const applications = ref([]);
const loading = ref(false);
const selectedStatus = ref('all');

// Get user from sessionStorage
const user = computed(() => {
    const userStr = sessionStorage.getItem('user');
    return userStr ? JSON.parse(userStr) : null;
});

const toggleMenu = () => {
    menuOpen.value = !menuOpen.value;
};

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

// Fetch user's applications
const fetchApplications = () => {
    loading.value = true;

    const storedUser = JSON.parse(sessionStorage.getItem('user'));
    const userID = storedUser?.id;

    if (!userID) {
        console.error('No user found in sessionStorage');
        loading.value = false;
        return;
    }

    axios
        .get(`/api/myApplication/${userID}`)
        .then((res) => {
            applications.value = res.data;
            loading.value = false;
        })
        .catch((err) => {
            console.error('Error fetching applications:', err);
            loading.value = false;
        });
};

// Hide registration link if there's an approved registration
const hasApprovedRegistration = computed(() => {
    if (!applications.value.length) return true;

    if (applications.value.status?.toLowerCase() === 'pending') {
        return false;
    }

    return applications.value.every((app) => app.status?.toLowerCase() === 'rejected');
});

onMounted(() => {
    fetchApplications();
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
                        <!-- Sidebar toggle icon -->
                        <PanelRightClose />
                    </label>
                    <div class="px-4 text-[20px] font-semibold">Welcome, {{ user?.first_name || 'Client' }}</div>
                </div>

                <div><Avatar /></div>
            </nav>

            <!-- Page content here - Router View Goes Here -->
            <div class="flex-1 overflow-y-auto p-4">
                <slot />
            </div>
        </div>

        <div class="drawer-side is-drawer-close:overflow-visible">
            <label for="my-drawer-4" aria-label="close sidebar" class="drawer-overlay"></label>
            <div class="flex min-h-full flex-col items-start bg-base-200 is-drawer-close:w-14 is-drawer-open:w-64">
                <!-- Sidebar content here -->
                <ul class="menu w-full grow">
                    <li>
                        <RouterLink :to="{ name: 'user_home' }" class="w-full">
                            <button
                                class="flex cursor-pointer items-center gap-2 is-drawer-close:tooltip is-drawer-close:tooltip-right"
                                data-tip="Homepage"
                            >
                                <House />
                                <span class="is-drawer-close:hidden">Homepage</span>
                            </button>
                        </RouterLink>
                    </li>

                    <li v-if="hasApprovedRegistration">
                        <RouterLink :to="{ name: 'register' }" class="w-full">
                            <button
                                class="flex cursor-pointer items-center gap-2 is-drawer-close:tooltip is-drawer-close:tooltip-right"
                                data-tip="Register"
                            >
                                <NotebookPen />
                                <span class="is-drawer-close:hidden">Register</span>
                            </button>
                        </RouterLink>
                    </li>

                    <li>
                        <RouterLink :to="{ name: 'my_applications' }" class="w-full">
                            <button
                                class="flex cursor-pointer items-center gap-2 is-drawer-close:tooltip is-drawer-close:tooltip-right"
                                data-tip="My Applications"
                            >
                                <FileUser />
                                <span class="is-drawer-close:hidden">My Applications</span>
                            </button>
                        </RouterLink>
                    </li>

                    <li>
                        <RouterLink :to="{ name: 'client_profile' }" class="w-full">
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
