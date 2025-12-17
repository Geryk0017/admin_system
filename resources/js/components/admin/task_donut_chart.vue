<script setup> 
import { ref, onMounted } from 'vue' 
import ApexCharts from 'vue3-apexcharts' 
import charts from '../developer/charts.vue'
import axios from 'axios' 

// const donutOptions = ref({ 
//     labels: ['Completed', 'In Progress', 'Overdue'], }) 
//     const donutSeries = ref([0,0,0]) 
    
// onMounted(async () => { 
//     const response = await axios.get('/api/add_task') 
//     const tasks = response.data 
    
//     donutSeries.value = [ tasks.filter(t => t.progress === 100).length, tasks.filter(t => t.progress >= 0 && t.progress < 100).length, tasks.filter(t => t.progress < 100 && new Date(t.due_date) < new Date()).length ] }) 

</script>
<template> 
    <charts />
</template>