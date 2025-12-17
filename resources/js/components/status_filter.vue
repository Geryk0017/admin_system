<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: 'all'
  }
})

const emit = defineEmits(['update:modelValue', 'change'])

const selectedStatus = ref(props.modelValue)

// When user changes selection, update parent
const handleChange = () => {
  emit('update:modelValue', selectedStatus.value) // updates v-model in parent
}

</script>

<template>
  <select v-model="selectedStatus" @change="handleChange" class="px-4 py-2 border border-gray-300 rounded-lg">
    <option value="all">All Status</option>
    <option value="pending">Pending</option>
    <option value="under review">Under Review</option>
    <option value="verified">Verified</option>
    <option value="approved">Approved</option>
    <option value="rejected">Rejected</option>
  </select>
</template>
