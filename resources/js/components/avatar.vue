<script setup>
import axios from 'axios';
import { User } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const open = ref(false);
const avatarButton = ref(null);
const dropdownPosition = ref({ top: 0, left: 0 });
const router = useRouter();
const route = useRoute();
const currentUser = ref(null);

function toggleDropdown() {
    open.value = !open.value;
    if (open.value && avatarButton.value) {
        const rect = avatarButton.value.getBoundingClientRect();
        dropdownPosition.value = {
            top: rect.bottom + window.scrollY, // below the button
            left: rect.right - 176, // align right, 176px = dropdown width
        };
    }
}

// Get user from sessionStorage
const user = computed(() => {
    const userStr = sessionStorage.getItem('user');
    return userStr ? JSON.parse(userStr) : null;
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
            // Still clear session and redirect
            sessionStorage.removeItem('user');
            router.push('/');
        });
};

// ✅ Updated toProfile function with role-based navigation
const toProfile = () => {
    const currentUser = user.value;

    if (!currentUser) {
        router.push({ name: 'login' });
        return;
    }

    // Navigate based on user role
    switch (currentUser.role) {
        case 'admin':
            router.push({ name: 'admin_profile' });
            break;
        case 'verifier':
            router.push({ name: 'verifier_profile' });
            break;
        case 'developer':
            router.push({ name: 'developer_profile' });
            break;
        case 'client':
            router.push({ name: 'client_profile' });
            break;
        default:
            router.push({ name: 'profile' });
    }

    open.value = false;
};

// Close dropdown when clicking outside
onMounted(() => {
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.avatar-button') && !e.target.closest('.dropdown-teleport')) {
            open.value = false;
        }
    });
});
</script>

<template>
    <div class="inline-block">
        <!-- Avatar Button -->
        <div class="avatar-button mr-5 rounded-full border-2" ref="avatarButton">
            <button @click.stop="toggleDropdown" class="flex h-[40px] w-[40px] cursor-pointer items-center justify-center">
                <User :size="30" />
            </button>
        </div>

        <!-- Dropdown via Teleport -->
        <Teleport to="body">
            <div
                v-if="open"
                class="dropdown-teleport absolute z-[9999] rounded-md border border-gray-200 bg-white shadow-lg"
                :style="{ top: dropdownPosition.top + 'px', left: dropdownPosition.left + 'px', width: '176px' }"
            >
                <ul class="py-1 text-sm text-gray-700">
                    <!-- ✅ Show user info at top -->
                    <li v-if="user" class="border-b border-gray-200 px-4 py-2">
                        <p class="font-semibold text-gray-900">{{ user.first_name }} {{ user.last_name }}</p>
                        <p class="text-xs text-gray-500 capitalize">{{ user.role }}</p>
                    </li>

                    <li>
                        <button @click="toProfile" class="w-full cursor-pointer px-4 py-2 text-left hover:bg-gray-100">Profile</button>
                    </li>
                    <li>
                        <button @click="logout" class="w-full cursor-pointer px-4 py-2 text-left text-red-600 hover:bg-red-100">Logout</button>
                    </li>
                </ul>
            </div>
        </Teleport>
    </div>
</template>
