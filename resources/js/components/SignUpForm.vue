<script setup>
import axios from 'axios';
import { Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';
import { useRouter } from 'vue-router';
const emit = defineEmits(['switchToSignIn']);

const router = useRouter();
const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = ref({
    email: '',
    first_name: '',
    middle_name: '',
    last_name: '',
    password: '',
    retype_password: '',
});

const message = ref('');
const errors = ref({});
let messageTimeout = null;

const closeMessage = () => {
    message.value = '';
    if (messageTimeout) {
        clearTimeout(messageTimeout);
    }
};

const register = () => {
    errors.value = {};
    message.value = '';
    if (messageTimeout) {
        clearTimeout(messageTimeout);
    }

    axios
        .post('/api/signup', form.value)
        .then((res) => {
            message.value = res.data.message;
            form.value = { email: '', first_name: '', middle_name: '', last_name: '', password: '', retype_password: '' };

            sessionStorage.setItem('user', JSON.stringify(res.data.user));
            // Show message and redirect to register page after 3 seconds
            messageTimeout = setTimeout(() => {
                router.push('/client/register'); // Route to register page after login
            }, 3000);
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
    <div class="flex h-screen items-center justify-center bg-white">
        <div class="w-[100%] md:w-[75%]">
            <div>
                <!-- HEADER -->
                <div class="flex h-[100px] justify-center px-5 leading-[100px]">
                    <header>
                        <h1 class="text-[25px]">Sign Up</h1>
                    </header>
                </div>
                <!-- SIGNUP FORM -->
                <div class="m-5">
                    <!-- Success/Error Message -->
                    <div
                        v-if="message"
                        class="mx-5 mb-5 flex items-center justify-between rounded p-3"
                        :class="message.includes('success') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                    >
                        <span>{{ message }}</span>
                        <button @click="closeMessage" class="ml-4 text-lg font-bold transition hover:opacity-70">&times;</button>
                    </div>
                    <form @submit.prevent="register">
                        <div class="mx-5 my-5">
                            <label for="email">Email:</label> <br />
                            <input
                                type="text"
                                class="h-[30px] w-full border-0 border-b focus:border-2 focus:ring-0"
                                :class="errors.email ? 'border-red-500' : 'border-primary'"
                                name="email"
                                v-model="form.email"
                            />
                            <p v-if="errors.email" class="mt-1 text-sm text-red-500">{{ errors.email[0] }}</p>
                        </div>
                        <div class="mx-5 my-5">
                            <label for="first_name">First Name:</label> <br />
                            <input
                                type="text"
                                class="h-[30px] w-full border-0 border-b focus:border-2 focus:ring-0"
                                :class="errors.first_name ? 'border-red-500' : 'border-primary'"
                                v-model="form.first_name"
                                name="first_name"
                            />
                            <p v-if="errors.first_name" class="mt-1 text-sm text-red-500">{{ errors.first_name[0] }}</p>
                        </div>

                        <div class="mx-5 my-5">
                            <label for="middle_name">Middle Name:</label> <br />
                            <input
                                type="text"
                                class="h-[30px] w-full border-0 border-b focus:border-2 focus:ring-0"
                                :class="errors.middle_name ? 'border-red-500' : 'border-primary'"
                                v-model="form.middle_name"
                                name="middle_name"
                            />
                            <p v-if="errors.middle_name" class="mt-1 text-sm text-red-500">{{ errors.middle_name[0] }}</p>
                        </div>

                        <div class="mx-5 my-5">
                            <label for="last_name">Last Name:</label> <br />
                            <input
                                type="text"
                                class="h-[30px] w-full border-0 border-b focus:border-2 focus:ring-0"
                                :class="errors.last_name ? 'border-red-500' : 'border-primary'"
                                v-model="form.last_name"
                                name="last_name"
                            />
                            <p v-if="errors.last_name" class="mt-1 text-sm text-red-500">{{ errors.last_name[0] }}</p>
                        </div>

                        <div class="relative mx-5 my-5">
                            <label for="password">Password:</label> <br />
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                :class="errors.password ? 'border-red-500' : 'border-primary'"
                                class="h-[30px] w-full border-0 border-b focus:border-2 focus:ring-0"
                                v-model="form.password"
                                name="password"
                            />
                            <span
                                class="absolute top-[38px] right-2 -translate-y-1/2 cursor-pointer text-gray-500"
                                @click="showPassword = !showPassword"
                            >
                                <Eye v-if="showPassword" class="h-5 w-5" />
                                <EyeOff v-else class="h-5 w-5" />
                            </span>
                        </div>

                        <div class="relative mx-5 my-5">
                            <label for="retype_password">Confirm Password:</label> <br />
                            <input
                                :type="showConfirmPassword ? 'text' : 'password'"
                                :class="errors.password ? 'border-red-500' : 'border-primary'"
                                class="h-[30px] w-full border-0 border-b border-primary focus:border-2 focus:ring-0"
                                v-model="form.retype_password"
                                name="password"
                            />

                            <span
                                class="absolute top-[38px] right-2 -translate-y-1/2 cursor-pointer text-gray-500"
                                @click="showConfirmPassword = !showConfirmPassword"
                            >
                                <Eye v-if="showConfirmPassword" class="h-5 w-5" />
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
                        <p class="px-1">Already have an account?</p>
                        <button @click="emit('switchToSignIn')" class="cursor-pointer font-bold text-secondary">Sign In</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
