<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';
import ApexCharts from 'vue3-apexcharts';

const donutOptions = ref({
    labels: ['Completed', 'In Progress', 'Overdue'],
    colors: ['#10b981', '#3b82f6', '#ef4444'],
    legend: {
        position: 'bottom',
    },
    responsive: [
        {
            breakpoint: 480,
            options: {
                chart: {
                    width: 300,
                },
                legend: {
                    position: 'bottom',
                },
            },
        },
    ],
});

const lineSeries = ref([{ name: 'Progress', data: [] }]);
const donutSeries = ref([0, 0, 0]);

const lineOptions = ref({
    chart: {
        type: 'line',
        zoom: { enabled: false },
        toolbar: { show: true },
    },
    xaxis: {
        categories: [],
        title: { text: 'Week' },
    },
    yaxis: {
        min: 0,
        max: 100,
        title: { text: 'Average Progress (%)' },
    },
    stroke: {
        curve: 'smooth',
        width: 3,
    },
    colors: ['#3b82f6'],
    markers: { size: 5 },
    tooltip: {
        y: {
            formatter: (val) => val + '%',
        },
    },
    dataLabels: {
        enabled: false,
    },
});

onMounted(() => {
    progressChart();
    weeklyChart();
});

function getWeekNumber(date) {
    const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
    const dayNum = d.getUTCDay() || 7;
    d.setUTCDate(d.getUTCDate() + 4 - dayNum);
    const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
    const weekNo = Math.ceil(((d - yearStart) / 86400000 + 1) / 7);
    return weekNo;
}

async function fetchAllTasks() {
    const storedUser = JSON.parse(sessionStorage.getItem('user'));
    if (!storedUser) return [];

    try {
        const response = await axios.get('/api/tasks/chart', {
            params: {
                role: storedUser.role,
                user_id: storedUser.id,
            },
        });
        return response.data || [];
    } catch (error) {
        console.error('Error fetching tasks:', error);
        return [];
    }
}

async function weeklyChart() {
    const tasks = await fetchAllTasks();

    if (tasks.length === 0) {
        console.log('No tasks found');
        lineOptions.value.xaxis.categories = ['No Data'];
        lineSeries.value[0].data = [0];
        return;
    }

    // Group tasks by week
    const weekMap = new Map();

    tasks.forEach((task) => {
        if (!task.due_date) return;

        const date = new Date(task.due_date);
        const year = date.getFullYear();
        const weekNumber = getWeekNumber(date);
        const weekLabel = `Week ${weekNumber} (${year})`;

        if (!weekMap.has(weekLabel)) {
            weekMap.set(weekLabel, []);
        }
        weekMap.get(weekLabel).push(task.progress || 0);
    });

    if (weekMap.size === 0) {
        console.log('No valid tasks with due dates');
        lineOptions.value.xaxis.categories = ['No Data'];
        lineSeries.value[0].data = [0];
        return;
    }

    // Sort weeks chronologically
    const sortedWeeks = Array.from(weekMap.entries()).sort((a, b) => {
        const matchA = a[0].match(/Week (\d+) \((\d+)\)/);
        const matchB = b[0].match(/Week (\d+) \((\d+)\)/);

        if (!matchA || !matchB) return 0;

        const [, weekA, yearA] = matchA;
        const [, weekB, yearB] = matchB;

        return yearA !== yearB ? yearA - yearB : weekA - weekB;
    });

    // Calculate average progress per week
    const categories = sortedWeeks.map(([label]) => label);
    const data = sortedWeeks.map(([_, progressArr]) => {
        const avg = progressArr.reduce((sum, p) => sum + p, 0) / progressArr.length;
        return Math.round(avg);
    });

    lineOptions.value.xaxis.categories = categories;
    lineSeries.value[0].data = data;

    console.log('Weekly chart loaded:', { weeks: categories.length, data });
}

async function progressChart() {
    const tasks = await fetchAllTasks();

    if (tasks.length === 0) {
        console.log('No tasks found');
        return;
    }

    const now = new Date();

    const completed = tasks.filter((t) => t.progress === 100).length;
    const inProgress = tasks.filter((t) => t.progress > 0 && t.progress < 100 && new Date(t.due_date) >= now).length;
    const overdue = tasks.filter((t) => t.progress < 100 && new Date(t.due_date) < now).length;

    donutSeries.value = [completed, inProgress, overdue];

    console.log('Progress chart loaded:', { completed, inProgress, overdue });
}
</script>
<template>
    <div class="mb-5 w-full p-5">
        <div class="grid gap-5 md:grid-cols-4">
            <!-- Donut Chart - Task Status -->
            <div class="h-[400px] w-full overflow-hidden rounded-md bg-white drop-shadow-md md:col-span-2 md:h-[600px]">
                <div class="flex h-full w-full flex-col p-5 md:p-10">
                    <h3 class="mb-3 text-lg font-bold text-gray-700">Task Status Overview</h3>
                    <div class="min-h-0 flex-1">
                        <ApexCharts type="donut" :options="donutOptions" :series="donutSeries" width="100%" height="100%" />
                    </div>
                </div>
            </div>

            <!-- Line Chart - Weekly Progress -->
            <div class="h-[400px] w-full overflow-hidden rounded-md bg-white drop-shadow-md md:col-span-2 md:h-[600px]">
                <div class="flex h-full w-full flex-col p-5 md:p-10">
                    <h3 class="mb-3 text-lg font-bold text-gray-700">Weekly Progress Trend</h3>
                    <div class="min-h-0 flex-1">
                        <ApexCharts type="line" :options="lineOptions" :series="lineSeries" width="100%" height="100%" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
