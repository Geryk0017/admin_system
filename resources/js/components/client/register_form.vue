<script setup>
import axios from 'axios';
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import SubmitButton from '../submitButton.vue';
import Step1 from './Step1.vue';
import Step2 from './Step2.vue';
import Step3 from './Step3.vue';

const currentStep = ref(1);
const totalSteps = 3;
const router = useRouter();
const currentUser = ref(null);
const form = ref({
    user_id: '',
    first_name: '',
    middle_name: '',
    last_name: '',
    gender: '',
    birthdate: '',
    email: '',
    phone: '',
    amount: '',
    address: '',
    income_source: '',
    occupation: '',
    salary_range_from: '',
    salary_range_to: '',
});

function nextStep() {
    if (currentStep.value < totalSteps) currentStep.value++;
}

function prevStep() {
    if (currentStep.value > 1) currentStep.value--;
}

const salaryFromFormatted = ref('');
const salaryToFormatted = ref('');

function formatSalaryFrom(e) {
    let value = e.target.value;

    // Remove non-numeric characters
    value = value.replace(/[^\d]/g, '');

    // Format with commas
    const formatted = value ? new Intl.NumberFormat().format(value) : '';

    // Update visible field
    salaryFromFormatted.value = formatted;

    // Save raw number to form
    form.value.salary_range_from = value ? Number(value) : null;
}

function formatSalaryTo(e) {
    let value = e.target.value;

    value = value.replace(/[^\d]/g, '');

    const formatted = value ? new Intl.NumberFormat().format(value) : '';

    salaryToFormatted.value = formatted;
    form.value.salary_range_to = value ? Number(value) : null;
}

onMounted(() => {
    // Get logged-in user from sessionStorage
    const storedUser = JSON.parse(sessionStorage.getItem('user'));
    if (storedUser) {
        currentUser.value = storedUser;

        form.value.first_name = storedUser.first_name || '';
        form.value.middle_name = storedUser.middle_name || '';
        form.value.last_name = storedUser.last_name || '';
        form.value.email = storedUser.email || '';
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

    // Get user ID from sessionStorage
    const user = JSON.parse(sessionStorage.getItem('user'));
    if (user && user.id) {
        form.value.user_id = user.id;
    } else {
        Swal.fire({
            title: 'Error!',
            text: 'User not logged in. Please log in first.',
            icon: 'error',
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'OK',
        });
        return;
    }

    axios
        .post('/api/registration', form.value)
        .then((res) => {
            // Show success alert
            Swal.fire({
                title: 'Success!',
                text: res.data.message || 'Application Submitted Successfully',
                icon: 'success',
                confirmButtonColor: '#10b981',
                confirmButtonText: 'OK',
            }).then(() => {
                router.push({ name: 'user_home' }).then(() => {
                    window.location.reload();
                });

                // Reset form
                form.value = {
                    user_id: '',
                    first_name: '',
                    middle_name: '',
                    last_name: '',
                    gender: '',
                    birthdate: '',
                    email: '',
                    phone: '',
                    amount: '',
                    address: '',
                    income_source: '',
                    occupation: '',
                    salary_range_from: '',
                    salary_range_to: '',
                };
            });
        })
        .catch((err) => {
            if (err.response?.status === 422) {
                // Validation errors
                errors.value = err.response.data.errors;

                // Show validation error alert
                Swal.fire({
                    title: 'Validation Error',
                    text: err.response?.data?.message || 'Please check the form for errors.',
                    icon: 'error',
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'OK',
                });
            } else {
                message.value = err.response?.data?.message || 'Application failed.';

                // Show general error alert
                Swal.fire({
                    title: 'Error!',
                    text: err.response?.data?.message || 'Application failed.',
                    icon: 'error',
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'OK',
                });
            }
        });
};
</script>

<template>
    <div class="mx-auto h-screen max-w-3xl px-5 py-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Application Form</h1>
            <p class="mt-2 text-gray-600">Fill out the form below to register</p>
        </div>

        <!-- STEPS -->
        <div class="my-5 flex justify-center">
            <ul class="steps steps-vertical w-full lg:steps-horizontal">
                <li :class="currentStep >= 1 ? 'step step-primary' : 'step'">Personal Information</li>
                <li :class="currentStep >= 2 ? 'step step-primary' : 'step'">Employment/Financial Info</li>
                <li :class="currentStep >= 3 ? 'step step-primary' : 'step'">Review your Application</li>
            </ul>
        </div>

        <div class="rounded-lg bg-white shadow-lg">
            <form @submit.prevent="currentStep === totalSteps ? register() : nextStep()">
                <!-- Success/Error Message -->
                <div
                    v-if="message"
                    class="mb-5 flex items-center justify-between rounded p-3"
                    :class="message.includes('Success') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                >
                    <span>{{ message }}</span>
                    <button type="button" @click="closeMessage" class="ml-4 text-lg font-bold transition hover:opacity-70">&times;</button>
                </div>

                <!-- PERSONAL DETAILS -->
                <Step1 v-if="currentStep === 1" :form="form" :errors="errors" />

                <!-- EMPLOYEMENT/FINANCIAL DETAILS -->
                <Step2
                    v-if="currentStep === 2"
                    :form="form"
                    :errors="errors"
                    :salary-from-formatted="salaryFromFormatted"
                    :salary-to-formatted="salaryToFormatted"
                    :format-salary-from="formatSalaryFrom"
                    :format-salary-to="formatSalaryTo"
                    @update:form="form = $event"
                />

                <!-- REVIEW INPUTS -->
                <Step3
                    v-if="currentStep === 3"
                    :form="form"
                    :errors="errors"
                    :salary-from-formatted="salaryFromFormatted"
                    :salary-to-formatted="salaryToFormatted"
                    :format-salary-from="formatSalaryFrom"
                    :format-salary-to="formatSalaryTo"
                />

                <div class="m-5 mt-10 flex justify-center gap-5">
                    <!-- Back button -->
                    <SubmitButton type="button" v-if="currentStep > 1" bt="← Back" class="my-5 w-[200px] rounded-md bg-gray-500" @click="prevStep" />

                    <!-- Next / Submit button -->
                    <SubmitButton type="submit" :bt="currentStep === totalSteps ? 'Submit Application' : 'Next'" class="my-5 w-[200px] rounded-md" />
                </div>
            </form>
        </div>
    </div>
</template>
