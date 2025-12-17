<script setup>
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';

const props = defineProps({
    to: {
        type: String,
        default: ''
    }
});

const route = useRoute();

const backTo = computed(() => {
    if (props.to) return props.to;
    if (route.path.startsWith('/verifier')) return '/verifier/dashboard';
    if (route.path.startsWith('/admin')) return '/admin/dashboard';
});
</script>

<template>
    <button class="bg-red-500 text-white h-[50px] w-full md:!w-[250px] text-center"> <RouterLink :to="backTo">Cancel</RouterLink> </button>
</template>