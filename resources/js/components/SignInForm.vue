<script setup>
import axios from 'axios';
import { Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';

const emit = defineEmits(['switchToSignUp']);
const router = useRouter();
const showPassword = ref(false);

const form = ref({
    email: '',
    password: '',
});

const message = ref('');
const errors = ref({});

const login = () => {
    errors.value = {};
    message.value = '';

    axios
        .post('/api/login', form.value)
        .then((res) => {
            const user = res.data.user;
            const hasApplication = res.data.has_application;
            const applicationStatus = res.data.application_status;

            // Store user data in sessionStorage
            sessionStorage.setItem('user', JSON.stringify(user));

            // Redirect based on role
            if (user.role === 'admin') {
                router.push('/admin/dashboard');
            } else if (user.role === 'verifier') {
                router.push('/verifier/dashboard');
            } else if (user.role === 'client') {
                if (!hasApplication) {
                    // No application exists
                    router.push('/client/register');
                } else if (applicationStatus === 'approved') {
                    router.push('/client/home');
                } else {
                    // Has application but not approved
                    router.push('/client/my_applications');
                }
            } else if (user.role === 'developer') {
                router.push('/developer/dashboard');
            }
        })
        .catch((err) => {
            if (err.response?.status === 422) {
                errors.value = err.response.data.errors;
            } else if (err.response?.status === 403) {
                message.value = err.response.data.error || 'Your account is pending approval.';
            } else if (err.response?.status === 401) {
                message.value = 'Invalid email or password';
            } else {
                message.value = err.response?.data?.message || 'Login failed.';
            }
        });
};
</script>

<template>
    <div class="flex h-screen items-center justify-center bg-white">
        <div class="w-[75%]">
            <div>
                <!-- HEADER -->
                <div class="flex h-[100px] justify-center px-5 leading-[100px]">
                    <header>
                        <h1 class="text-[25px]">Sign In</h1>
                    </header>
                </div>
                <!-- LOGIN FORM -->
                <div class="m-5 my-[10%]">
                    <!-- Error Message -->
                    <div v-if="message" class="mx-5 mb-5 rounded bg-red-100 p-3 text-red-700">
                        {{ message }}
                    </div>
                    <form @submit.prevent="login">
                        <div class="mx-5 my-5">
                            <label for="email">Email:</label> <br />
                            <input
                                type="text"
                                class="h-[30px] w-full border-0 border-b focus:border-2 focus:ring-0"
                                :class="errors.email ? 'border-red-500' : 'border-primary'"
                                v-model="form.email"
                            />
                            <p v-if="errors.email" class="mt-1 text-sm text-red-500">{{ errors.email[0] }}</p>
                        </div>
                        <div class="relative mx-5 my-5">
                            <label for="password">Password:</label> <br />
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                class="h-[30px] w-full border-0 border-b focus:border-2 focus:ring-0"
                                :class="errors.password ? 'border-red-500' : 'border-primary'"
                                v-model="form.password"
                            />
                            <span
                                class="absolute top-[38px] right-2 -translate-y-1/2 cursor-pointer text-gray-500"
                                @click="showPassword = !showPassword"
                            >
                                <Eye v-if="showPassword" class="h-5 w-5" />
                                <EyeOff v-else class="h-5 w-5" />
                            </span>
                            <p v-if="errors.password" class="mt-1 text-sm text-red-500">{{ errors.password[0] }}</p>
                        </div>
                        <div class="flex w-full justify-end">
                            <div class="mx-5 my-5">
                                <RouterLink to="/reset_password" class="font-semibold text-secondary"> Forgot Password?</RouterLink>
                            </div>
                        </div>
                        <div class="w-full px-5 py-5">
                            <button class="h-[40px] w-full cursor-pointer bg-primary text-white transition hover:opacity-90" type="submit">
                                Submit
                            </button>
                        </div>
                    </form>
                    <div class="mx-5 my-5 flex justify-center">
                        <p class="px-1">Don't have an account?</p>
                        <button @click="emit('switchToSignUp')" class="cursor-pointer font-bold text-secondary">Register Now</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
