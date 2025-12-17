<script setup>
import Add_developer_form from '@/components/admin/add_developer_form.vue';

const props = defineProps({
    loadingUsers: Boolean,
    filteredUsers: Array,
    selectedStatusUsers: String,
    selectedRole: String,
    searchQueryUsers: String,
});

const emit = defineEmits([
    'update:searchQueryUsers',
    'update:selectedStatusUsers',
    'update:selectedRole',
    'fetchUsers',
    'handleApprove',
    'confirmApprove',
    'confirmDeactivate',
    'toAddDev',
    'confirmDeleteUser',
]);

const formatDate = (dateString) => {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
};

const getUserStatusClass = (status) => {
    if (status === 'active') return 'bg-green-100 text-green-700 px-3 py-1 rounded';
    if (status === 'inactive') return 'bg-yellow-100 text-yellow-700 px-3 py-1 rounded';
    return 'bg-gray-100 text-gray-700 px-3 py-1 rounded';
};

const viewApplication = (application) => {
    selectedApplication.value = application;
    document.getElementById('my_modal_3').showModal();
};

const closeModal = () => {
    document.getElementById('my_modal_3').close();
};
</script>

<template>
    <div>
        <div class="flex flex-col md:flex-row md:justify-between">
            <div class="flex items-center gap-3">
                <input
                    type="text"
                    class="h-[50px] w-full rounded-md border-fields"
                    :value="searchQueryUsers"
                    @input="emit('update:searchQueryUsers', $event.target.value)"
                    placeholder="Search by name or email..."
                />
            </div>

            <div class="grid grid-cols-2 items-center md:flex md:gap-3">
                <div>
                    <label class="text-sm font-medium text-gray-700"> Filter by Status: </label>
                </div>

                <div class="col-span-2 my-3 md:my-0">
                    <select
                        :value="selectedStatusUsers"
                        @change="
                            emit('update:selectedStatusUsers', $event.target.value);
                            emit('fetchUsers');
                        "
                        class="h-[50px] w-[100%] rounded-lg border border-gray-300 py-2 focus:border-transparent focus:ring-2 focus:ring-primary md:w-[200px]"
                    >
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>

                    <span class="hidden text-sm text-gray-600 md:inline"> ({{ filteredUsers.length }} results) </span>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700"> Filter by User type: </label>
                </div>

                <div class="my-3">
                    <select
                        :value="selectedRole"
                        @change="
                            emit('update:selectedRole', $event.target.value);
                            emit('fetchUsers');
                        "
                        class="h-[50px] w-[100%] rounded-lg border border-gray-300 py-2 focus:border-transparent focus:ring-2 focus:ring-primary md:w-[200px]"
                    >
                        <option value="all">All Roles</option>
                        <option value="developer">Developer</option>
                        <option value="client">Client</option>
                        <option value="admin">Admin</option>
                        <option value="verifier">Client</option>
                    </select>

                    <span class="hidden text-sm text-gray-600 md:inline"> ({{ filteredUsers.length }} results) </span>
                </div>

                <div class="col-span-3 my-5 md:my-0">
                    <button
                        onclick="my_modal_3.showModal()"
                        class="h-[50px] w-[100%] cursor-pointer rounded-md bg-green-500 px-4 py-2 text-white transition duration-300 ease-in-out hover:bg-green-700"
                    >
                        + Add Developer
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto whitespace-nowrap">
            <table class="w-full border border-gray-200 bg-white text-gray-500">
                <thead class="border-b border-gray-200 bg-table-header">
                    <tr>
                        <th class="px-6 py-3">User ID</th>
                        <th class="px-6 py-3">User Type</th>
                        <th class="px-6 py-3">Full Name</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Registration Date</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <tr v-if="loadingUsers">
                        <td colspan="6" class="py-10 text-gray-500"><span class="skeleton skeleton-text">Loading Users...</span></td>
                    </tr>

                    <tr v-else-if="!loadingUsers && filteredUsers.length === 0">
                        <td colspan="6" class="py-10 text-gray-500">No users found.</td>
                    </tr>

                    <tr v-else v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-bold">{{ user.id }}</td>
                        <td class="px-6 py-3 font-bold">{{ user.role.toUpperCase() }}</td>
                        <td class="px-6 py-3 font-bold">{{ user.first_name.toUpperCase() + ' ' + user.last_name.toUpperCase() }}</td>
                        <td class="px-6 py-3">{{ user.email }}</td>
                        <td class="px-6 py-3">{{ formatDate(user.created_at) }}</td>
                        <td class="px-6 py-3">
                            <span :class="getUserStatusClass(user.status)">
                                {{ user.status.charAt(0).toUpperCase() + user.status.slice(1) }}
                            </span>
                        </td>
                        <td class="space-x-3 px-6 py-3">
                            <template v-if="user.status === 'inactive'">
                                <button @click="emit('confirmApprove', user)" class="cursor-pointer font-semibold text-green-600 hover:underline">
                                    Approve
                                </button>
                            </template>
                            <template v-else>
                                <button @click="emit('confirmDeactivate', user)" class="cursor-pointer text-red-500 hover:underline">
                                    Deactivate
                                </button>

                                <button @click="emit('confirmDeleteUser', user)" class="cursor-pointer text-red-500 hover:underline">Delete</button>
                            </template>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <dialog id="my_modal_3" class="modal">
            <div class="modal-box max-w-3xl">
                <form method="dialog">
                    <button class="btn absolute top-2 right-2 btn-circle btn-ghost btn-sm">✕</button>
                </form>
                <h3 class="text-lg font-bold">Add Developer</h3>
                <div class="py-4">
                    <Add_developer_form @close="closeModal" @success="handleSuccess" />
                </div>
            </div>
        </dialog>
    </div>
</template>
