<script setup>
const props = defineProps({
    searchQueryUsers: String,
    selectedStatusUsers: String,
    filteredUsers: Array,
    loadingUsers: Boolean,
    selectedRole: String,
    getUserStatusClass: Function,
    formatDate: Function,
});

const emit = defineEmits([
    'update:searchQueryUsers',
    'update:selectedStatusUsers',
    'fetchUsers',
    'confirmApprove',
    'confirmDeactivate',
    'update:selectedRole',
]);
</script>
<template>
    <div>
        <div class="flex flex-col space-y-3 md:flex-row md:justify-between">
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

                <div class="my-5">
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
                    <label class="text-sm font-medium text-gray-700"> Filter by Status: </label>
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
                        <option value="verifier">Verifier</option>
                    </select>

                    <span class="hidden text-sm text-gray-600 md:inline"> ({{ filteredUsers.length }} results) </span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto whitespace-nowrap">
            <table class="w-full border border-gray-200 bg-white text-gray-500">
                <thead class="border-b border-gray-200 bg-table-header">
                    <tr>
                        <th class="sticky left-0 z-10 bg-table-header px-6 py-3">User ID</th>
                        <th class="px-6 py-3">User Type</th>
                        <th class="px-6 py-3">Full Name</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Registration Date</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <tr v-if="loadingUsers">
                        <td :colspan="6" class="py-10 text-gray-500"><span class="skeleton skeleton-text">Loading Users...</span></td>
                    </tr>

                    <tr v-else-if="!loadingUsers && filteredUsers.length === 0">
                        <td :colspan="6" class="py-10 text-gray-500">No users found.</td>
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
                        <!-- <td class="space-x-3 px-6 py-3">
                            <template v-if="user.status === 'inactive'">
                                <button @click="emit('confirmApprove', user)" class="cursor-pointer font-semibold text-green-600 hover:underline">
                                    Approve
                                </button>
                            </template>
                            <template v-else>
                                <button @click="emit('confirmDeactivate', user)" class="cursor-pointer text-red-500 hover:underline">
                                    Deactivate
                                </button>
                            </template>
                        </td> -->
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
