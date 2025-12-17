<script setup>
const props = defineProps({
    form: Object,
    errors: Object,
    salaryFromFormatted: String,
    salaryToFormatted: String,
    formatSalaryFrom: Function,
    formatSalaryTo: Function,
});

const emit = defineEmits(['update:form']);

const updateForm = (field, value) => {
    emit('update:form', { ...props.form, [field]: value });
};
</script>

<template>
    <div class="p-5">
        <div class="my-5">
            <label for="income_source" class="mb-2 block text-sm font-semibold text-gray-700">
                Source of Income <span class="text-red-500">*</span></label
            >
            <select
                name="income_source"
                class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                :class="errors.income_source ? 'border-red-500' : 'border-primary'"
                :value="form.income_source"
                @input="updateForm('income_source', $event.target.value)"
                required
            >
                <option value="" selected disabled>-- Select --</option>
                <option value="employment">Employment</option>
                <option value="self employed">Self Employed</option>
                <option value="freelance">Freelance</option>
                <option value="business">Business</option>
                <option value="allowance">Allowance</option>
                <option value="unemployed">Unemployed</option>
                <option value="others">Others</option>
            </select>
            <p v-if="errors.income_source" class="mt-1 text-sm text-red-500">{{ errors.income_source[0] }}</p>
        </div>

        <div class="my-5">
            <label for="occupation" class="mb-2 block text-sm font-semibold text-gray-700"> Occupation <span class="text-red-500">*</span></label>
            <input
                type="text"
                class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                name="occupation"
                :class="errors.occupation ? 'border-red-500' : 'border-primary'"
                v-model="form.occupation"
                @input="updateForm('occupation', $event.target.value)"
                required
            />
            <p v-if="errors.occupation" class="mt-1 text-sm text-red-500">{{ errors.occupation[0] }}</p>
        </div>

        <div class="my-5">
            <label for="salary_range_from" class="mb-2 block text-sm font-semibold text-gray-700">
                Salary Range from <span class="text-red-500">*</span></label
            >
            <input
                type="text"
                class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                name="salary_range_from"
                :class="errors.salary_range_from ? 'border-red-500' : 'border-primary'"
                :value="salaryFromFormatted"
                @input="formatSalaryFrom"
                required
            />
            <p v-if="errors.salary_range_from" class="mt-1 text-sm text-red-500">{{ errors.salary_range_from[0] }}</p>
        </div>

        <div class="my-5 md:my-0">
            <label for="salary_range_to" class="mb-2 block text-sm font-semibold text-gray-700">
                Salary Range to <span class="text-red-500">*</span></label
            >
            <input
                type="text"
                class="h-[50px] w-full border-0 border-b transition focus:border-primary focus:outline-none"
                name="salary_range_to"
                :class="errors.salary_range_to ? 'border-red-500' : 'border-primary'"
                :value="salaryToFormatted"
                @input="formatSalaryTo"
                required
            />
            <p v-if="errors.salary_range_to" class="mt-1 text-sm text-red-500">{{ errors.salary_range_to[0] }}</p>
        </div>
    </div>
</template>
