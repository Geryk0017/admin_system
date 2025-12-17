<script setup>
import { ref } from 'vue'

defineProps({
    bt: {
        type: String,
        default: 'Submit'
    }
})
</script>


<template>
    <button class="bg-primary h-[50px] text-white font-semibold cursor-pointer"> {{ bt }} </button>
</template>