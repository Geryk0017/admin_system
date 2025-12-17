<script setup>
const props = defineProps({
    form: Object,
    errors: Object,
});
</script>

<template>
    <div class="p-5">
        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="my-5 md:my-0">
                <label for="first_name" class="mb-2 block text-sm font-semibold text-gray-700">
                    First Name <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    class="h-[50px] w-full cursor-not-allowed border-0 border-b transition focus:border-primary focus:outline-none"
                    :class="errors.first_name ? 'border-red-500' : 'border-primary'"
                    name="first_name"
                    required
                    v-model="form.first_name"
                    disabled
                />
                <p v-if="errors.first_name" class="mt-1 text-sm text-red-500">{{ errors.first_name[0] }}</p>
            </div>
            <div class="my-5 md:my-0">
                <label for="middle_name" class="mb-2 block text-sm font-semibold text-gray-700"> Middle Name </label>
                <input
                    type="text"
                    class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                    :class="errors.middle_name ? 'border-red-500' : 'border-primary'"
                    name="middle_name"
                    v-model="form.middle_name"
                />
                <p v-if="errors.middle_name" class="mt-1 text-sm text-red-500">{{ errors.middle_name[0] }}</p>
            </div>
            <div class="my-5 md:my-0">
                <label for="last_name" class="mb-2 block text-sm font-semibold text-gray-700"> Last Name <span class="text-red-500">*</span></label>
                <input
                    type="text"
                    class="h-[50px] w-full cursor-not-allowed border-0 border-b transition focus:border-primary focus:outline-none"
                    :class="errors.last_name ? 'border-red-500' : 'border-primary'"
                    name="last_name"
                    v-model="form.last_name"
                    required
                    disabled
                />
                <p v-if="errors.last_name" class="mt-1 text-sm text-red-500">{{ errors.last_name[0] }}</p>
            </div>
            <div class="my-5 md:my-0">
                <label for="gender" class="mb-2 block text-sm font-semibold text-gray-700"> Gender <span class="text-red-500">*</span> </label>
                <select
                    name="gender"
                    class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                    :class="errors.gender ? 'border-red-500' : 'border-primary'"
                    v-model="form.gender"
                    required
                >
                    <option value="" selected disabled>-- Select --</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
                <p v-if="errors.gender" class="mt-1 text-sm text-red-500">{{ errors.gender[0] }}</p>
            </div>
            <div class="col-span-2 my-5 md:my-0">
                <label for="birthdate" class="mb-2 block text-sm font-semibold text-gray-700">
                    Date of Birth <span class="text-red-500">*</span>
                </label>
                <input
                    type="date"
                    class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                    name="birthdate"
                    :class="errors.birthdate ? 'border-red-500' : 'border-primary'"
                    required
                    v-model="form.birthdate"
                />
                <p v-if="errors.birthdate" class="mt-1 text-sm text-red-500">{{ errors.birthdate[0] }}</p>
            </div>
            <div class="col-span-2 my-5 md:my-0">
                <label for="email" class="mb-2 block text-sm font-semibold text-gray-700"> Email Address <span class="text-red-500">*</span></label>
                <input
                    type="email"
                    class="h-[50px] w-full cursor-not-allowed border-0 border-b transition focus:border-primary focus:outline-none"
                    name="email"
                    :class="errors.email ? 'border-red-500' : 'border-primary'"
                    v-model="form.email"
                    required
                    placeholder="Enter email address"
                    disabled
                />
                <p v-if="errors.email" class="mt-1 text-sm text-red-500">{{ errors.email[0] }}</p>
            </div>
            <div class="my-5 md:my-0">
                <label for="phone" class="mb-2 block text-sm font-semibold text-gray-700"> Mobile Number <span class="text-red-500">*</span></label>
                <input
                    type="text"
                    class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                    placeholder="Must start with 09 or +63"
                    name="phone"
                    :class="errors.phone ? 'border-red-500' : 'border-primary'"
                    v-model="form.phone"
                    required
                />
                <p v-if="errors.phone" class="mt-1 text-sm text-red-500">{{ errors.phone[0] }}</p>
            </div>
            <div class="col-span-3 my-5 md:my-0">
                <label for="last_name" class="mb-2 block text-sm font-semibold text-gray-700"> Address <span class="text-red-500">*</span></label>
                <input
                    type="text"
                    class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                    :class="errors.address ? 'border-red-500' : 'border-primary'"
                    name="address"
                    v-model="form.address"
                    required
                    placeholder="Enter full address"
                />
                <p v-if="errors.address" class="mt-1 text-sm text-red-500">{{ errors.address[0] }}</p>
            </div>
        </div>
    </div>
</template>
