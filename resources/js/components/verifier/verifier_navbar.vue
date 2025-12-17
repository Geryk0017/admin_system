<script setup>
import axios from 'axios';
import { LayoutDashboard, PanelRightClose, UserRoundPen } from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import Avatar from '../avatar.vue';

const router = useRouter();
const menuOpen = ref(false);

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
            // Still clear session and redirect
            sessionStorage.removeItem('user');
            router.push('/');
        });
};

// Auto-close menu when screen width >= md (768px)
const handleResize = () => {
    if (window.innerWidth >= 768) {
        menuOpen.value = false;
    }
};

onMounted(() => {
    window.addEventListener('resize', handleResize);
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', handleResize);
});

const props = defineProps({
    title: {
        type: String,
        default: 'Admin',
    },
    dashboardLink: {
        type: String,
        required: true,
    },
    taskLink: {
        type: String,
        required: true,
    },
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
                    <div class="px-4">VERIFIER - Welcome, {{ user?.first_name || 'Client' }}</div>
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
                        <RouterLink :to="{ name: 'verifier_dashboard' }" class="w-full">
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
                        <RouterLink :to="{ name: 'verifier_profile' }" class="w-full">
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

<!-- <template>
    <nav>
        <div class="w-full h-[100px] bg-white drop-shadow-sm flex justify-between items-center">
            <div class="mx-5 h-[50px] flex items-center space-x-3">
                <img :src="logo" class="w-[50px] h-[50px]" />
                <h1 class="text-[15px] font-semibold">{{ props.title }} - {{ user?.fullname || 'User' }}</h1>
            </div>

            <div class="mx-3 flex justify-center items-center">
                
                <ul class="mx-5 hidden md:flex gap-5 ">
                    <div class="flex items-center gap-3">
                        <House/>
                        <li> <RouterLink :to="dashboardLink"> Dashboard  </RouterLink> </li>
                    </div>

                    <div class="flex items-center gap-3">
                        <Avatar />
                    </div>
                </ul>
                <button @click="toggleMenu"> <Menu class="w-[30px] h-[30px] md:hidden"/>  </button>
            </div>
        </div>

        <Teleport to="body">
            <div v-if="menuOpen" class="absolute top-[100px] w-screen bg-white flex justify-center">
                <ul class="mx-5 md:hidden space-y-5  p-5">
                    <div class="flex items-center gap-3 w-screen p-5 border-b border-fields">
                        <LayoutDashboard/>
                        <li> <RouterLink :to="dashboardLink" @click="menuOpen = false"> Dashboard  </RouterLink> </li>
                    </div>

                    <div class="flex items-center gap-3 w-screen p-5 border-b border-fields">
                        <LogOut/>
                        <li>  <button @click="logout" class="hover:text-red-600 transition cursor-pointer">Logout</button> </li>
                    </div>
                </ul>
            </div>
        </Teleport>
    </nav>
</template> -->
