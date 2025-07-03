<script setup>
import ConfirmationDialog from '@/components/ConfirmationDialog.vue';
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { reactive, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3'
import Toast from '@/components/Toast.vue';
import { debounce } from 'lodash'
import LoadingSpinner from '@/components/LoadingSpinner.vue';

const { props: $page } = usePage()
const isLoading = ref(false)
router.on('start', () => {
    isLoading.value = true
})
router.on('finish', () => {
    isLoading.value = false
})
const filters = reactive({
    search: $page.filters.search || '',
    sortBy: $page.filters.sortBy || 'asc',
})
const props = defineProps({
    admins: {
        type: Object,
        default: () => ({ data: [], links: [], meta: {} }),
    },
});

const search = debounce(() => {
    router.get(route('admins.list'), filters, { preserveState: true, replace: true })
}, 300)

function sort() {
    router.get(route('admins.list'), filters, {
        preserveState: true,
        replace: true,
    })
}
const breadcrumbs = [
    {
        title: 'Admins',
        href: '/admins',
    },
];
const form = useForm({});
const confirmOpen = ref(false)
const selectedUserId = ref(null)
function askToDelete(id) {
    selectedUserId.value = id
    confirmOpen.value = true
}

const createAdmin = () => {
    router.visit('/admins/create');
};
// Get previous and next page URLs
const getPreviousPageUrl = () => {
    const prevLink = props.admins.links?.find(link => link.label === '&laquo; Previous');
    return prevLink?.url || null;
};

const getNextPageUrl = () => {
    const nextLink = props.admins.links?.find(link => link.label === 'Next &raquo;');
    return nextLink?.url || null;
};

// Get only numbered page links (excluding prev/next)
const getPageLinks = () => {
    if (!props.admins.links) return [];
    return props.admins.links.filter(link =>
        link.label !== '&laquo; Previous' &&
        link.label !== 'Next &raquo;'
    );
};
const toastVisible = ref(false)

const deleteUser = () => {
    form.delete(route('admins.delete', selectedUserId.value), {
        preserveScroll: true,
        onSuccess: () => {
            confirmOpen.value = false
        },
    });
}

watch(() => $page.toast, (newVal) => {
    if (newVal) {
        toastVisible.value = true
        setTimeout(() => (toastVisible.value = false), 4000)
    }
})

</script>

<template>

    <Head title="Admins" />

    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Dashboard Title -->
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Admins</h1>
            </div>

            <!-- Admins Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <!-- Admins Header -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Admins List</h2>
                </div>

                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                        <!-- Search Input -->
                        <div class="relative flex-1 max-w-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input v-model="filters.search" @input="search" type="text" placeholder="Search admins..."
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-colors duration-200" />
                        </div>

                        <!-- Sort and Create Button -->
                        <div class="flex items-center gap-3">
                            <!-- Sort Select -->
                            <div class="relative">
                                <select v-model="filters.sortBy" @change="sort"
                                    class="appearance-none bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-md px-4 py-2 pr-8 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-colors duration-200">
                                    <option value="" selected disabled>Select</option>
                                    <option value="asc">Sort Name(A-Z)</option>
                                    <option value="desc">Sort Name(Z-A)</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Create Admin Button -->
                            <button @click="createAdmin"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Create Admin
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Admins Table -->
                <div class="overflow-x-auto">
                    <LoadingSpinner v-if="isLoading" message="Fetching data..." />
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" v-if="!isLoading">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Name
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Email
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Role
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="admin in admins.data" :key="admin.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ admin.name || 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ admin.email || 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ admin.role || 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap space-x-4">
                                    <Link :href="`admins/${admin.id}/edit`">
                                    <Button
                                        class="bg-green-400/50 text-white hover:bg-green-500 cursor-pointer">Edit</Button>
                                    </Link>
                                    <Button @click="askToDelete(admin.id)" variant=""
                                        class="hover:bg-red-500 bg-red-400/50 text-white cursor-pointer">Delete</Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <ConfirmationDialog :open="confirmOpen" @update:open="confirmOpen = $event" @confirm="deleteUser"
                    @cancel="confirmOpen = false" title="Delete user?"
                    message="This will permanently delete the user." />
                <!-- Empty State -->
                <div v-if="admins.data && admins.data.length === 0 && !isLoading" class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No admins found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Get started by creating a new admin.
                    </p>
                    <button @click="createAdmin"
                        class="mt-4 inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md transition-colors duration-200">
                        Create Your First Admin
                    </button>
                </div>

                <!-- Enhanced Pagination -->
                <div v-if="admins.data && admins.data.length > 0 && admins.links"
                    class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            <span v-if="admins.meta">
                                Showing {{ admins.meta.from || 1 }} to {{ admins.meta.to || admins.data.length
                                }}
                                of {{ admins.meta.total || admins.data.length }} results
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            <button @click="router.get(getPreviousPageUrl())" :disabled="!getPreviousPageUrl()" :class="[
                                'inline-flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2',
                                getPreviousPageUrl()
                                    ? 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600'
                                    : 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-600 border border-gray-200 dark:border-gray-700 cursor-not-allowed'
                            ]">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Previous
                            </button>

                            <div class="hidden sm:flex items-center space-x-1">
                                <template v-for="(link, index) in getPageLinks()" :key="index">
                                    <button v-if="!isNaN(link.label)" @click="router.get(link.url)"
                                        :disabled="!link.url" :class="[
                                            'px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2',
                                            link.active
                                                ? 'bg-gray-600 text-white border border-gray-600'
                                                : link.url
                                                    ? 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600'
                                                    : 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-600 border border-gray-200 dark:border-gray-700 cursor-not-allowed'
                                        ]">
                                        {{ link.label }}
                                    </button>

                                    <span v-else-if="link.label === '...'"
                                        class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                                        ...
                                    </span>
                                </template>
                            </div>

                            <div
                                class="sm:hidden px-3 py-2 text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                <span v-if="admins.meta">
                                    Page {{ admins.meta.current_page || 1 }} of {{ admins.meta.last_page || 1 }}
                                </span>
                            </div>

                            <button @click="router.get(getNextPageUrl())" :disabled="!getNextPageUrl()" :class="[
                                'inline-flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2',
                                getNextPageUrl()
                                    ? 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600'
                                    : 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-600 border border-gray-200 dark:border-gray-700 cursor-not-allowed'
                            ]">
                                Next
                                <svg class="h-4 w-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Toast :show="toastVisible" :message="$page.toast" />

    </AppLayout>
</template>
