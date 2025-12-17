<script setup>
import SubmitButton from '@/components/submitButton.vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    application: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['close', 'refresh']);

const user = computed(() => {
    const userData = sessionStorage.getItem('user');
    return userData ? JSON.parse(userData) : {};
});

const role = computed(() => {
    return user.value.role || '';
});

// Create a local copy of application data for editing
const form = ref({
    status: '',
    remarks: '',
});

onMounted(() => {
    // Initialize form with application data
    form.value.status = props.application.status || '';
    form.value.remarks = props.application.remarks || '';
});

const formatCurrency = (value) => {
    if (value === null || value === undefined || value === '') return '₱0.00';
    return (
        '₱' +
        Number(value).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        })
    );
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
};

const errors = ref({});

const updateApplication = () => {
    errors.value = {};

    console.log('Updating application:', props.application.id);
    console.log('Form data:', form.value);

    axios
        .put(`/api/registration/${props.application.id}`, form.value)
        .then((res) => {
            // Close modal
            emit('close');

            // Show success message
            Swal.fire({
                title: 'Success!',
                text: res.data.message || 'Application updated successfully',
                icon: 'success',
                confirmButtonColor: '#10b981',
                confirmButtonText: 'OK',
            }).then(() => {
                // Emit refresh to reload the list
                emit('refresh');
            });
        })
        .catch((err) => {
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
                    text: err.response?.data?.message || 'Update failed.',
                    icon: 'error',
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'OK',
                });
            }
        });
};
</script>

<template>
    <form @submit.prevent="updateApplication">
        <h1 class="text-[20px] font-semibold">Personal Information</h1>
        <div class="w-full">
            <div class="justify-between gap-5 md:flex">
                <div class="my-5 flex-1 md:flex md:flex-col">
                    <label for="email">First Name</label>
                    <input
                        type="email"
                        class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                        name="first_name"
                        readonly
                        :value="application.first_name"
                    />
                </div>

                <div class="my-5 flex-1 md:flex md:flex-col">
                    <label for="email">Middle Name</label>
                    <input
                        type="email"
                        class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                        name="email"
                        readonly
                        :value="application.middle_name || 'N/A'"
                    />
                </div>

                <div class="my-5 flex-1 md:flex md:flex-col">
                    <label for="email">Last Name</label>
                    <input
                        type="email"
                        class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                        name="email"
                        readonly
                        :value="application.last_name"
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
                        :value="application.email"
                    />
                </div>
                <div class="flex-1 md:flex md:flex-col">
                    <label for="phone_number">Mobile Number</label>
                    <input
                        type="text"
                        class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                        name="phone_number"
                        readonly
                        :value="application.phone"
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
                        :value="application.address || 'N/A'"
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
                        :value="formatDate(application.birthdate)"
                    />
                </div>
                <div class="my-5 flex-1 md:flex md:flex-col">
                    <label for="gender">Gender</label>
                    <input
                        type="text"
                        class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                        name="gender"
                        readonly
                        :value="application.gender ? application.gender.charAt(0).toUpperCase() + application.gender.slice(1) : 'Not specified'"
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
                            :value="application.income_source || 'N/A'"
                            disabled
                        />
                    </div>

                    <div class="my-5 md:flex md:flex-col">
                        <label for="occupation"> Occupation </label>
                        <input
                            type="text"
                            class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                            name="occupation"
                            :value="application.occupation || 'N/A'"
                            disabled
                        />
                    </div>

                    <div class="my-5 md:flex md:flex-col">
                        <label for="income"> Salary Range </label>
                        <input
                            type="text"
                            class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                            name="income"
                            :value="formatCurrency(application.salary_range_from) + ' - ' + formatCurrency(application.salary_range_to)"
                            disabled
                        />
                    </div>
                </div>
            </div>

            <div class="md:w-[50%]">
                <h1 class="text-[20px] font-semibold">Status and Remarks</h1>

                <div>
                    <div class="my-5 md:flex md:flex-col">
                        <label for="status">Status <span class="text-red-500">*</span></label>
                        <select
                            v-if="role === 'verifier'"
                            name="status"
                            class="w-full cursor-pointer border-0 border-b-2 border-primary"
                            v-model="form.status"
                        >
                            <option value="" disabled>-- Select --</option>
                            <option value="pending">Pending</option>
                            <option value="verified">Verified</option>
                            <option value="under review">Under Review</option>
                            <option value="rejected">Rejected</option>
                            <option value="approved" disabled>Approved</option>
                            <option value="rejected" disabled>Rejected</option>
                        </select>

                        <select
                            v-else-if="role === 'admin'"
                            name="status"
                            class="w-full cursor-pointer border-0 border-b-2 border-primary"
                            v-model="form.status"
                        >
                            <option value="" disabled>-- Select --</option>
                            <option value="pending" disabled>Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="verified" disabled>Verified</option>
                            <option value="under review" disabled>Under Review</option>
                        </select>
                    </div>

                    <div class="my-5 md:flex md:flex-col">
                        <label for="remarks">Remarks</label>
                        <textarea
                            class="w-full resize-none border-0 border-b-2 border-primary"
                            name="remarks"
                            :value="application.remarks || 'No remarks'"
                            rows="3"
                        ></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- BUTTONS -->
        <div class="mt-10 flex flex-wrap justify-center gap-4">
            <SubmitButton bt="Update Application" class="rounded-lg px-6 py-3" />
        </div>
    </form>
</template>
