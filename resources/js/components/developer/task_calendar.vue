<script setup>
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import FullCalendar from '@fullcalendar/vue3';
import axios from 'axios';
import Swal from 'sweetalert2';
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const tasks = ref([]);
const due_date = ref('all');
const currentUser = ref(null);
const searchQuery = ref('');
const selectedStatus = ref('all');
const loading = ref(false);

const screenWidth = ref(window.innerWidth);

window.addEventListener('resize', () => {
    screenWidth.value = window.innerWidth;
});

const filteredTasks = computed(() => {
    return tasks.value.filter((task) => {
        const matchesSearch =
            !searchQuery.value ||
            task.task_id?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            task.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            (task.developer?.first_name &&
                (task.developer.first_name + ' ' + task.developer.last_name).toLowerCase().includes(searchQuery.value.toLowerCase()));

        const matchesStatus = selectedStatus.value === 'all' || task.priority_level.toLowerCase() === selectedStatus.value.toLowerCase();

        const matchDueDate = due_date.value === 'all' || (task.due_date && task.due_date.startsWith(due_date.value));

        return matchesSearch && matchesStatus && matchDueDate;
    });
});

onMounted(() => {
    const storedUser = JSON.parse(sessionStorage.getItem('user'));
    if (storedUser) {
        currentUser.value = storedUser;
        fetchTasks();
    } else {
        Swal.fire({ title: 'Error!', text: 'Please log in first.', icon: 'error' }).then(() => {
            router.push({ name: 'login' });
        });
    }
});

const fetchTasks = async () => {
    loading.value = true;
    try {
        const params = {
            role: currentUser.value?.role,
            user_id: currentUser.value?.id,
            per_page: 1000, // Get all tasks for calendar
        };

        const response = await axios.get('/api/add_task', { params });

        // Handle paginated response
        tasks.value = response.data.data || [];

        console.log('Calendar tasks loaded:', tasks.value.length);
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

// Convert tasks → calendar events
const calendarEvents = computed(() => {
    return filteredTasks.value.map((task) => ({
        id: task.task_id,
        title: `(${task.task_id}) ${task.title}`,
        date: task.due_date,
        backgroundColor: task.priority_level === 'high' ? '#ef4444' : '#3b82f6',
        borderColor: 'transparent',
    }));
});

// Display tasks to calendar
const calendarOptions = computed(() => ({
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    events: calendarEvents.value,
    height: screenWidth.value < 768 ? 250 : 560,
    expandRows: true,
}));
</script>
<template>
    <div>
        <FullCalendar :options="calendarOptions" />
    </div>
</template>
