<script setup>
import { Hourglass, BadgeCheck, FileSearch, CircleX, User, ThumbsUp, ThumbsDown } from 'lucide-vue-next';
import { ref, onMounted } from 'vue'
import axios from 'axios'

const stats = ref({
  pending: 0,
  verified: 0,
  under_review: 0,
  approved: 0,
  rejected: 0,
})

const userCount = ref({
    client: 0
})

onMounted(() => {
  axios.get('/api/application-stats')
    .then(response => {
      stats.value = response.data
    })
    .catch(error => {
      console.error('Error fetching stats:', error)
    })
})

onMounted(() => {
  axios.get('/api/users/count')
    .then(res => {
      userCount.value = res.data
    })
    .catch(err => console.error(err))
})
</script>

<template>
<div class="m-5 grid md:grid-cols-3 md:grid-rows-2 gap-5 h-[550px] overflow-y-auto">
    <!-- Pending -->
    <div class="w-full h-[250px] bg-yellow-100 drop-shadow-md flex items-center justify-center gap-5 rounded-xl">
      <div>
        <Hourglass :size="75" class="text-yellow-700"/>
      </div>
      <div class="text-yellow-700">
        <h1 class="text-center font-semibold text-[25px]">{{ stats.pending }}</h1>
        <h1>Pending</h1>
      </div>
    </div>

    <!-- Verified -->
    <div class="w-full h-[250px] bg-blue-100 drop-shadow-md flex items-center justify-center gap-5 rounded-xl">
      <div>
        <BadgeCheck :size="75" class="text-blue-700"/>
      </div>
      <div class="text-blue-700">
        <h1 class="text-center font-semibold text-[25px]">{{ stats.verified }}</h1>
        <h1>Verified</h1>
      </div>
    </div>

    <!-- Under Review -->
    <div class="w-full h-[250px] bg-gray-100 drop-shadow-md flex items-center justify-center gap-5 rounded-xl">
      <div>
        <FileSearch :size="75" class="text-gray-700"/>
      </div>
      <div class="text-gray-700">
        <h1 class="text-center font-semibold text-[25px]">{{ stats.under_review }}</h1>
        <h1>Under Review</h1>
      </div>
    </div>

    <!-- Rejected -->
    <div class="w-full h-[250px] bg-red-100 drop-shadow-md flex items-center justify-center gap-5 rounded-xl">
      <div>
        <CircleX :size="75" class="text-red-700"/>
      </div>
      <div class="text-red-700">
        <h1 class="text-center font-semibold text-[25px]">{{ stats.rejected }}</h1>
        <h1>Rejected</h1>
      </div>
    </div>

    <div class="w-full h-[250px] bg-green-100 drop-shadow-md flex items-center justify-center gap-5 rounded-xl">
      <div>
        <ThumbsUp :size="75" class="text-green-700"/>
      </div>
      <div class="text-green-700">
        <h1 class="text-center font-semibold text-[25px]">{{ stats.approved }}</h1>
        <h1>Approved</h1>
      </div>
    </div>

    <div class="w-full h-[250px] bg-white drop-shadow-md flex items-center justify-center gap-5 rounded-xl">
      <div>
        <User :size="75"/>
      </div>
      <div>
        <h1 class="text-center font-semibold text-[25px]">{{ userCount.client }}</h1>
        <h1>Total number of Clients</h1>
      </div>
    </div>

</div>
</template>
