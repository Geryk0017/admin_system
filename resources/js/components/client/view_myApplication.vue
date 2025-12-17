<script setup>
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const props = defineProps({
    application: {
        type: Object,
        required: true,
    },
});

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

const formatCurrency = (amount) => {
    if (!amount) return '₱0.00';
    return '₱' + parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

onMounted(() => {
    // Get logged-in user from sessionStorage
    const storedUser = JSON.parse(sessionStorage.getItem('user'));
    if (storedUser) {
        currentUser.value = storedUser;
    }

    form.value = {
        user_id: props.application.user_id || '',
        first_name: props.application.first_name || '',
        middle_name: props.application.middle_name || '',
        last_name: props.application.last_name || '',
        gender: props.application.gender || '',
        birthdate: props.application.birthdate || '',
        email: props.application.email || '',
        phone: props.application.phone || '',
        address: props.application.address || '',
        income_source: props.application.income_source || '',
        occupation: props.application.occupation || '',
        salary_range_from: props.application.salary_range_from || '',
        salary_range_to: props.application.salary_range_to || '',
    };
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

const goBack = () => {
    router.back();
};
</script>

<template>
    <div class="mx-auto max-w-6xl">
        <form @submit.prevent="updateRegistration">
            <header>
                <h1 class="text-[20px] font-semibold">Personal Information</h1>
            </header>

            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:my-0">
                    <label for="first_name" class="mb-2 block text-sm font-semibold text-gray-700"> First Name </label>
                    <input
                        type="text"
                        class="h-[50px] w-full cursor-not-allowed border-0 border-b border-primary bg-gray-100 transition focus:outline-none"
                        name="first_name"
                        v-model="form.first_name"
                        disabled
                    />
                </div>

                <div class="my-5 md:my-0">
                    <label for="middle_name" class="mb-2 block text-sm font-semibold text-gray-700"> Middle Name </label>
                    <input
                        type="text"
                        class="h-[50px] w-full cursor-not-allowed border-0 border-b border-primary bg-gray-100 transition focus:outline-none"
                        name="middle_name"
                        v-model="form.middle_name"
                        disabled
                    />
                </div>

                <div class="my-5 md:my-0">
                    <label for="last_name" class="mb-2 block text-sm font-semibold text-gray-700"> Last Name </label>
                    <input
                        type="text"
                        class="h-[50px] w-full cursor-not-allowed border-0 border-b border-primary bg-gray-100 transition focus:outline-none"
                        name="last_name"
                        v-model="form.last_name"
                        disabled
                    />
                </div>

                <div class="my-5 md:my-0">
                    <label for="gender" class="mb-2 block text-sm font-semibold text-gray-700"> Gender </label>
                    <select
                        name="gender"
                        class="h-[50px] w-full cursor-not-allowed border-0 border-b border-primary bg-gray-100 transition focus:outline-none"
                        v-model="form.gender"
                        disabled
                    >
                        <option value="" selected>-- Select --</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>

                <div class="col-span-2 my-5 md:my-0">
                    <label for="birthdate" class="mb-2 block text-sm font-semibold text-gray-700"> Date of Birth </label>
                    <input
                        type="date"
                        class="h-[50px] w-full cursor-not-allowed border-0 border-b border-primary bg-gray-100 transition focus:outline-none"
                        name="birthdate"
                        v-model="form.birthdate"
                        disabled
                    />
                </div>

                <div class="col-span-2 my-5 md:my-0">
                    <label for="email" class="mb-2 block text-sm font-semibold text-gray-700"> Email Address </label>
                    <input
                        type="email"
                        class="h-[50px] w-full cursor-not-allowed border-0 border-b border-primary bg-gray-100 transition focus:outline-none"
                        name="email"
                        v-model="form.email"
                        placeholder="Enter email address"
                        disabled
                    />
                </div>

                <div class="my-5 md:my-0">
                    <label for="phone" class="mb-2 block text-sm font-semibold text-gray-700"> Mobile Number </label>
                    <input
                        type="text"
                        class="h-[50px] w-full cursor-not-allowed border-0 border-b border-primary bg-gray-100 transition focus:outline-none"
                        placeholder="Must start with 09 or +63"
                        name="phone"
                        v-model="form.phone"
                        disabled
                    />
                </div>

                <div class="col-span-3 my-5 md:my-0">
                    <label for="last_name" class="mb-2 block text-sm font-semibold text-gray-700"> Address </label>
                    <input
                        type="text"
                        class="h-[50px] w-full cursor-not-allowed border-0 border-b border-primary bg-gray-100 transition focus:outline-none"
                        name="last_name"
                        v-model="form.address"
                        placeholder="Enter full address"
                        disabled
                    />
                </div>
            </div>
            <header class="my-5">
                <h1 class="text-[20px] font-semibold">Employement/Financial Details</h1>
            </header>

            <div class="md:grid md:grid-cols-4 md:gap-6">
                <div class="col-span-2">
                    <div>
                        <label for="income_source" class="mb-2 block text-sm font-semibold text-gray-700"> Source of Income </label>
                        <select
                            name="gender"
                            class="h-[50px] w-full cursor-not-allowed border-0 border-b border-primary bg-gray-100 transition focus:outline-none"
                            v-model="form.income_source"
                            disabled
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
                    </div>
                    <div class="my-5">
                        <label for="occupation" class="mb-2 block text-sm font-semibold text-gray-700"> Occupation </label>
                        <input
                            type="text"
                            class="h-[50px] w-full cursor-not-allowed border-0 border-b border-primary bg-gray-100 transition focus:outline-none"
                            name="occupation"
                            disabled
                            v-model="form.occupation"
                        />
                    </div>
                </div>
                <div class="col-span-2">
                    <div>
                        <label for="salary_range_from" class="mb-2 block text-sm font-semibold text-gray-700"> Salary Range from </label>
                        <input
                            type="text"
                            class="h-[50px] w-full cursor-not-allowed border-0 border-b border-primary bg-gray-100 transition focus:outline-none"
                            name="salary_range_from"
                            :value="formatCurrency(form.salary_range_from)"
                            disabled
                            @input="formatSalaryFrom"
                            placeholder="0"
                        />
                    </div>
                    <div class="my-5">
                        <label for="salary_range_to" class="mb-2 block text-sm font-semibold text-gray-700"> Salary Range to </label>
                        <input
                            type="text"
                            class="h-[50px] w-full cursor-not-allowed border-0 border-b border-primary bg-gray-100 transition focus:outline-none"
                            name="salary_range_to"
                            disabled
                            :value="formatCurrency(form.salary_range_to)"
                            @input="formatSalaryTo"
                            placeholder="0"
                        />
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
