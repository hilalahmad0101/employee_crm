<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface Company {
    id: number;
    name: string;
    employees: number;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Company',
        href: '/companies',
    },
];

// Sample data - replace with actual props from your controller
const companies = ref<Company[]>([
    { id: 1, name: 'Name1', employees: 23 },
    { id: 2, name: 'TechCorp Solutions', employees: 45 },
    { id: 3, name: 'Digital Innovations', employees: 12 },
    { id: 4, name: 'Global Systems', employees: 78 },
    { id: 5, name: 'Future Technologies', employees: 34 },
]);

const searchQuery = ref('');
const sortBy = ref('name');

// Computed properties for dashboard stats
const totalCompanies = computed(() => companies.value.length);
const totalEmployees = computed(() => companies.value.reduce((sum, company) => sum + company.employees, 0));
const pendingInvitations = computed(() => 8); // This would come from your backend

// Filtered and sorted companies
const filteredCompanies = computed(() => {
    let filtered = companies.value;

    if (searchQuery.value) {
        filtered = filtered.filter(company =>
            company.name.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
    }

    return filtered.sort((a, b) => {
        if (sortBy.value === 'name') {
            return a.name.localeCompare(b.name);
        } else if (sortBy.value === 'employees') {
            return b.employees - a.employees;
        }
        return 0;
    });
});

// Methods
const createCompany = () => {
    router.visit('/companies/create');
};

const editCompany = (company: Company) => {
    router.visit(`/companies/${company.id}/edit`);
};

const deleteCompany = (company: Company) => {
    if (confirm(`Are you sure you want to delete ${company.name}?`)) {
        router.delete(`/companies/${company.id}`);
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Dashboard Title -->
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Companies</h1>
            </div>


            <!-- Companies Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <!-- Companies Header -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Companies</h2>
                </div>

                <!-- Search and Controls -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row gap-4 justify-between">
                        <!-- Search Input -->
                        <div class="relative flex-1 max-w-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search companies..."
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <div class="flex gap-3">
                            <!-- Sort Dropdown -->
                            <select
                                v-model="sortBy"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="name">Sort By Name</option>
                                <option value="employees">Sort By Employees</option>
                            </select>

                            <!-- Create Company Button -->
                            <button
                                @click="createCompany"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                            >
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create Company
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Companies Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Employees
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr
                                v-for="company in filteredCompanies"
                                :key="company.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ company.name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ company.employees }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex space-x-2">
                                        <button
                                            @click="editCompany(company)"
                                            class="inline-flex items-center px-3 py-1 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded transition-colors duration-200"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            @click="deleteCompany(company)"
                                            class="inline-flex items-center px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded transition-colors duration-200"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-if="filteredCompanies.length === 0" class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No companies found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ searchQuery ? 'Try adjusting your search criteria.' : 'Get started by creating a new company.' }}
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
