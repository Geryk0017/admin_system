<script setup>
import SubmitButton from '@/components/submitButton.vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { computed, onMounted, ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import Add_task_form from './add_task_form.vue';
import Edit_task from './edit_task.vue';
import View_task from './view_task.vue';

const router = useRouter();
const tasks = ref([]);
const loading = ref(false);
const searchQuery = ref('');
const selectedStatus = ref('all');
const due_date = ref('all');
const currentUser = ref(null);
const message = ref('');
const selectedTask = ref(null); // Changed to store entire task object
let messageTimeout = null;

const paginationTasks = ref({
    current_page: 1,
    last_page: 1,
    links: [],
});

onMounted(() => {
    const storedUser = JSON.parse(sessionStorage.getItem('user'));
    if (storedUser) {
        currentUser.value = storedUser;
        fetchTasks();
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

const fetchTasks = async (page = 1) => {
    loading.value = true;

    const params = {
        page,
        role: currentUser.value?.role,
        user_id: currentUser.value?.id,
        search: searchQuery.value || undefined,
        priority: selectedStatus.value !== 'all' ? selectedStatus.value : undefined,
        due_date: due_date.value !== 'all' ? due_date.value : undefined,
    };

    Object.keys(params).forEach((key) => params[key] === undefined && delete params[key]);

    try {
        const response = await axios.get('/api/add_task', { params });

        tasks.value = response.data.data;
        paginationTasks.value = {
            current_page: response.data.current_page,
            last_page: response.data.last_page,
            links: response.data.links,
        };
    } catch (error) {
        console.error('Error fetching tasks:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to load tasks.',
            icon: 'error',
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'OK',
        });
    } finally {
        loading.value = false;
    }
};

watch([selectedStatus, due_date, searchQuery], () => {
    fetchTasks(1);
});

const getPageFromUrl = (url) => {
    if (!url) return 1;
    const urlParams = new URLSearchParams(url.split('?')[1]);
    return urlParams.get('page') || 1;
};

const filteredTasks = computed(() => tasks.value);

const closeMessage = () => {
    message.value = '';
    if (messageTimeout) {
        clearTimeout(messageTimeout);
    }
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
};

const getPriorityClass = (priority) => {
    switch (priority?.toLowerCase()) {
        case 'high':
            return 'bg-red-100 text-red-700 px-3 py-1 rounded font-semibold';
        case 'medium':
            return 'bg-yellow-100 text-yellow-700 px-3 py-1 rounded font-semibold';
        case 'low':
            return 'bg-green-100 text-green-700 px-3 py-1 rounded font-semibold';
        default:
            return 'bg-gray-100 text-gray-700 px-3 py-1 rounded font-semibold';
    }
};

const canEditTask = (task) => {
    if (currentUser.value?.role === 'admin') {
        return true;
    }
    if (currentUser.value?.role === 'developer') {
        return task.developer?.id === currentUser.value.id;
    }
    return false;
};

const canDeleteTask = (task) => {
    return currentUser.value?.role === 'admin' || currentUser.value?.role === 'developer';
};

const confirmDelete = (task) => {
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete task "${task.title}". This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            handleDelete(task);
        }
    });
};

const handleDelete = async (task) => {
    try {
        await axios.delete(`/api/add_task/${task.id}`);

        fetchTasks(paginationTasks.value.current_page);

        Swal.fire({
            title: 'Deleted!',
            text: 'Task has been deleted successfully.',
            icon: 'success',
            confirmButtonColor: '#10b981',
            confirmButtonText: 'OK',
        });
    } catch (err) {
        Swal.fire({
            title: 'Error!',
            text: err.response?.data?.message || 'Failed to delete task.',
            icon: 'error',
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'OK',
        });
    }
};

const closeModal = () => {
    document.getElementById('my_modal_3')?.close();
};

const closeEditModal = () => {
    document.getElementById('my_modal_4')?.close();
};

const closeViewModal = () => {
    document.getElementById('my_modal_5')?.close();
};

const handleSuccess = () => {
    fetchTasks(paginationTasks.value.current_page);
};

const editModalTask = (task) => {
    selectedTask.value = task; // Store entire task object
    document.getElementById('my_modal_4')?.showModal();
};

const viewModalTask = (task) => {
    selectedTask.value = task; // Store entire task object
    document.getElementById('my_modal_5')?.showModal();
};
</script>

<template>
    <div class="m-5">
        <div
            v-if="message"
            class="mb-5 flex items-center justify-between rounded p-3"
            :class="message.includes('Success') || message.includes('success') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
        >
            <span>{{ message }}</span>
            <button @click="closeMessage" class="ml-4 text-lg font-bold transition hover:opacity-70">&times;</button>
        </div>

        <div v-if="currentUser?.status !== 'active'">
            <div class="flex h-screen items-center justify-center">
                <div class="w-full max-w-md rounded-lg bg-white p-8 text-center shadow-lg">
                    <h2 class="mb-4 text-2xl font-bold text-red-600">Account Inactive</h2>
                    <p class="mb-6 text-gray-700">Your account is currently inactive. You cannot access this page until your account is activated.</p>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="my-5 justify-between gap-4 space-y-3 md:flex md:space-y-0">
                <div class="flex items-center gap-4">
                    <h1 class="text-center text-xl font-bold md:text-left md:text-2xl">
                        {{ currentUser?.role === 'developer' ? 'My Tasks' : 'All Tasks' }}
                    </h1>
                    <div class="flex w-full items-center gap-2 md:w-auto">
                        <input
                            type="text"
                            class="h-[50px] w-full rounded-md border border-gray-300 px-3 text-sm focus:ring-2 focus:ring-blue-500 md:w-[250px]"
                            v-model="searchQuery"
                            placeholder="Search tasks..."
                        />
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="my-5 w-full gap-4 space-y-5 md:flex md:space-y-0">
                        <!-- FILTER BY DUE DATE -->
                        <div class="flex items-center gap-2 md:px-5">
                            <div class="flex-1">
                                <label class="w-full text-sm font-medium text-gray-700"> Filter by Due date: </label>
                            </div>

                            <div class="gap-2 md:flex">
                                <div>
                                    <input
                                        type="date"
                                        class="w-[150px] rounded-lg border border-gray-300 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500"
                                        v-model="due_date"
                                    />
                                </div>
                                <div class="flex items-center">
                                    <span class="hidden text-sm text-gray-600 md:block"> ({{ filteredTasks.length }} results) </span>
                                </div>
                            </div>
                        </div>

                        <!-- FILTER BY PRIORITY -->
                        <div class="flex items-center gap-2 md:px-5">
                            <div class="flex-1">
                                <label class="w-full text-sm font-medium text-gray-700"> Filter by Priority: </label>
                            </div>

                            <div class="gap-2 md:flex">
                                <div>
                                    <select
                                        v-model="selectedStatus"
                                        class="w-[150px] rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value="all">All Task</option>
                                        <option value="high">High</option>
                                        <option value="medium">Medium</option>
                                        <option value="low">Low</option>
                                    </select>
                                </div>
                                <div class="flex items-center">
                                    <span class="hidden text-sm text-gray-600 md:block"> ({{ filteredTasks.length }} results) </span>
                                </div>
                            </div>
                        </div>
                        <!-- ADD TASK BUTTON -->
                        <div>
                            <SubmitButton
                                v-if="currentUser?.role === 'admin' || currentUser?.role === 'developer'"
                                onclick="my_modal_3.showModal()"
                                bt="+ Add Task"
                                class="w-[100%] rounded-md bg-green-500 transition duration-300 ease-in-out hover:bg-green-700 md:w-[150px]"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto whitespace-nowrap">
                <table class="w-full border border-gray-200 bg-white text-gray-500">
                    <thead class="border-b border-gray-200 bg-table-header">
                        <tr>
                            <th class="px-6 py-3">Task ID</th>
                            <th class="px-6 py-3">Task Title</th>
                            <th class="px-6 py-3" v-if="currentUser?.role !== 'developer'">Assigned To</th>
                            <th class="px-6 py-3">Priority Level</th>
                            <th class="px-6 py-3">Start Date</th>
                            <th class="px-6 py-3">Due Date</th>
                            <th class="px-6 py-3">Progress</th>
                            <th class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <tr v-if="loading">
                            <td :colspan="currentUser?.role === 'developer' ? 7 : 8" class="py-10 text-gray-500">
                                <span class="skeleton skeleton-text">Loading Task...</span>
                            </td>
                        </tr>
                        <tr v-else-if="!loading && filteredTasks.length === 0">
                            <td :colspan="currentUser?.role === 'developer' ? 7 : 8" class="py-10 text-gray-500">
                                {{ currentUser?.role === 'developer' ? 'No tasks assigned to you.' : 'No tasks found.' }}
                            </td>
                        </tr>
                        <tr v-else v-for="task in filteredTasks" :key="task.id" class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-bold text-primary">
                                {{ task.task_id || `#${task.id}` }}
                            </td>
                            <td class="px-6 py-3 font-semibold text-gray-800">
                                {{ task.title }}
                            </td>
                            <td class="px-6 py-3" v-if="currentUser?.role !== 'developer'">
                                {{ task.developer?.first_name + ' ' + task.developer?.last_name || 'Unassigned' }}
                            </td>
                            <td class="px-6 py-3">
                                <span :class="getPriorityClass(task.priority_level)">
                                    {{ task.priority_level.charAt(0).toUpperCase() + task.priority_level.slice(1) }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                {{ formatDate(task.start_date) }}
                            </td>
                            <td class="px-6 py-3">
                                {{ formatDate(task.due_date) }}
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="h-4 w-[200px] overflow-hidden rounded-full bg-blue-500 shadow-inner">
                                        <div
                                            class="animate-stripes h-full bg-[length:30px_30px] transition-all duration-500"
                                            :style="{ width: task.progress + '%' }"
                                        ></div>
                                    </div>
                                    <span class="min-w-[35px] text-sm font-semibold">{{ task.progress }}%</span>
                                </div>
                            </td>
                            <td class="space-x-3 px-6 py-3">
                                <button @click="viewModalTask(task)" class="cursor-pointer text-secondary hover:underline">View</button>

                                <button
                                    v-if="canEditTask(task) && task.progress < 100"
                                    @click="editModalTask(task)"
                                    class="cursor-pointer text-primary hover:underline"
                                >
                                    {{ currentUser?.role === 'developer' ? 'Update Progress' : 'Edit' }}
                                </button>

                                <button v-if="canDeleteTask(task)" @click="confirmDelete(task)" class="cursor-pointer text-red-500 hover:underline">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- PAGINATION -->
                <div v-if="paginationTasks.links.length > 0" class="mt-4 flex justify-end gap-2">
                    <button
                        v-for="(link, index) in paginationTasks.links"
                        :key="index"
                        @click="link.url && fetchTasks(getPageFromUrl(link.url))"
                        :disabled="!link.url || link.active"
                        v-html="link.label"
                        class="rounded border px-3 py-1"
                        :class="[
                            link.active ? 'bg-primary text-white' : 'bg-white hover:bg-gray-100',
                            !link.url ? 'cursor-not-allowed opacity-50' : 'cursor-pointer',
                        ]"
                    ></button>
                </div>
            </div>
        </div>

        <!-- ADD TASK MODAL -->
        <dialog id="my_modal_3" class="modal">
            <div class="modal-box max-w-3xl">
                <form method="dialog">
                    <button class="btn absolute top-2 right-2 btn-circle btn-ghost btn-sm">✕</button>
                </form>
                <h3 class="mb-4 text-lg font-bold">Add Task</h3>
                <div class="py-4">
                    <Add_task_form @close="closeModal" @task-added="handleSuccess" />
                </div>
            </div>
        </dialog>

        <!-- EDIT TASK MODAL -->
        <dialog id="my_modal_4" class="modal">
            <div class="modal-box max-w-3xl">
                <form method="dialog">
                    <button class="btn absolute top-2 right-2 btn-circle btn-ghost btn-sm">✕</button>
                </form>
                <h3 class="mb-4 text-lg font-bold">Edit Task {{ selectedTask?.task_id }}</h3>
                <div class="py-4">
                    <Edit_task
                        v-if="selectedTask"
                        :key="selectedTask.id"
                        :task="selectedTask"
                        @task-updated="handleSuccess"
                        @close="closeEditModal"
                    />
                </div>
            </div>
        </dialog>

        <!-- VIEW TASK MODAL -->
        <dialog id="my_modal_5" class="modal">
            <div class="modal-box max-w-3xl">
                <form method="dialog">
                    <button class="btn absolute top-2 right-2 btn-circle btn-ghost btn-sm">✕</button>
                </form>
                <h3 class="mb-4 text-lg font-bold">View Task {{ selectedTask?.task_id }}</h3>
                <div class="py-4">
                    <View_task
                        v-if="selectedTask"
                        :key="selectedTask.id"
                        :task="selectedTask"
                        @task-updated="handleSuccess"
                        @close="closeViewModal"
                    />
                </div>
            </div>
        </dialog>
    </div>
</template>
