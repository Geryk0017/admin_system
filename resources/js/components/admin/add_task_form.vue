<script setup>
import SubmitButton from '@/components/submitButton.vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';

// Define emit
const emit = defineEmits(['task-added']);

const progress = ref(0);
const router = useRouter();
const developers = ref([]);
const clientsWithRegistration = ref([]);
const message = ref('');
const errors = ref({});
const currentUser = ref(null);
let messageTimeout = null;

onMounted(() => {
    const storedUser = JSON.parse(sessionStorage.getItem('user'));
    if (storedUser) {
        currentUser.value = storedUser;

        if (storedUser.role === 'developer') {
            task.value.developer_id = storedUser.id;
        }

        fetchDevelopers();
        fetchClientsWithRegistration();
    }
});

const task = ref({
    title: '',
    description: '',
    developer_id: '',
    client_id: '',
    priority_level: '',
    start_date: '',
    due_date: '',
    notes: '',
    progress: 0,
});

const closeMessage = () => {
    message.value = '';
    if (messageTimeout) {
        clearTimeout(messageTimeout);
    }
};

const fetchDevelopers = async () => {
    try {
        const response = await axios.get('/api/developers');
        developers.value = response.data;
    } catch (error) {
        console.error('Error fetching developers:', error);
    }
};

const fetchClientsWithRegistration = async () => {
    try {
        const clientsWithRegistrationResponse = await axios.get('/api/clients-with-registration');
        clientsWithRegistration.value = clientsWithRegistrationResponse.data;
    } catch (error) {
        console.error('Error fetching clients:', error);
    }
};

const addTask = () => {
    errors.value = {};
    message.value = '';

    if (messageTimeout) {
        clearTimeout(messageTimeout);
    }

    // Set progress before submitting
    task.value.progress = progress.value;

    axios
        .post('/api/add_task', task.value)
        .then((res) => {
            emit('close');

            // Small delay to ensure modal is closed before showing SweetAlert
            setTimeout(() => {
                Swal.fire({
                    title: 'Success!',
                    text: res.data.message || 'Task Added Successfully',
                    icon: 'success',
                    confirmButtonColor: '#10b981',
                    confirmButtonText: 'OK',
                }).then(() => {
                    // Reset form
                    task.value = {
                        title: '',
                        description: '',
                        developer_id: currentUser.value?.role === 'developer' ? currentUser.value.id : '',
                        client_id: '',
                        priority_level: '',
                        start_date: '',
                        due_date: '',
                        notes: '',
                        progress: 0,
                    };
                    progress.value = 0;

                    // Emit success to refresh task list
                    emit('task-added');
                });
            }, 100);
        })
        .catch((err) => {
            if (err.response?.status === 422) {
                errors.value = err.response.data.errors;
            } else {
                // Other errors – optionally close modal or show message
                message.value = err.response?.data?.message || 'Adding Task failed.';

                Swal.fire({
                    title: 'Error!',
                    text: err.response?.data?.message || 'Task Submission failed.',
                    icon: 'error',
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'OK',
                });
            }
        });
};

const goBack = () => {
    router.back();
};
</script>

<style>
@keyframes moveStripes {
    from {
        background-position: 0 0;
    }
    to {
        background-position: 30px 0;
    }
}

.animate-stripes {
    background-image: repeating-linear-gradient(45deg, rgba(255, 255, 255, 0.2) 0, rgba(255, 255, 255, 0.2) 10px, transparent 10px, transparent 20px);
    animation: moveStripes 1s linear infinite;
}
</style>

<template>
    <div class="max-w-3xl">
        <form @submit.prevent="addTask" class="w-full">
            <div class="grid w-full grid-cols-1 gap-4 rounded-md md:grid-cols-2">
                <!-- Task Title -->
                <div class="md:col-span-2">
                    <label for="title" class="mb-2 block text-sm font-semibold text-gray-700">Task Title <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        v-model="task.title"
                        class="w-full border-0 border-b-2 transition-colors focus:outline-none"
                        :class="errors.title ? 'border-red-500' : 'border-primary'"
                        placeholder="Enter task title"
                    />
                    <p v-if="errors.title" class="mt-1 text-sm text-red-500">{{ errors.title[0] }}</p>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="mb-2 block text-sm font-semibold text-gray-700"
                        >Description <span class="text-red-500">*</span></label
                    >
                    <textarea
                        v-model="task.description"
                        class="w-full resize-none rounded-md border-2 p-3 transition-colors focus:outline-none"
                        :class="errors.description ? 'border-red-500' : 'border-primary'"
                        rows="3"
                        placeholder="Provide a detailed description"
                    ></textarea>
                    <p v-if="errors.description" class="mt-1 text-sm text-red-500">{{ errors.description[0] }}</p>
                </div>

                <!-- Assigned To (Developer) -->
                <div v-if="currentUser && currentUser.role === 'developer'">
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Assigned To</label>
                    <input
                        type="text"
                        :value="`${currentUser.first_name} ${currentUser.middle_name || ''} ${currentUser.last_name}`.trim()"
                        class="w-full border-0 border-b-2 border-primary"
                        disabled
                        readonly
                    />
                </div>

                <!-- Assigned To (Admin) -->
                <div v-else>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Assigned To <span class="text-red-500">*</span></label>
                    <select
                        v-model="task.developer_id"
                        class="w-full border-0 border-b-2 bg-white transition-colors focus:outline-none"
                        :class="errors.developer_id ? 'border-red-500' : 'border-primary'"
                    >
                        <option value="" disabled>-- Select Developer --</option>
                        <option v-for="dev in developers" :key="dev.id" :value="dev.id">
                            {{ dev.first_name }} {{ dev.middle_name || '' }} {{ dev.last_name }}
                        </option>
                    </select>
                    <p v-if="errors.developer_id" class="mt-1 text-sm text-red-500">{{ errors.developer_id[0] }}</p>
                </div>

                <!-- Priority Level -->
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Priority Level <span class="text-red-500">*</span></label>
                    <select
                        v-model="task.priority_level"
                        class="w-full border-0 border-b-2 bg-white transition-colors focus:outline-none"
                        :class="errors.priority_level ? 'border-red-500' : 'border-primary'"
                    >
                        <option value="" disabled>-- Select Priority --</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                    <p v-if="errors.priority_level" class="mt-1 text-sm text-red-500">{{ errors.priority_level[0] }}</p>
                </div>

                <div class="md:col-span-2">
                    <div class="col-span-3 grid w-full gap-4 md:grid-cols-3">
                        <!-- Start Date -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700">Start Date <span class="text-red-500">*</span></label>
                            <input
                                type="date"
                                v-model="task.start_date"
                                name="start_date"
                                class="w-full border-0 border-b-2 transition-colors focus:outline-none"
                                :class="errors.start_date ? 'border-red-500' : 'border-primary'"
                            />
                            <p v-if="errors.start_date" class="mt-1 text-sm text-red-500">{{ errors.start_date[0] }}</p>
                        </div>
                        <!-- Due Date -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700">Due Date <span class="text-red-500">*</span></label>
                            <input
                                type="date"
                                v-model="task.due_date"
                                name="due_date"
                                class="w-full border-0 border-b-2 transition-colors focus:outline-none"
                                :class="errors.due_date ? 'border-red-500' : 'border-primary'"
                            />
                            <p v-if="errors.due_date" class="mt-1 text-sm text-red-500">{{ errors.due_date[0] }}</p>
                        </div>
                        <!-- Client -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700">Client Name <span class="text-red-500">*</span></label>
                            <select
                                v-model="task.client_id"
                                class="w-full border-0 border-b-2 bg-white transition-colors focus:outline-none"
                                :class="errors.client_id ? 'border-red-500' : 'border-primary'"
                            >
                                <option value="" disabled>-- Select Client --</option>
                                <option v-for="client in clientsWithRegistration" :key="client.id" :value="client.id">
                                    {{ client.first_name }} {{ client.last_name }}
                                </option>
                            </select>
                            <p v-if="errors.client_id" class="mt-1 text-sm text-red-500">{{ errors.client_id[0] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Notes (Optional)</label>
                    <textarea
                        v-model="task.notes"
                        class="w-full resize-none rounded-md border-2 border-primary p-3 transition-colors focus:outline-none"
                        rows="2"
                        placeholder="Add any additional notes..."
                    ></textarea>
                </div>

                <!-- Progress -->
                <div class="md:col-span-2">
                    <label class="mb-3 block text-sm font-semibold text-gray-700">Initial Progress</label>
                    <div class="rounded-lg border-2 border-primary bg-gray-50 p-4">
                        <div class="mb-2 flex items-center gap-4">
                            <input type="range" min="0" max="100" v-model="progress" class="flex-1 cursor-not-allowed accent-blue-600" disabled />
                            <span class="w-14 text-right text-xl font-bold text-blue-600">{{ progress }}%</span>
                        </div>
                        <div class="h-6 w-full overflow-hidden rounded-full bg-gray-300 shadow-inner">
                            <div
                                class="animate-stripes h-full bg-gradient-to-r from-blue-500 to-blue-700 bg-[length:30px_30px] transition-all duration-500"
                                :style="{ width: progress + '%' }"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-4 flex justify-end space-x-2 md:col-span-2">
                    <SubmitButton class="ounded-lg w-full rounded-md px-8 py-3 shadow-md" bt="Create Task" />
                </div>
            </div>
        </form>
    </div>
</template>
