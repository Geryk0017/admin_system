<script setup>
import Dev_metrics from '@/components/developer/dev_metrics.vue';
import charts from '@/components/developer/charts.vue';
import { ref, onMounted } from 'vue'

const currentUser = ref(null)

onMounted(() => {
    // Get logged-in user from sessionStorage
    const storedUser = JSON.parse(sessionStorage.getItem('user'))
    if (storedUser) {
        currentUser.value = storedUser
        console.log(currentUser.value.status)
    } else {
        Swal.fire({
            title: 'Error!',
            text: 'Please log in first.',
            icon: 'error',
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'OK'
        }).then(() => {
            router.push({ name: 'login' })
        })
    }
})

</script>


<template>
    <div v-if="currentUser?.status !== 'active'" >
            <div class="flex items-center justify-center h-screen">
                <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full text-center"> 
                    <h2 class="text-2xl font-bold mb-4 text-red-600">Account Inactive</h2>
                    <p class="mb-6 text-gray-700">
                        Your account is currently inactive. You cannot access this page until your account is activated.
                    </p>
                </div>
            </div>
        </div>
    <div v-else>
        <Dev_metrics />
        <charts />
    </div>
</template>


