<script setup>
import axios from 'axios';
import Swal from 'sweetalert2';
import { ref } from 'vue';
import SubmitButton from '../submitButton.vue';

const message = ref('');
const errors = ref({});
const showModal = ref(false);
let messageTimeout = null;

const emit = defineEmits(['close', 'success']);

const closeMessage = () => {
    message.value = '';
    if (messageTimeout) {
        clearTimeout(messageTimeout);
    }
};

const form = ref({
    first_name: '',
    middle_name: '',
    last_name: '',
    email: '',
});

const resetForm = () => {
    form.value = {
        first_name: '',
        middle_name: '',
        last_name: '',
        email: '',
    };
    errors.value = {};
    message.value = '';
};

const register = () => {
    errors.value = {};
    message.value = '';

    if (messageTimeout) {
        clearTimeout(messageTimeout);
    }

    axios
        .post('/api/developer/signup', form.value)
        .then((res) => {
            emit('close');
            Swal.fire({
                title: 'Developer Created!',
                html: `Account created successfully.<br>Password: <b>${res.data.generated_password}</b>`,
                icon: 'success',
                confirmButtonColor: '#10b981',
            }).then(() => {
                resetForm();
                emit('success');
                emit('close');
            });
        })

        .catch((err) => {
            if (err.response?.status === 422) {
                errors.value = err.response.data.errors;
            } else {
                message.value = err.response?.data?.message || 'Registration failed.';
            }
        });
};
</script>

<template>
    <div class="mx-auto max-w-4xl rounded-lg">
        <div class="mb-6">
            <form @submit.prevent="register" class="mb-6">
                <h2 class="mb-6 text-xl font-semibold text-gray-800">Profile Information</h2>

                <div class="gap-5 md:grid md:grid-cols-2">
                    <div class="col-span-2">
                        <label for="first_name" class="mb-2 block text-sm font-semibold text-gray-700">First Name</label>
                        <input
                            type="text"
                            class="h-[50px] w-full border-0 border-b transition focus:outline-none"
                            :class="errors.first_name ? 'border-red-500' : 'border-primary'"
                            v-model="form.first_name"
                            name="first_name"
                        />
                        <p v-if="errors.first_name" class="mt-1 text-sm text-red-500">{{ errors.first_name[0] }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label for="middle_name" class="mb-2 block text-sm font-semibold text-gray-700">Middle Name (Optional)</label>
                        <input
                            type="text"
                            class="h-[50px] w-full border-0 border-b transition focus:outline-none"
                            :class="errors.middle_name ? 'border-red-500' : 'border-primary'"
                            v-model="form.middle_name"
                            name="mname"
                        />
                        <p v-if="errors.middle_name" class="mt-1 text-sm text-red-500">{{ errors.middle_name[0] }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label for="last_name" class="mb-2 block text-sm font-semibold text-gray-700">Last Name</label>
                        <input
                            type="text"
                            class="h-[50px] w-full border-0 border-b transition focus:outline-none"
                            :class="errors.last_name ? 'border-red-500' : 'border-primary'"
                            v-model="form.last_name"
                            name="last_name"
                        />
                        <p v-if="errors.last_name" class="mt-1 text-sm text-red-500">{{ errors.last_name[0] }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label for="email" class="mb-2 block text-sm font-semibold text-gray-700">Email</label>
                        <input
                            type="email"
                            class="h-[50px] w-full border-0 border-b transition focus:outline-none"
                            :class="errors.last_name ? 'border-red-500' : 'border-primary'"
                            v-model="form.email"
                            name="developer"
                        />
                        <p v-if="errors.email" class="mt-1 text-sm text-red-500">{{ errors.email[0] }}</p>
                    </div>
                </div>
                <div class="mt-10 flex justify-center gap-4">
                    <SubmitButton bt="Create Account" class="w-[200px] rounded-md shadow-md" />
                </div>
            </form>
        </div>
    </div>
</template>

<!-- <div class="relative my-5">
                        <label for="password" class="mb-2 block text-sm font-semibold text-gray-700">Password</label>
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            class="h-[50px] w-full border-0 border-b transition focus:border-2 focus:ring-0"
                            :class="errors.password ? 'border-red-500' : 'border-primary'"
                            v-model="form.password"
                        />
                        <p v-if="errors.password" class="mt-1 text-sm text-red-500">{{ errors.password[0] }}</p>
                        <span
                            class="absolute top-[42px] right-2 cursor-pointer text-gray-500 hover:text-gray-700"
                            @click="showPassword = !showPassword"
                        >
                            <Eye v-if="showPassword" class="h-5 w-5" />
                            <EyeOff v-else class="h-5 w-5" />
                        </span>
                    </div>

                    <div class="relative my-5">
                        <label for="retype_password" class="mb-2 block text-sm font-semibold text-gray-700">Confirm Password</label>
                        <input
                            :type="showConfirmPassword ? 'text' : 'password'"
                            :class="errors.retype_password ? 'border-red-500' : 'border-primary'"
                            class="h-[50px] w-full border-0 border-b transition focus:border-2 focus:ring-0"
                            v-model="form.retype_password"
                        />
                        <span
                            class="absolute top-[42px] right-2 cursor-pointer text-gray-500 hover:text-gray-700"
                            @click="showConfirmPassword = !showConfirmPassword"
                        >
                            <Eye v-if="showConfirmPassword" class="h-5 w-5" />
                            <EyeOff v-else class="h-5 w-5" />
                        </span>
                        <p v-if="errors.retype_password" class="mt-1 text-sm text-red-500">{{ errors.retype_password[0] }}</p>
                    </div> -->
