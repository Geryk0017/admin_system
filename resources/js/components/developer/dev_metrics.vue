<script setup>
import axios from 'axios';
import { CalendarClock, CalendarX, CircleCheckBig, ListTodo } from 'lucide-vue-next';
import Swal from 'sweetalert2';
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import Task_calendar from './task_calendar.vue';

const loading = ref(false);
const currentUser = ref(null);
const tasks = ref([]);
const now = new Date();
const router = useRouter();

// Computed stats for dashboard
const totalTasks = computed(() => tasks.value.length);
const completedTasks = computed(() => tasks.value.filter((t) => t.progress === 100).length);
const inProgressTasks = computed(() => tasks.value.filter((t) => t.progress >= 0 && t.progress < 100 && new Date(t.due_date) >= now).length);
const overdueTasks = computed(
    () =>
        tasks.value.filter((t) => {
            const dueDate = new Date(t.due_date);
            return t.progress < 100 && dueDate < new Date();
        }).length,
);

// Fetch tasks
const fetchTasks = async () => {
    loading.value = true;
    try {
        const params = {
            role: currentUser.value?.role,
            user_id: currentUser.value?.id,
            per_page: 1000, // Get all tasks for metrics
        };

        const response = await axios.get('/api/add_task', { params });

        // Handle paginated response
        tasks.value = response.data.data || [];

        console.log('Metrics tasks loaded:', tasks.value.length);
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

// Get logged-in user
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
</script>

<template>
    <div class="w-full">
        <div class="m-5 grid h-[250px] gap-5 overflow-y-auto md:h-[600px] md:grid-cols-4 md:grid-rows-2 md:overflow-y-hidden md:py-5">
            <div class="flex h-[250px] w-full items-center justify-center gap-5 rounded-xl bg-white drop-shadow-md md:h-full">
                <div>
                    <ListTodo :size="75" />
                </div>
                <div class="text-center">
                    <h1 class="text-[25px] font-semibold">{{ totalTasks }}</h1>
                    <h1>Total Task</h1>
                </div>
            </div>

            <div class="flex h-[250px] w-full items-center justify-center gap-5 rounded-xl bg-yellow-100 drop-shadow-md md:h-full">
                <div>
                    <CalendarClock :size="75" class="text-yellow-500" />
                </div>
                <div class="text-center text-yellow-500">
                    <h1 class="text-[25px] font-semibold">{{ inProgressTasks }}</h1>
                    <h1>Progress</h1>
                </div>
            </div>
            <div class="h-[250px] w-full gap-5 drop-shadow-md md:col-span-2 md:row-span-2 md:h-full">
                <Task_calendar />
            </div>

            <div class="flex h-[250px] w-full items-center justify-center gap-5 rounded-xl bg-green-100 drop-shadow-md md:h-full">
                <div>
                    <CircleCheckBig :size="75" class="text-green-700" />
                </div>
                <div class="text-center text-green-700">
                    <h1 class="text-[25px] font-semibold">{{ completedTasks }}</h1>
                    <h1>Completed</h1>
                </div>
            </div>

            <div class="flex h-[250px] w-full items-center justify-center gap-5 rounded-xl bg-red-100 drop-shadow-md md:h-full">
                <div>
                    <CalendarX :size="75" class="text-red-500" />
                </div>
                <div class="text-center text-red-500">
                    <h1 class="text-[25px] font-semibold">{{ overdueTasks }}</h1>
                    <h1>Overdue</h1>
                </div>
            </div>
        </div>
    </div>
</template>
