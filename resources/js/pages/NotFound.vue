<script setup>
import SubmitButton from '@/components/submitButton.vue';
import NotFound from '../../assets/notfound.png'
import { useRouter } from 'vue-router';

const router = useRouter()

const goBack = () => {
    router.back()
}
</script>

<template>
    <div class="absolute top-0 flex flex-col items-center justify-center w-full h-screen">
        <img :src="NotFound" class="bg-cover">
       <SubmitButton @click="goBack" bt="<- Back" class="w-[200px] rounded-md"/>
    </div>
</template>