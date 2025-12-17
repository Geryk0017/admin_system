<script setup>
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';

const props = defineProps({
    task: {
        type: Object,
        required: true,
    },
});

const progress = ref(0);
const router = useRouter();
const loading = ref(false);
const clients = ref([]);
const developers = ref([]);
const message = ref('');
const errors = ref({});
const currentUser = ref(null);
let messageTimeout = null;

const emit = defineEmits(['close']);

const taskForm = ref({
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

const toDashboard = () => {
    emit('close');
};

onMounted(() => {
    const storedUser = JSON.parse(sessionStorage.getItem('user'));
    if (storedUser) {
        currentUser.value = storedUser;
    }

    fetchDevelopers();
    fetchClients();

    // Populate form with task data from props
    taskForm.value = {
        title: props.task.title || '',
        description: props.task.description || '',
        developer_id: props.task.developer_id || '',
        client_id: props.task.client_id || '',
        priority_level: props.task.priority_level || '',
        start_date: props.task.start_date || '',
        due_date: props.task.due_date || '',
        notes: props.task.notes || '',
        progress: props.task.progress || 0,
    };

    progress.value = props.task.progress || 0;

    console.log('View Task loaded:', taskForm.value);
});

const fetchDevelopers = async () => {
    try {
        const response = await axios.get('/api/developers');
        developers.value = response.data;
    } catch (error) {
        console.error('Error fetching developers:', error);
    }
};

const fetchClients = async () => {
    try {
        const response = await axios.get('/api/user_client');
        clients.value = response.data;
    } catch (error) {
        console.error('Error fetching clients:', error);
    }
};

const getDeveloperName = computed(() => {
    if (!taskForm.value.developer_id) return 'Unassigned';
    const dev = developers.value.find((d) => d.id === taskForm.value.developer_id);
    return dev ? `${dev.first_name} ${dev.middle_name || ''} ${dev.last_name}`.trim() : 'Unassigned';
});
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
    <div class="md:justify-center">
        <!-- Edit Form -->
        <form class="max-w-3xl">
            <div class="grid w-full grid-cols-1 gap-4 rounded-md md:grid-cols-2">
                <!-- Task Title -->
                <div class="md:col-span-2">
                    <label for="title" class="mb-2 block text-sm font-semibold text-gray-700">Task Title</label>
                    <input
                        type="text"
                        v-model="taskForm.title"
                        class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                        disabled
                    />
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="mb-2 block text-sm font-semibold text-gray-700">Description</label>
                    <textarea
                        v-model="taskForm.description"
                        class="w-full cursor-not-allowed resize-none rounded-md border-2 border-primary bg-gray-100 p-3"
                        rows="3"
                        disabled
                    ></textarea>
                </div>

                <!-- Assigned To -->
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Assigned To</label>
                    <input
                        type="text"
                        :value="getDeveloperName"
                        class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                        disabled
                        readonly
                    />
                </div>

                <!-- Priority Level -->
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Priority Level</label>
                    <select
                        v-model="taskForm.priority_level"
                        class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                        disabled
                    >
                        <option value="" disabled>-- Select --</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <div class="col-span-3 grid w-full gap-4 md:grid-cols-3">
                        <!-- Start Date -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700">Start Date</label>
                            <input
                                type="date"
                                v-model="taskForm.start_date"
                                class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                                disabled
                            />
                        </div>
                        <!-- Due Date -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700">Due Date</label>
                            <input
                                type="date"
                                v-model="taskForm.due_date"
                                class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                                disabled
                            />
                        </div>
                        <!-- Client -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700">Client Name</label>
                            <select
                                v-model="taskForm.client_id"
                                class="w-full cursor-not-allowed border-0 border-b-2 border-primary bg-gray-100"
                                disabled
                            >
                                <option value="" disabled>-- Select Client --</option>
                                <option v-for="client in clients" :key="client.id" :value="client.id">
                                    {{ client.first_name }} {{ client.last_name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Notes (Optional)</label>
                    <textarea
                        v-model="taskForm.notes"
                        class="w-full cursor-not-allowed resize-none rounded-md border-2 border-primary bg-gray-100 p-3"
                        rows="2"
                        placeholder="No notes provided..."
                        disabled
                    ></textarea>
                </div>

                <!-- Progress -->
                <div class="md:col-span-2">
                    <label class="mb-3 block text-sm font-semibold text-gray-700">Progress</label>
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
            </div>
        </form>
    </div>
</template>
