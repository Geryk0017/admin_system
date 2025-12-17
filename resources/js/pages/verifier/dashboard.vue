<script setup>
import metrics from '@/components/admin/metrics.vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import Applications_table from './applications_table.vue';
import User_management_table from './user_management_table.vue';

const router = useRouter();
const users = ref([]);
const applications = ref([]);
const loadingUsers = ref(false);
const loadingApplications = ref(false);
const searchQueryUsers = ref('');
const searchQueryApplications = ref('');
const selectedStatusUsers = ref('all');
const selectedStatusApplications = ref('all');
const selectedRole = ref('all');
const message = ref('');
const activeTab = ref('users');
let messageTimeout = null;

const paginationUsers = ref({
    current_page: 1,
    last_page: 1,
    links: [],
});

const paginationApplications = ref({
    current_page: 1,
    last_page: 1,
    links: [],
});

// Fetch data on component mount
onMounted(() => {
    fetchUsers();
    fetchApplications();
});

// Switch tab
const switchTab = (tab) => {
    activeTab.value = tab;
};

// USERS FUNCTIONS
const fetchUsers = (page = 1) => {
    loadingUsers.value = true;

    const params = {
        page: page,
    };

    if (selectedStatusUsers.value !== 'all') {
        params.status = selectedStatusUsers.value;
    }

    if (selectedRole.value !== 'all') {
        params.role = selectedRole.value;
    }
    // SEARCH QUERY
    if (searchQueryUsers.value) {
        params.search = searchQueryUsers.value;
    }

    axios
        .get('/api/users', { params })
        .then((res) => {
            users.value = res.data.data;
            paginationUsers.value = {
                current_page: res.data.current_page,
                last_page: res.data.last_page,
                links: res.data.links,
            };
            loadingUsers.value = false;
        })
        .catch((err) => {
            console.error('Error fetching users:', err);
            loadingUsers.value = false;
        });
};

// const filteredUsers = computed(() => {
//     return users.value.filter((user) => {
//         const matchesSearch =
//             !searchQueryUsers.value ||
//             user.id.toString().includes(searchQueryUsers.value) ||
//             user.fullname.toLowerCase().includes(searchQueryUsers.value.toLowerCase()) ||
//             user.email.toLowerCase().includes(searchQueryUsers.value.toLowerCase());

//         const matchesStatus = selectedStatusUsers.value === 'all' || user.status.toLowerCase() === selectedStatusUsers.value;

//         return matchesSearch && matchesStatus;
//     });
// });

// APPLICATIONS FUNCTIONS
const fetchApplications = (page = 1) => {
    loadingApplications.value = true;

    const params = {
        page: page,
    };
    if (selectedStatusApplications.value !== 'all') {
        params.status = selectedStatusApplications.value;
    }

    if (searchQueryApplications.value) {
        params.search = searchQueryApplications.value;
    }

    axios
        .get('/api/registration', { params })
        .then((res) => {
            applications.value = res.data.data;
            paginationApplications.value = {
                current_page: res.data.current_page,
                last_page: res.data.last_page,
                links: res.data.links,
            };
            loadingApplications.value = false;
        })
        .catch((err) => {
            console.error('Error fetching applications:', err);
            loadingApplications.value = false;
        });
};

// const filteredApplications = computed(() => {
//     return applications.value.filter((app) => {
//         const matchesSearch =
//             !searchQueryApplications.value ||
//             app.id.toString().includes(searchQueryApplications.value) ||
//             app.first_name.toLowerCase().includes(searchQueryApplications.value.toLowerCase()) ||
//             app.last_name.toLowerCase().includes(searchQueryApplications.value.toLowerCase()) ||
//             app.email.toLowerCase().includes(searchQueryApplications.value.toLowerCase());

//         const matchesStatus = selectedStatusApplications.value === 'all' || app.status.toLowerCase() === selectedStatusApplications.value;

//         return matchesSearch && matchesStatus;
//     });
// });

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

const handleDeleteApplication = async (application) => {
    try {
        await axios.delete(`/api/registration/${application.id}`);

        applications.value = applications.value.filter((app) => app.id !== application.id);

        Swal.fire({
            title: 'Deleted!',
            text: 'Application has been deleted successfully.',
            icon: 'success',
            confirmButtonColor: '#10b981',
            confirmButtonText: 'OK',
        });
    } catch (err) {
        Swal.fire({
            title: 'Error!',
            text: err.response?.data?.message || 'Failed to delete application.',
            icon: 'error',
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'OK',
        });
    }
};

const viewApplication = (id) => {
    router.push({ name: 'verifier_view_client', params: { id: id } });
};

const editApplication = (id) => {
    router.push({ name: 'verifier_update_status_client', params: { id: id } });
};

// UTILITY FUNCTIONS
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

const closeMessage = () => {
    message.value = '';
    if (messageTimeout) {
        clearTimeout(messageTimeout);
    }
};

const getPageFromUrl = (url) => {
    if (!url) return null;
    const urlParams = new URLSearchParams(url.split('?')[1]);
    return urlParams.get('page') || 1;
};
</script>

<template>
    <div class="">
        <metrics />
        <div class="m-5">
            <!-- Success/Error Message -->
            <div
                v-if="message"
                class="mb-5 flex items-center justify-between rounded p-3"
                :class="message.includes('Success') || message.includes('success') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
            >
                <span>{{ message }}</span>
                <button @click="closeMessage" class="ml-4 text-lg font-bold transition hover:opacity-70">&times;</button>
            </div>

            <!-- TAB NAVIGATION -->
            <div class="mb-6 flex gap-2 border-b border-gray-200">
                <button
                    @click="switchTab('users')"
                    class="cursor-pointer px-6 py-3 text-lg font-semibold transition-all"
                    :class="activeTab === 'users' ? 'border-b-4 border-primary text-primary' : 'text-gray-500 hover:text-gray-700'"
                >
                    User Management
                    <span v-if="users.length > 0" class="ml-2 rounded-full bg-primary px-2 py-1 text-xs text-white">
                        {{ users.length }}
                    </span>
                </button>
                <button
                    @click="switchTab('applications')"
                    class="cursor-pointer px-6 py-3 text-lg font-semibold transition-all"
                    :class="activeTab === 'applications' ? 'border-b-4 border-primary text-primary' : 'text-gray-500 hover:text-gray-700'"
                >
                    Registration
                    <span v-if="applications.length > 0" class="ml-2 rounded-full bg-primary px-2 py-1 text-xs text-white">
                        {{ applications.length }}
                    </span>
                </button>
            </div>

            <!-- USER MANAGEMENT TABLE -->
            <User_management_table
                v-show="activeTab === 'users'"
                :loading-users="loadingUsers"
                :filtered-users="users"
                :selected-status-users="selectedStatusUsers"
                :selected-role="selectedRole"
                :search-query-users="searchQueryUsers"
                :format-date="formatDate"
                :getUserStatusClass="getUserStatusClass"
                @update:searchQueryUsers="
                    searchQueryUsers = $event;
                    fetchUsers(1);
                "
                @update:selectedRole="
                    selectedRole = $event;
                    fetchUsers(1);
                "
                @update:selectedStatusUsers="selectedStatusUsers = $event"
                @fetchUsers="fetchUsers(1)"
            />

            <!-- PAGINATION FOR USERS -->
            <div v-if="activeTab === 'users' && paginationUsers.links.length > 0" class="mt-4 flex justify-end gap-2">
                <button
                    v-for="(link, index) in paginationUsers.links"
                    :key="index"
                    @click="link.url && fetchUsers(getPageFromUrl(link.url))"
                    :disabled="!link.url || link.active"
                    v-html="link.label"
                    class="rounded border px-3 py-1"
                    :class="[
                        link.active ? 'bg-primary text-white' : 'bg-white hover:bg-gray-100',
                        !link.url ? 'cursor-not-allowed opacity-50' : 'cursor-pointer',
                    ]"
                ></button>
            </div>

            <!-- APPLICATION REGISTRATION TABLE -->
            <Applications_table
                v-show="activeTab === 'applications'"
                :loading-applications="loadingApplications"
                :filtered-applications="applications"
                :selected-status-applications="selectedStatusApplications"
                :search-query-applications="searchQueryApplications"
                :format-date="formatDate"
                :get-application-status-class="getApplicationStatusClass"
                @update:searchQueryApplications="
                    searchQueryApplications = $event;
                    fetchApplications(1);
                "
                @update:selectedStatusApplications="selectedStatusApplications = $event"
                @fetchApplications="fetchApplications(1)"
                @viewApplication="viewApplication"
                @editApplication="editApplication"
                @confirmDeleteApplication="confirmDeleteApplication"
            />

            <!-- PAGINATION FOR APPLICATIONS -->
            <div v-if="activeTab === 'applications' && paginationApplications.links.length > 0" class="mt-4 flex justify-end gap-2">
                <button
                    v-for="(link, index) in paginationApplications.links"
                    :key="index"
                    @click="link.url && fetchApplications(getPageFromUrl(link.url))"
                    :disabled="!link.url || link.active"
                    v-html="link.label"
                    class="rounded border px-3 py-1"
                    :class="[
                        link.active ? 'bg-primary text-white' : 'bg-white hover:bg-gray-100',
                        !link.url ? 'cursor-not-allowed opacity-50' : 'cursor-pointer',
                    ]"
                ></button>
            </div>
        </div>
    </div>
</template>
