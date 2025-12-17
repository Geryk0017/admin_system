<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import AdminNavbar from './components/admin/admin_navbar.vue';
import Navbar from './components/navbar.vue';
import verifier_navbar from './components/verifier/verifier_navbar.vue';

const route = useRoute();

const currentNavbar = computed(() => {
    if (route.meta.hideNavbar) return null;
    if (route.path.startsWith('/admin') || route.path.startsWith('/developer')) return 'admin';
    if (route.path.startsWith('/verifier')) return 'verifier';

    return 'default';
});

const pageTitle = computed(() => {
    if (route.path.startsWith('/admin')) return 'Admin';
    if (route.path.startsWith('/verifier')) return 'Verifier';
    if (route.path.startsWith('/developer')) return 'Developer';
});

const dashboardLink = computed(() => {
    if (route.path.startsWith('/admin')) return '/admin/dashboard';
    if (route.path.startsWith('/verifier')) return '/verifier/dashboard';
    if (route.path.startsWith('/developer')) return '/developer/dashboard';
    return '/';
});

const taskLink = computed(() => {
    if (route.path.startsWith('/admin')) return '/admin/task';
    if (route.path.startsWith('/developer')) return '/developer/task';
    return '/';
});
</script>

<template>
    <!-- Wrap router-view inside Navbar using slot -->
    <Navbar v-if="currentNavbar === 'default'">
        <router-view />
    </Navbar>

    <AdminNavbar v-else-if="currentNavbar === 'admin'" :title="pageTitle" :dashboardLink="dashboardLink" :taskLink="taskLink">
        <router-view />
    </AdminNavbar>

    <verifier_navbar v-else-if="currentNavbar === 'verifier'" :title="pageTitle" :dashboardLink="dashboardLink" :taskLink="taskLink">
        <router-view />
    </verifier_navbar>

    <!-- For pages without navbar -->
    <router-view v-else />
</template>
