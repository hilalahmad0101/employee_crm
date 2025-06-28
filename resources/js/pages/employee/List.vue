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
    employees: {
        type: Object,
        default: () => ({ data: [], links: [], meta: {} }),
    },
});


const search = debounce(() => {
    router.get(route('employees.list'), filters, { preserveState: true, replace: true })
}, 300)

function sort() {
    router.get(route('employees.list'), filters, {
        preserveState: true,
        replace: true,
    })
}
const breadcrumbs = [
    {
        title: 'employees',
        href: '/employees',
    },
];
const form = useForm({});
const confirmOpen = ref(false)
const selectedUserId = ref(null)
function askToDelete(id) {
    selectedUserId.value = id
    confirmOpen.value = true
}

// Get previous and next page URLs
const getPreviousPageUrl = () => {
    const prevLink = props.employees.links?.find(link => link.label === '&laquo; Previous');
    return prevLink?.url || null;
};

const getNextPageUrl = () => {
    const nextLink = props.employees.links?.find(link => link.label === 'Next &raquo;');
    return nextLink?.url || null;
};

// Get only numbered page links (excluding prev/next)
const getPageLinks = () => {
    if (!props.employees.links) return [];
    return props.employees.links.filter(link =>
        link.label !== '&laquo; Previous' &&
        link.label !== 'Next &raquo;'
    );
};
const toastVisible = ref(false)

const deleteUser = () => {
    form.delete(route('employees.delete', selectedUserId.value), {
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

    <Head title="employees" />

    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Dashboard Title -->
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">employees</h1>
            </div>

            <!-- employees Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <!-- employees Header -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">employees List</h2>
                </div>

                <!-- Search and Controls -->
                <!-- <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-end">
                        <button @click="createemployee"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Create employee
                        </button>
                    </div>
                </div> -->

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
                            <input v-model="filters.search" @input="search" type="text"
                                placeholder="Search employees..."
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
                        </div>
                    </div>
                </div>

                <!-- employees Table -->
                <div class="overflow-x-auto">
                    <LoadingSpinner v-if="isLoading" message="Fetching data..." />
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" v-if="!isLoading">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Employee Name
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Email
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Address
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="employee in employees.data" :key="employee.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ employee.name || 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ employee.email || 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ employee.profile?.address || 'N/A' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap space-x-4"> <Button
                                        @click="askToDelete(employee.id)" variant=""
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
                <div v-if="employees.data && employees.data.length === 0 && !isLoading" class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No employees found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Get started by creating a new employee.
                    </p>
                    <button @click="createemployee"
                        class="mt-4 inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md transition-colors duration-200">
                        Create Your First employee
                    </button>
                </div>

                <!-- Enhanced Pagination -->
                <div v-if="employees.data && employees.data.length > 0 && employees.links"
                    class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            <span v-if="employees.meta">
                                Showing {{ employees.meta.from || 1 }} to {{ employees.meta.to || employees.data.length
                                }}
                                of {{ employees.meta.total || employees.data.length }} results
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
                                <span v-if="employees.meta">
                                    Page {{ employees.meta.current_page || 1 }} of {{ employees.meta.last_page || 1 }}
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
