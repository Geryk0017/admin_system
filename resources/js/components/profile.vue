<script setup>
import SubmitButton from '@/components/submitButton.vue';
import axios from 'axios';
import { Eye, EyeOff } from 'lucide-vue-next';
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const currentUser = ref(null);
const loading = ref(true);
const showOldPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

const profileForm = ref({
    first_name: '',
    middle_name: '',
    last_name: '',
    email: '',
});

const passwordForm = ref({
    email: '',
    old_password: '',
    new_password: '',
    retype_password: '',
});

const errors = ref({});

onMounted(() => {
    // Get logged-in user from sessionStorage
    const storedUser = sessionStorage.getItem('user');
    if (storedUser) {
        currentUser.value = JSON.parse(storedUser);

        // Populate profile form with user data
        profileForm.value = {
            first_name: currentUser.value.first_name || '',
            middle_name: currentUser.value.middle_name || '',
            last_name: currentUser.value.last_name || '',
            email: currentUser.value.email || '',
        };

        // Set email for password form
        passwordForm.value.email = currentUser.value.email || '';

        loading.value = false;
    } else {
        Swal.fire({
            title: 'Error!',
            text: 'Please log in first.',
            icon: 'error',
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'OK',
        }).then(() => {
            router.push({ name: 'login' });
        });
    }
});

const updateProfile = async (e) => {
    e.preventDefault();
    errors.value = {};

    try {
        const response = await axios.put(`/api/users/${currentUser.value.id}`, profileForm.value);

        // Update sessionStorage with new data
        const updatedUser = { ...currentUser.value, ...profileForm.value };
        sessionStorage.setItem('user', JSON.stringify(updatedUser));
        currentUser.value = updatedUser;

        Swal.fire({
            title: 'Success!',
            text: response.data.message || 'Profile updated successfully',
            icon: 'success',
            confirmButtonColor: '#10b981',
            confirmButtonText: 'OK',
        });
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors;

            Swal.fire({
                title: 'Validation Error',
                text: 'Please check the form for errors.',
                icon: 'error',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'OK',
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: err.response?.data?.message || 'Profile update failed.',
                icon: 'error',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'OK',
            });
        }
    }
};

const resetPassword = async (e) => {
    e.preventDefault();
    errors.value = {};

    // Validate passwords match
    if (passwordForm.value.new_password !== passwordForm.value.retype_password) {
        Swal.fire({
            title: 'Error!',
            text: 'New passwords do not match.',
            icon: 'error',
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'OK',
        });
        return;
    }

    try {
        const response = await axios.post('/api/reset-password', passwordForm.value);

        Swal.fire({
            title: 'Success!',
            text: response.data.message || 'Password reset successfully',
            icon: 'success',
            confirmButtonColor: '#10b981',
            confirmButtonText: 'OK',
        });

        // Clear password fields
        passwordForm.value.old_password = '';
        passwordForm.value.new_password = '';
        passwordForm.value.retype_password = '';
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors;

            Swal.fire({
                title: 'Validation Error',
                text: 'Please check the form for errors.',
                icon: 'error',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'OK',
            });
        } else if (err.response?.status === 401) {
            Swal.fire({
                title: 'Error!',
                text: 'Old password is incorrect.',
                icon: 'error',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'OK',
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: err.response?.data?.error || err.response?.data?.message || 'Password reset failed.',
                icon: 'error',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'OK',
            });
        }
    }
};
</script>

<template>
    <div class="mx-auto max-w-4xl px-5 py-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">My Profile</h1>
            <p class="mt-2 text-gray-600">
                Manage your account information here. You can update your email address or change your password at any time.
            </p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="rounded-lg bg-white p-8 text-center shadow-lg">
            <p class="text-gray-500">Loading profile...</p>
        </div>

        <div v-else>
            <!-- Profile Information Form -->
            <form @submit="updateProfile" class="mb-6 rounded-lg bg-white p-8 shadow-lg">
                <h2 class="mb-6 text-xl font-semibold text-gray-800">Profile Information</h2>

                <div class="gap-5 md:grid md:grid-cols-2">
                    <div class="col-span-2">
                        <label for="first_name" class="mb-2 block text-sm font-semibold text-gray-700">First Name</label>
                        <input
                            type="text"
                            class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                            :class="errors.first_name ? 'border-red-500' : 'border-primary'"
                            name="first_name"
                            v-model="profileForm.first_name"
                        />
                        <p v-if="errors.first_name" class="mt-1 text-sm text-red-500">{{ errors.first_name[0] }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label for="middle_name" class="mb-2 block text-sm font-semibold text-gray-700">Middle Name</label>
                        <input
                            type="text"
                            class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                            :class="errors.middle_name ? 'border-red-500' : 'border-primary'"
                            name="middle_name"
                            v-model="profileForm.middle_name"
                        />
                        <p v-if="errors.middle_name" class="mt-1 text-sm text-red-500">{{ errors.middle_name[0] }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label for="last_name" class="mb-2 block text-sm font-semibold text-gray-700">Last Name</label>
                        <input
                            type="text"
                            class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                            :class="errors.last_name ? 'border-red-500' : 'border-primary'"
                            name="last_name"
                            v-model="profileForm.last_name"
                        />
                        <p v-if="errors.last_name" class="mt-1 text-sm text-red-500">{{ errors.last_name[0] }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label for="email" class="mb-2 block text-sm font-semibold text-gray-700">Email</label>
                        <input
                            type="email"
                            class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                            :class="errors.email ? 'border-red-500' : 'border-primary'"
                            name="email"
                            v-model="profileForm.email"
                        />
                        <p v-if="errors.email" class="mt-1 text-sm text-red-500">{{ errors.email[0] }}</p>
                    </div>
                </div>

                <div class="mt-10 flex justify-center">
                    <SubmitButton bt="Update Profile" class="w-[200px] rounded-md" />
                </div>
            </form>

            <!-- Password Reset Form -->
            <form @submit="resetPassword" class="rounded-lg bg-white p-8 shadow-lg">
                <h2 class="mb-6 text-xl font-semibold text-gray-800">Change Password</h2>

                <div class="gap-5 md:grid md:grid-cols-1">
                    <div class="relative">
                        <label for="old_password" class="mb-2 block text-sm font-semibold text-gray-700">Old Password</label>
                        <input
                            :type="showOldPassword ? 'text' : 'password'"
                            class="h-[50px] w-full border-0 border-b focus:border-2 focus:ring-0"
                            :class="errors.old_password ? 'border-red-500' : 'border-primary'"
                            v-model="passwordForm.old_password"
                            required
                        />
                        <span class="absolute top-[42px] right-2 cursor-pointer text-gray-500" @click="showOldPassword = !showOldPassword">
                            <Eye v-if="showOldPassword" class="h-5 w-5" />
                            <EyeOff v-else class="h-5 w-5" />
                        </span>
                        <p v-if="errors.old_password" class="mt-1 text-sm text-red-500">{{ errors.old_password[0] }}</p>
                    </div>

                    <div class="relative">
                        <label for="new_password" class="mb-2 block text-sm font-semibold text-gray-700">New Password</label>
                        <input
                            :type="showNewPassword ? 'text' : 'password'"
                            class="h-[50px] w-full border-0 border-b focus:border-2 focus:ring-0"
                            :class="errors.new_password ? 'border-red-500' : 'border-primary'"
                            v-model="passwordForm.new_password"
                            required
                        />
                        <span class="absolute top-[42px] right-2 cursor-pointer text-gray-500" @click="showNewPassword = !showNewPassword">
                            <Eye v-if="showNewPassword" class="h-5 w-5" />
                            <EyeOff v-else class="h-5 w-5" />
                        </span>
                        <p v-if="errors.new_password" class="mt-1 text-sm text-red-500">{{ errors.new_password[0] }}</p>
                    </div>

                    <div class="relative">
                        <label for="retype_password" class="mb-2 block text-sm font-semibold text-gray-700">Confirm New Password</label>
                        <input
                            :type="showConfirmPassword ? 'text' : 'password'"
                            class="h-[50px] w-full border-0 border-b focus:border-2 focus:ring-0"
                            :class="errors.retype_password ? 'border-red-500' : 'border-primary'"
                            v-model="passwordForm.retype_password"
                            required
                        />
                        <span class="absolute top-[42px] right-2 cursor-pointer text-gray-500" @click="showConfirmPassword = !showConfirmPassword">
                            <Eye v-if="showConfirmPassword" class="h-5 w-5" />
                            <EyeOff v-else class="h-5 w-5" />
                        </span>
                        <p v-if="errors.retype_password" class="mt-1 text-sm text-red-500">{{ errors.retype_password[0] }}</p>
                    </div>
                </div>

                <div class="mt-10 flex justify-center">
                    <SubmitButton bt="Change Password" class="w-[200px] rounded-md" />
                </div>
            </form>
        </div>
    </div>
</template>
