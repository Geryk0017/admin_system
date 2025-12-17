<script setup>
import Edit_form from '@/components/client/edit_form.vue';
import View_myApplication from '@/components/client/view_myApplication.vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';

const users = ref([]);
const searchQueryApplications = ref('');
const loadingApplications = ref(false);
const selectedStatusUsers = ref('all');
const selectedStatusApplications = ref('all');
const selectedApplication = ref(null);
const applications = ref([]);
const loadingUsers = ref(false);
const currentUser = ref(null);
const searchQuery = ref('');
const router = useRouter();

onMounted(() => {
    // Get logged-in user from sessionStorage
    const storedUser = JSON.parse(sessionStorage.getItem('user'));
    if (storedUser) {
        currentUser.value = storedUser;
        fetchUsers();
        fetchApplications();
    } else {
        Swal.fire({
            title: 'Error!',
            text: 'Please log in first.',
            icon: 'error',
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'OK',
        }).then(() => {
            router.push({ name: 'login' });
        });
    }
});

const fetchUsers = () => {
    loadingUsers.value = true;

    const params = {};
    if (selectedStatusUsers.value !== 'all') {
        params.status = selectedStatusUsers.value;
    }

    axios
        .get('/api/users', { params })
        .then((res) => {
            users.value = res.data;
            loadingUsers.value = false;
        })
        .catch((err) => {
            console.error('Error fetching users:', err);
            loadingUsers.value = false;
        });
};

const fetchApplications = () => {
    if (!currentUser.value) return;

    loadingApplications.value = true;

    const params = {};
    if (selectedStatusApplications.value !== 'all') {
        params.status = selectedStatusApplications.value;
    }

    // Fetch only the current user's applications
    axios
        .get(`/api/myApplication/${currentUser.value.id}`, { params })
        .then((res) => {
            applications.value = res.data;
            loadingApplications.value = false;
        })
        .catch((err) => {
            console.error('Error fetching applications:', err);
            loadingApplications.value = false;
        });
};

const handleDeleteApplication = async (application) => {
    try {
        await axios.delete(`/api/registration/${application.id}`);

        await refreshUserData();
        fetchApplications();

        Swal.fire({
            title: 'Deleted!',
            text: 'Application has been deleted successfully.',
            icon: 'success',
            confirmButtonColor: '#10b981',
            confirmButtonText: 'OK',
        });

        // Refresh the applications list
        fetchApplications();
    } catch (error) {
        console.error('Error deleting application:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to delete application. Please try again.',
            icon: 'error',
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'OK',
        });
    }
};

const refreshUserData = async () => {
    try {
        const response = await axios.get('/api/current-user');
        const updatedUser = response.data;

        // Update sessionStorage with fresh data
        sessionStorage.setItem('user', JSON.stringify(updatedUser));

        // Update current user ref
        currentUser.value = updatedUser;
    } catch (error) {
        console.error('Error refreshing user data:', error);
    }
};

const confirmDeleteApplication = (application) => {
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete application #${application.id}. This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            handleDeleteApplication(application);
        }
    });
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
};

const filteredApplications = computed(() => {
    return applications.value.filter((app) => {
        const matchesSearch =
            !searchQueryApplications.value ||
            app.id.toString().includes(searchQueryApplications.value) ||
            app.first_name.toLowerCase().includes(searchQueryApplications.value.toLowerCase()) ||
            app.last_name.toLowerCase().includes(searchQueryApplications.value.toLowerCase()) ||
            app.email.toLowerCase().includes(searchQueryApplications.value.toLowerCase());

        const matchesStatus = selectedStatusApplications.value === 'all' || app.status.toLowerCase() === selectedStatusApplications.value;

        return matchesSearch && matchesStatus;
    });
});

const getApplicationStatusClass = (status) => {
    switch (status?.toLowerCase().trim()) {
        case 'approved':
            return 'bg-green-100 text-green-700 px-3 py-1 rounded';
        case 'rejected':
            return 'bg-red-100 text-red-700 px-3 py-1 rounded';
        case 'pending':
            return 'bg-yellow-100 text-yellow-700 px-3 py-1 rounded';
        case 'verified':
            return 'bg-secondary text-white px-3 py-1 rounded';
        case 'under review':
            return 'bg-gray-100 text-gray-700 px-3 py-1 rounded';
        default:
            return 'bg-gray-100 text-gray-700 px-3 py-1 rounded';
    }
};

const viewApplicationModal = (application) => {
    selectedApplication.value = application;
    document.getElementById('my_modal_5')?.showModal();
};

const editApplicationModal = (application) => {
    selectedApplication.value = application;
    document.getElementById('my_modal_6')?.showModal();
};
const closeViewModal = () => {
    document.getElementById('my_modal_5')?.close();
};

const closeEditModal = () => {
    document.getElementById('my_modal_6')?.close();
};

const handleApplicationUpdate = () => {
    fetchApplications(); // Refresh the list
};
</script>

<template>
    <div class="m-5 flex flex-col space-y-3 md:flex-row md:justify-between">
        <div class="flex items-center gap-3">
            <h1 class="col-span-2 w-full text-[20px] font-semibold md:text-[25px]">My Applications</h1>

            <input
                type="text"
                class="col-span-2 h-[50px] rounded-md border border-gray-300 px-3 text-sm focus:ring-2 focus:ring-blue-500 md:h-[50px]"
                placeholder="Search by ID, name, email, or phone..."
                v-model="searchQueryApplications"
                @input="searchQueryApplications = $event.target.value"
            />
        </div>

        <div class="grid grid-cols-2 items-center gap-3">
            <div>
                <label for="filter_status">Filter by Status:</label>
            </div>
            <div>
                <select
                    v-model="selectedStatusApplications"
                    @change="fetchApplications"
                    class="h-[50px] w-[100%] rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500 md:w-[150px]"
                >
                    <option value="all">All</option>
                    <option value="pending">Pending</option>
                    <option value="under review">Under Review</option>
                    <option value="verified">Verified</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>
    </div>

    <div class="m-5 overflow-x-auto whitespace-nowrap">
        <table class="w-full border border-gray-200 bg-white text-gray-500">
            <thead class="border-b border-gray-200 bg-table-header">
                <tr>
                    <th class="px-6 py-3">Application ID</th>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Application Date</th>
                    <th class="px-6 py-3">Phone Number</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <tr v-if="loadingApplications">
                    <td colspan="7" class="py-10 text-gray-500">Loading applications...</td>
                </tr>
                <tr v-else-if="!loadingApplications && filteredApplications.length === 0">
                    <td colspan="7" class="py-10 text-gray-500">No applications found.</td>
                </tr>
                <tr v-else v-for="application in filteredApplications" :key="application.id" class="hover:bg-gray-50">
                    <td class="px-6 py-3 font-bold">{{ application.id }}</td>
                    <td class="px-6 py-3 font-bold">{{ application.first_name }} {{ application.last_name }}</td>
                    <td class="px-6 py-3">{{ formatDate(application.created_at) }}</td>
                    <td class="px-6 py-3">{{ application.phone }}</td>
                    <td class="px-6 py-3">{{ application.email }}</td>
                    <td class="px-6 py-3">
                        <span :class="getApplicationStatusClass(application.status)">
                            {{ application.status.charAt(0).toUpperCase() + application.status.slice(1) }}
                        </span>
                    </td>

                    <td class="space-x-3 px-6 py-3">
                        <button @click="viewApplicationModal(application)" class="cursor-pointer text-secondary hover:underline">View</button>
                        <button
                            v-if="application.status.toLowerCase() === 'pending'"
                            @click="editApplicationModal(application)"
                            class="cursor-pointer text-primary hover:underline"
                        >
                            Edit
                        </button>
                        <button
                            v-if="application.status.toLowerCase() !== 'under review'"
                            @click="confirmDeleteApplication(application)"
                            class="cursor-pointer text-red-500 hover:underline"
                        >
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
                <View_myApplication
                    v-if="selectedApplication"
                    :application="selectedApplication"
                    @close="closeViewModal"
                    @application-updated="handleApplicationUpdate"
                />
            </div>
        </div>
    </dialog>

    <dialog id="my_modal_6" class="modal">
        <div class="modal-box max-w-3xl">
            <form method="dialog">
                <button class="btn absolute top-2 right-2 btn-circle btn-ghost btn-sm">✕</button>
            </form>
            <div class="mb-4 flex items-center">
                <h3 class="mr-5 text-lg font-bold">Edit Application ID - {{ selectedApplication?.id }}</h3>
                <span :class="getApplicationStatusClass(selectedApplication?.status)">
                    {{ selectedApplication?.status.charAt(0).toUpperCase() + selectedApplication?.status.slice(1) }}
                </span>
            </div>

            <div class="py-4">
                <Edit_form
                    v-if="selectedApplication"
                    :application="selectedApplication"
                    @close="closeEditModal"
                    @application-updated="handleApplicationUpdate"
                />
            </div>
        </div>
    </dialog>
</template>
