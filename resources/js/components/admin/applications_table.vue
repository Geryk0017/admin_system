<script setup>
import View_form from '@/components/view_form.vue';
import { ref } from 'vue';
import Edit_form from '../edit_form.vue';

const selectedApplication = ref(null);
const props = defineProps({
    loadingApplications: Boolean,
    filteredApplications: Array,
    selectedStatusApplications: String,
    searchQueryApplications: String,
    getApplicationStatusClass: Function,
    formatDate: Function,
});

const emit = defineEmits([
    'update:searchQueryApplications',
    'update:selectedStatusApplications',
    'fetchApplications',
    'viewApplication',
    'editApplication',
    'confirmDeleteApplication',
]);

const viewApplicationModal = (application) => {
    selectedApplication.value = application;
    document.getElementById('my_modal_5')?.showModal();
};

const editApplicationModal = (application) => {
    selectedApplication.value = application;
    document.getElementById('my_modal_6')?.showModal();
};

const closeEditModal = () => {
    document.getElementById('my_modal_6')?.close();
};

const handleRefresh = () => {
    emit('fetchApplications');
};
</script>

<template>
    <div>
        <div class="flex flex-col md:flex-row md:justify-between">
            <div class="flex items-center gap-3">
                <input
                    type="text"
                    class="h-[50px] w-full rounded-md border-fields"
                    :value="searchQueryApplications"
                    @input="emit('update:searchQueryApplications', $event.target.value)"
                    placeholder="Search by applications..."
                />
            </div>

            <div class="grid grid-cols-2 items-center md:flex md:gap-3">
                <div>
                    <label class="text-sm font-medium text-gray-700"> Filter by Status: </label>
                </div>

                <div class="my-3">
                    <select
                        :value="selectedStatusApplications"
                        @change="emit('update:selectedStatusApplications', $event.target.value)"
                        class="h-[50px] w-[100%] rounded-lg border border-gray-300 py-2 focus:border-transparent focus:ring-2 focus:ring-primary md:w-[200px]"
                    >
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="under review">Under Review</option>
                        <option value="verified">Verified</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                    <span class="hidden text-sm text-gray-600 md:inline"> ({{ filteredApplications.length }} results) </span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto whitespace-nowrap">
            <table class="w-full border border-gray-200 bg-white text-gray-500">
                <thead class="border-b border-gray-200 bg-table-header">
                    <tr>
                        <th class="px-6 py-3">Application ID</th>
                        <th class="px-6 py-3">User ID</th>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Application Date</th>
                        <th class="px-6 py-3">Phone Number</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <tr v-if="loadingApplications">
                        <td :colspan="7" class="py-10 text-gray-500"><span class="skeleton skeleton-text">Loading Applications...</span></td>
                    </tr>

                    <tr v-else-if="!loadingApplications && filteredApplications.length === 0">
                        <td :colspan="7" class="py-10 text-gray-500">No applications found.</td>
                    </tr>

                    <tr v-else v-for="application in filteredApplications" :key="application.id" class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-bold">{{ application.id }}</td>
                        <td class="px-6 py-3 font-bold">{{ application.user_id }}</td>
                        <td class="px-6 py-3 font-bold">{{ application.first_name.toUpperCase() }} {{ application.last_name.toUpperCase() }}</td>
                        <td class="px-6 py-3">{{ formatDate(application.created_at) }}</td>
                        <td class="px-6 py-3">{{ application.phone }}</td>
                        <td class="px-6 py-3">
                            <span :class="getApplicationStatusClass(application.status)">
                                {{ application.status.charAt(0).toUpperCase() + application.status.slice(1) }}
                            </span>
                        </td>
                        <td class="space-x-3 px-6 py-3">
                            <button @click="viewApplicationModal(application)" class="cursor-pointer text-secondary hover:underline">View</button>

                            <button
                                v-if="
                                    application.status !== 'approved' &&
                                    application.status !== 'rejected' &&
                                    application.status !== 'pending' &&
                                    application.status !== 'under review'
                                "
                                @click="editApplicationModal(application)"
                                class="cursor-pointer text-primary hover:underline"
                            >
                                Update Status
                            </button>

                            <button @click="emit('confirmDeleteApplication', application)" class="cursor-pointer text-red-500 hover:underline">
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <dialog id="my_modal_5" class="modal">
            <div class="modal-box max-w-3xl">
                <form method="dialog">
                    <button class="btn absolute top-2 right-2 btn-circle btn-ghost btn-sm">✕</button>
                </form>
                <div class="mb-4 flex items-center">
                    <h3 class="mr-5 text-lg font-bold">View Application ID - {{ selectedApplication?.id }}</h3>
                    <span :class="getApplicationStatusClass(selectedApplication?.status)">
                        {{ selectedApplication?.status.charAt(0).toUpperCase() + selectedApplication?.status.slice(1) }}
                    </span>
                </div>
                <div class="py-4">
                    <View_form v-if="selectedApplication" :application="selectedApplication" @close="closeViewModal" />
                </div>
            </div>
        </dialog>

        <dialog id="my_modal_6" class="modal">
            <div class="modal-box max-w-3xl">
                <form method="dialog">
                    <button class="btn absolute top-2 right-2 btn-circle btn-ghost btn-sm">✕</button>
                </form>
                <h3 class="mb-4 text-lg font-bold">Edit Application ID - {{ selectedApplication?.id }}</h3>
                <div class="py-4">
                    <Edit_form v-if="selectedApplication" :application="selectedApplication" @close="closeEditModal" @refresh="handleRefresh" />
                </div>
            </div>
        </dialog>
    </div>
</template>
