<script setup>
import SignInForm from '@/components/SignInForm.vue';
import loginLogo from '../../assets/account-creation-bg.png';
import { ref } from 'vue';
import SignUpForm from '@/components/SignUpForm.vue';

const isSwapped = ref(false)

const switchToSignUp = () => {
    isSwapped.value = true
}

const switchToSignIn = () => {
    isSwapped.value = false
}
</script>

<template>
    <div class="w-full h-screen absolute top-0 flex justify-center items-center">
        <div class="w-full h-screen hidden md:block">
            <div class="h-full">
                <img v-if="isSwapped" :src="loginLogo" class="w-full h-full">
                <SignInForm v-else @switch-to-sign-up="switchToSignUp"/>
            </div>
        </div>
        
        <div class="w-full h-screen">
            <SignUpForm v-if="isSwapped" @switch-to-sign-in="switchToSignIn"/>
            <img v-else :src="loginLogo" class="w-full h-full">
        </div>
    </div>
</template>