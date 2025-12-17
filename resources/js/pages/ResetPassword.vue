<script setup>
import axios from 'axios'
import { ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import Swal from 'sweetalert2'

const router = useRouter()

const form = ref({
    email: '',
    old_password: '',
    new_password: '',
    retype_password: '',
})

const message = ref('')
const errors = ref({})

const resetPassword = () => {
  errors.value = {}
  message.value = ''
  
  axios.post('/api/reset-password', form.value)
    .then(res => {
      Swal.fire({
                title: 'Success!',
                text: res.data.message || 'Loan Application Submitted Successfully',
                icon: 'success',
                confirmButtonColor: '#10b981',
                confirmButtonText: 'OK'
            })
      
      // Clear form
      form.value = {
        email: '',
        old_password: '',
        new_password: '',
        retype_password: '',
      }
      
      // Redirect to login after 2 seconds
      setTimeout(() => {
        emit('switchToLogin')
      }, 2000)
    })
    .catch(err => {
      if (err.response?.status === 422) {
        Swal.fire({
            title: 'Validation Error',
            text: err.response?.data?.message || 'Please check the form for errors.',
            icon: 'error',
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'OK'
        })
        errors.value = err.response.data.errors
      } else if (err.response?.status === 401) {
        message.value = err.response.data.error || 
        Swal.fire({
            title: 'Validation Error',
            text: err.response?.data?.message || 'Please check the form for errors.',
            icon: 'error',
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'OK'
        })
      } else {
        message.value = err.response?.data?.message ||
        Swal.fire({
            title: 'Validation Error',
            text: err.response?.data?.message || 'Please check the form for errors.',
            icon: 'error',
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'OK'
        })
      }
    })
}
</script>

<template>
    <div class="w-full h-[800px] flex justify-center items-center">
        <div class="w-[500px] bg-white">
            <!-- HEADER -->
            <div class="px-5 leading-[100px] flex justify-center h-[100px]">
                <header>
                    <h1 class="text-[25px]">Reset Password</h1>
                </header>
            </div>
            <!-- RESET PASSWORD FORM -->
            <div class="m-5 my-[10%]">
                <form @submit.prevent="resetPassword">
                    <div class="my-5 mx-5">
                        <label for="email">Email:</label> <br>
                        <input
                            type="text"
                            class="w-full h-[30px] border-0 border-b focus:border-2 focus:ring-0"
                            :class="errors.email ? 'border-red-500' : 'border-primary'"
                            v-model="form.email"
                        >
                        <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email[0] }}</p>
                    </div>
                    <div class="my-5 mx-5">
                        <label for="old_password">Old Password:</label> <br>
                        <input
                            type="password"
                            class="w-full h-[30px] border-0 border-b focus:border-2 focus:ring-0"
                            :class="errors.old_password ? 'border-red-500' : 'border-primary'"
                            v-model="form.old_password"
                        >
                        <p v-if="errors.old_password" class="text-red-500 text-sm mt-1">{{ errors.old_password[0] }}</p>
                    </div>
                    <div class="my-5 mx-5">
                        <label for="new_password">New Password:</label> <br>
                        <input
                            type="password"
                            class="w-full h-[30px] border-0 border-b focus:border-2 focus:ring-0"
                            :class="errors.new_password ? 'border-red-500' : 'border-primary'"
                            v-model="form.new_password"
                        >
                        <p v-if="errors.new_password" class="text-red-500 text-sm mt-1">{{ errors.new_password[0] }}</p>
                    </div>
                    <div class="my-5 mx-5">
                        <label for="retype_password">Confirm Password:</label> <br>
                        <input
                            type="password"
                            class="w-full h-[30px] border-0 border-b focus:border-2 focus:ring-0"
                            :class="errors.retype_password ? 'border-red-500' : 'border-primary'"
                            v-model="form.retype_password"
                        >
                        <p v-if="errors.retype_password" class="text-red-500 text-sm mt-1">{{ errors.retype_password[0] }}</p>
                    </div>
                    <div class="w-full px-5 py-5">
                        <button class="w-full bg-primary text-white h-[40px] hover:opacity-90 transition cursor-pointer" type="submit">Reset Password</button>
                    </div>
                </form>
                <div class="my-5 mx-5 flex justify-center">
                    <p class="px-1">Remember your password?</p>
                    <RouterLink to="/" class="text-secondary font-bold cursor-pointer">Sign In</RouterLink>
                </div>
            </div>
        </div>
    </div>
</template>