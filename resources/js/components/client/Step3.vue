<script setup>
const props = defineProps({
    form: Object,
    errors: Object,
    salaryFromFormatted: String,
    salaryToFormatted: String,
    formatSalaryFrom: Function,
    formatSalaryTo: Function,
});
</script>

<template>
    <div>
        <header class="h-[50px] rounded-t-md bg-blue-500 text-center leading-[50px] text-white">
            <h1 class="text-[20px] font-semibold">Review your Application</h1>
        </header>

        <main class="space-y-3 p-5">
            <div class="md:grid md:grid-cols-2">
                <div>
                    <h1 class="py-5 text-[20px] font-bold uppercase">Personal Information</h1>
                    <section class="flex gap-3">
                        <label class="font-semibold"> First Name: </label>
                        <p>{{ form.first_name }}</p>
                    </section>
                    <section class="flex gap-3">
                        <label class="font-semibold"> Middle Name: </label>
                        <p v-if="form.middle_name !== ''">{{ form.middle_name }}</p>
                        <p v-else>N/A</p>
                    </section>
                    <section class="flex gap-3">
                        <label class="font-semibold"> Last Name: </label>
                        <p>{{ form.last_name }}</p>
                    </section>
                    <section class="flex gap-3">
                        <label class="font-semibold"> Gender: </label>
                        <p>{{ form.gender }}</p>
                    </section>
                    <section class="flex gap-3">
                        <label class="font-semibold"> Date of Birth: </label>
                        <p>{{ form.birthdate }}</p>
                    </section>
                    <section class="flex gap-3">
                        <label class="font-semibold"> Email: </label>
                        <p>{{ form.email }}</p>
                    </section>
                    <section class="flex gap-3">
                        <label class="font-semibold"> Mobile Number: </label>
                        <p>{{ form.phone }}</p>
                    </section>
                    <section class="flex gap-3">
                        <label class="font-semibold"> Address: </label>
                        <p>{{ form.address }}</p>
                    </section>
                </div>
                <div>
                    <h1 class="py-5 text-[20px] font-bold uppercase">Employment/Financial Info</h1>

                    <section class="flex gap-3">
                        <label class="font-semibold"> Source of Income: </label>
                        <p>{{ form.income_source }}</p>
                    </section>

                    <section class="flex gap-3">
                        <label class="font-semibold"> Occupation: </label>
                        <p>{{ form.occupation }}</p>
                    </section>

                    <section class="flex gap-3">
                        <label class="font-semibold"> Salary Range: </label>
                        <p>₱{{ salaryFromFormatted }}</p>
                    </section>

                    <section class="flex gap-3">
                        <label class="font-semibold"> Salary Range: </label>
                        <p>₱{{ salaryToFormatted }}</p>
                    </section>
                </div>
            </div>
        </main>
    </div>
</template>
