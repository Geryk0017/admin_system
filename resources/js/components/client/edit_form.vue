<script setup>
import axios from 'axios';
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import SubmitButton from '../submitButton.vue';

const props = defineProps({
    application: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['application-updated', 'close']);

const router = useRouter();
const route = useRoute();
const currentUser = ref(null);
const loading = ref(true);
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

const salaryFromFormatted = ref('');
const salaryToFormatted = ref('');

// Format currency helper function
function formatCurrency(value) {
    if (!value) return '';
    return new Intl.NumberFormat().format(value);
}

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

    // Remove non-numeric characters
    value = value.replace(/[^\d]/g, '');

    // Format with commas
    const formatted = value ? new Intl.NumberFormat().format(value) : '';

    // Update visible field
    salaryToFormatted.value = formatted;

    // Save raw number to form
    form.value.salary_range_to = value ? Number(value) : null;
}

onMounted(() => {
    // Get logged-in user from sessionStorage
    const storedUser = JSON.parse(sessionStorage.getItem('user'));
    if (storedUser) {
        currentUser.value = storedUser;
        form.value.user_id = storedUser.id;
        fetchApplication();
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

const fetchApplication = async () => {
    try {
        loading.value = true;
        const app = props.application;

        form.value = {
            user_id: app.user_id,
            first_name: app.first_name,
            middle_name: app.middle_name,
            last_name: app.last_name,
            gender: app.gender,
            birthdate: app.birthdate,
            email: app.email,
            phone: app.phone,
            amount: app.amount,
            address: app.address,
            income_source: app.income_source,
            occupation: app.occupation,
            salary_range_from: app.salary_range_from,
            salary_range_to: app.salary_range_to,
        };

        // FORMAT SALARY WHEN FETCHING SALARY RANGE
        if (app.salary_range_from) {
            salaryFromFormatted.value = formatCurrency(app.salary_range_from);
        }
        if (app.salary_range_to) {
            salaryToFormatted.value = formatCurrency(app.salary_range_to);
        }

        loading.value = false;
    } catch (err) {
        console.error('Error fetching application:', err);
        loading.value = false;

        Swal.fire({
            title: 'Error!',
            text: 'Failed to load application data.',
            icon: 'error',
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'OK',
        }).then(() => {
            router.push({ name: 'user_home' });
        });
    }
};

const message = ref('');
const errors = ref({});
let messageTimeout = null;

const closeMessage = () => {
    message.value = '';
    if (messageTimeout) {
        clearTimeout(messageTimeout);
    }
};

const updateRegistration = async () => {
    errors.value = {};
    message.value = '';

    if (messageTimeout) {
        clearTimeout(messageTimeout);
    }

    try {
        const response = await axios.put(`/api/registration/${props.application.id}`, form.value);

        emit('close');
        Swal.fire({
            title: 'Success!',
            text: response.data.message || 'Application Updated Successfully',
            icon: 'success',
            confirmButtonColor: '#10b981',
            confirmButtonText: 'OK',
        }).then(() => {
            emit('application-updated');
        });
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors;
        } else {
            message.value = err.response?.data?.message || 'Update failed.';
        }
    }
};

const goBack = () => {
    router.push({ name: 'my_applications' });
};
</script>

<template>
    <div class="mx-auto max-w-6xl">
        <form @submit.prevent="updateRegistration">
            <!-- Success/Error Message -->
            <div
                v-if="message"
                class="mb-5 flex items-center justify-between rounded p-3"
                :class="message.includes('Success') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
            >
                <span>{{ message }}</span>
                <button type="button" @click="closeMessage" class="ml-4 text-lg font-bold transition hover:opacity-70">&times;</button>
            </div>

            <header>
                <h1 class="text-[20px] font-semibold">Personal Information</h1>
            </header>

            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:my-0">
                    <label for="first_name" class="mb-2 block text-sm font-semibold text-gray-700"> First Name </label>
                    <input
                        type="text"
                        class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                        :class="errors.first_name ? 'border-red-500' : 'border-primary'"
                        name="first_name"
                        v-model="form.first_name"
                    />
                    <p v-if="errors.first_name" class="mt-1 text-sm text-red-500">{{ errors.first_name[0] }}</p>
                </div>

                <div class="my-5 md:my-0">
                    <label for="middle_name" class="mb-2 block text-sm font-semibold text-gray-700"> Middle Name </label>
                    <input
                        type="text"
                        class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                        :class="errors.middle_name ? 'border-red-500' : 'border-primary'"
                        name="middle_name"
                        v-model="form.middle_name"
                    />
                    <p v-if="errors.middle_name" class="mt-1 text-sm text-red-500">{{ errors.middle_name[0] }}</p>
                </div>

                <div class="my-5 md:my-0">
                    <label for="last_name" class="mb-2 block text-sm font-semibold text-gray-700"> Last Name </label>
                    <input
                        type="text"
                        class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                        :class="errors.last_name ? 'border-red-500' : 'border-primary'"
                        name="last_name"
                        v-model="form.last_name"
                    />
                    <p v-if="errors.last_name" class="mt-1 text-sm text-red-500">{{ errors.last_name[0] }}</p>
                </div>

                <div class="my-5 md:my-0">
                    <label for="gender" class="mb-2 block text-sm font-semibold text-gray-700"> Gender </label>
                    <select
                        name="gender"
                        class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                        :class="errors.gender ? 'border-red-500' : 'border-primary'"
                        v-model="form.gender"
                    >
                        <option value="" selected>-- Select --</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                    <p v-if="errors.gender" class="mt-1 text-sm text-red-500">{{ errors.gender[0] }}</p>
                </div>

                <div class="col-span-2 my-5 md:my-0">
                    <label for="birthdate" class="mb-2 block text-sm font-semibold text-gray-700"> Date of Birth </label>
                    <input
                        type="date"
                        class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                        name="birthdate"
                        :class="errors.birthdate ? 'border-red-500' : 'border-primary'"
                        v-model="form.birthdate"
                    />
                    <p v-if="errors.birthdate" class="mt-1 text-sm text-red-500">{{ errors.birthdate[0] }}</p>
                </div>

                <div class="col-span-2 my-5 md:my-0">
                    <label for="email" class="mb-2 block text-sm font-semibold text-gray-700"> Email Address </label>
                    <input
                        type="email"
                        class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                        name="email"
                        :class="errors.email ? 'border-red-500' : 'border-primary'"
                        v-model="form.email"
                        placeholder="Enter email address"
                    />
                    <p v-if="errors.email" class="mt-1 text-sm text-red-500">{{ errors.email[0] }}</p>
                </div>

                <div class="my-5 md:my-0">
                    <label for="phone" class="mb-2 block text-sm font-semibold text-gray-700"> Mobile Number </label>
                    <input
                        type="text"
                        class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                        placeholder="Must start with 09 or +63"
                        name="phone"
                        :class="errors.phone ? 'border-red-500' : 'border-primary'"
                        v-model="form.phone"
                    />
                    <p v-if="errors.phone" class="mt-1 text-sm text-red-500">{{ errors.phone[0] }}</p>
                </div>

                <div class="col-span-3 my-5 md:my-0">
                    <label for="address" class="mb-2 block text-sm font-semibold text-gray-700"> Address </label>
                    <input
                        type="text"
                        class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                        :class="errors.address ? 'border-red-500' : 'border-primary'"
                        name="address"
                        v-model="form.address"
                        placeholder="Enter full address"
                    />
                    <p v-if="errors.address" class="mt-1 text-sm text-red-500">{{ errors.address[0] }}</p>
                </div>
            </div>

            <header class="my-5">
                <h1 class="text-[20px] font-semibold">Employement/Financial Details</h1>
            </header>
            <!-- Employement / Financial Details -->
            <div class="md:grid md:grid-cols-4 md:gap-6">
                <div class="col-span-2">
                    <div>
                        <label for="income_source" class="mb-2 block text-sm font-semibold text-gray-700"> Source of Income </label>
                        <select
                            name="gender"
                            class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                            :class="errors.income_source ? 'border-red-500' : 'border-primary'"
                            v-model="form.income_source"
                        >
                            <option value="" selected disabled>-- Select --</option>
                            <option value="employment">Employment</option>
                            <option value="self employed">Self Employed</option>
                            <option value="freelance">Freelance</option>
                            <option value="business">Business</option>
                            <option value="allowance">Allowance</option>
                            <option value="unemployed">Unemployed</option>
                            <option value="others">Others</option>
                        </select>
                        <p v-if="errors.income_source" class="mt-1 text-sm text-red-500">{{ errors.income_source[0] }}</p>
                    </div>
                    <div class="my-5">
                        <label for="occupation" class="mb-2 block text-sm font-semibold text-gray-700"> Occupation </label>
                        <input
                            type="text"
                            class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                            name="occupation"
                            :class="errors.occupation ? 'border-red-500' : 'border-primary'"
                            v-model="form.occupation"
                        />
                        <p v-if="errors.occupation" class="mt-1 text-sm text-red-500">{{ errors.occupation[0] }}</p>
                    </div>
                </div>
                <div class="col-span-2">
                    <div>
                        <label for="salary_range_from" class="mb-2 block text-sm font-semibold text-gray-700"> Salary Range from </label>
                        <input
                            type="text"
                            class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                            name="salary_range_from"
                            :class="errors.salary_range_from ? 'border-red-500' : 'border-primary'"
                            v-model="salaryFromFormatted"
                            @input="formatSalaryFrom"
                            placeholder="0"
                        />
                        <p v-if="errors.salary_range_from" class="mt-1 text-sm text-red-500">{{ errors.salary_range_from[0] }}</p>
                    </div>
                    <div class="my-5">
                        <label for="salary_range_to" class="mb-2 block text-sm font-semibold text-gray-700"> Salary Range to </label>
                        <input
                            type="text"
                            class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                            name="salary_range_to"
                            :class="errors.salary_range_to ? 'border-red-500' : 'border-primary'"
                            v-model="salaryToFormatted"
                            @input="formatSalaryTo"
                            placeholder="0"
                        />
                        <p v-if="errors.salary_range_to" class="mt-1 text-sm text-red-500">{{ errors.salary_range_to[0] }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-10 flex justify-center space-x-5">
                <SubmitButton bt="Update Application" class="w-full rounded-md text-[12px] md:text-[15px]" />
            </div>
        </form>
    </div>
</template>
