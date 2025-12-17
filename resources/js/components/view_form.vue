<script setup>
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';

const props = defineProps({
    application: {
        type: Object,
        required: true,
    },
});
const emit = defineEmits(['close', 'refresh', 'application-updated']);

const router = useRouter();
const loading = ref(false);

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
    remarks: '',
    status: '',
});

const salaryFromFormatted = ref('');
const salaryToFormatted = ref('');

// Format currency helper function
const formatCurrency = (value) => {
    if (!value) return '';
    return new Intl.NumberFormat().format(value);
};

const applicationatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
};

const applicationatCurrency = (amount) => {
    if (!amount) return '₱0.00';
    return '₱' + parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

onMounted(() => {
    fetchApplication();
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
            remarks: app.remarks,
            status: app.status,
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
</script>

<template>
    <div>
        <h1 class="text-[20px] font-semibold">Personal Information</h1>

        <div class="w-full">
            <div class="justify-between gap-5 md:flex">
                <div class="my-5 flex-1 md:flex md:flex-col">
                    <label for="email">First Name</label>
                    <input
                        type="text"
                        class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                        name="first_name"
                        readonly
                        :value="form.first_name"
                    />
                </div>

                <div class="my-5 flex-1 md:flex md:flex-col">
                    <label for="email">Middle Name</label>
                    <input
                        type="text"
                        class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                        name="middle_name"
                        readonly
                        :value="form.middle_name || 'N/A'"
                    />
                </div>

                <div class="my-5 flex-1 md:flex md:flex-col">
                    <label for="email">Last Name</label>
                    <input
                        type="text"
                        class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                        name="last_name"
                        readonly
                        :value="form.last_name"
                    />
                </div>
            </div>
            <div class="justify-between gap-5 md:flex">
                <div class="flex-1 md:flex md:flex-col">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                        name="email"
                        readonly
                        :value="form.email"
                    />
                </div>
                <div class="flex-1 md:flex md:flex-col">
                    <label for="phone_number">Mobile Number</label>
                    <input
                        type="text"
                        class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                        name="phone_number"
                        readonly
                        :value="form.phone"
                    />
                </div>
            </div>

            <div class="justify-between gap-5 md:flex">
                <div class="my-5 flex-1 md:flex md:flex-col">
                    <label for="address">Address</label>
                    <input
                        type="text"
                        class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                        name="address"
                        :value="form.address || 'N/A'"
                        readonly
                    />
                </div>
                <div class="my-5 flex-1 md:flex md:flex-col">
                    <label for="birthdate">Birthdate</label>
                    <input
                        type="text"
                        class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                        name="birthdate"
                        readonly
                        :value="applicationatDate(form.birthdate)"
                    />
                </div>
                <div class="my-5 flex-1 md:flex md:flex-col">
                    <label for="gender">Gender</label>
                    <input
                        type="text"
                        class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                        name="gender"
                        readonly
                        :value="form.gender ? form.gender.charAt(0).toUpperCase() + form.gender.slice(1) : 'Not specified'"
                    />
                </div>
            </div>
        </div>

        <div class="w-full gap-5 md:flex md:justify-between">
            <div class="md:w-[50%]">
                <h1 class="text-[20px] font-semibold">Employment/Financial Info</h1>
                <div class="">
                    <div class="my-5 md:flex md:flex-col">
                        <label for="income_source"> Source of Income </label>
                        <input
                            type="text"
                            class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                            name="income_source"
                            :value="form.income_source || 'N/A'"
                            disabled
                        />
                    </div>

                    <div class="my-5 md:flex md:flex-col">
                        <label for="occupation"> Occupation </label>
                        <input
                            type="text"
                            class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                            name="occupation"
                            :value="form.occupation || 'N/A'"
                            disabled
                        />
                    </div>

                    <div class="my-5 md:flex md:flex-col">
                        <label for="income"> Salary Range </label>
                        <input
                            type="text"
                            class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                            name="income"
                            :value="applicationatCurrency(form.salary_range_from) + ' - ' + applicationatCurrency(form.salary_range_to)"
                            disabled
                        />
                    </div>
                </div>
            </div>

            <div class="md:w-[50%]">
                <h1 class="text-[20px] font-semibold">Status and Remarks</h1>

                <div>
                    <div class="my-5 md:flex md:flex-col">
                        <label for="status">Status</label>
                        <input
                            type="text"
                            class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                            name="status"
                            :value="form.status?.charAt(0).toUpperCase() + form.status?.slice(1) || 'N/A'"
                            disabled
                        />
                    </div>

                    <div class="my-5 md:flex md:flex-col">
                        <label for="remarks">Remarks</label>
                        <textarea
                            class="w-full cursor-not-allowed resize-none border-0 border-b-2 border-primary bg-gray-100"
                            name="remarks"
                            readonly
                            :value="form.remarks || 'No remarks'"
                            rows="3"
                        ></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
