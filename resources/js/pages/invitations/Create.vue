<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import Card from '@/components/ui/card/Card.vue';

const props = defineProps({
    companies: {
        type: Object,
        default: null,
    }
})

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Invitations',
        href: '/invitations',
    },
    {
        title: 'Create company',
        href: '/invitations/create',
    },
];

const company_id = ref<HTMLInputElement | null>(null);
const email = ref<HTMLInputElement | null>(null);
const role = ref<HTMLInputElement | null>(null);

const form = useForm({
    email: '',
    role: '',
    company_id: ''
});

const createCompany = () => {
    form.post(route('invitations.send'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: (errors: any) => {
            if (errors.company_id) {
                if (company_id.value instanceof HTMLInputElement) {
                    company_id.value.focus();
                }
            }

            if (errors.email) {
                if (email.value instanceof HTMLInputElement) {
                    email.value.focus();
                }
            }

            if (errors.role) {
                if (role.value instanceof HTMLInputElement) {
                    role.value.focus();
                }
            }
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Create company" />

        <div class="m-2 p-4">
            <Card class="p-10">
                <div class="space-y-6">
                    <HeadingSmall title="Create new company"
                        description="Add a new company to your system with basic information" />

                    <form @submit.prevent="createCompany" class="space-y-6">
                        <div class="grid gap-2">
                            <Label for="company_id">Select company</Label>
                            <select id="company_id" v-model="form.company_id"
                                class="mt-1 block border w-full outline-none p-2 text-white bg-gray-800 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="" class="bg-gray-800 text-white">Select a company</option>
                                <option v-for="company in companies" :key="company.id" :value="company.id"
                                    class="bg-gray-800 text-white">
                                    {{ company.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.company_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email">Email address</Label>
                            <Input id="email" ref="emailInput" v-model="form.email" type="email"
                                class="mt-1 block w-full" autocomplete="email" placeholder="company@example.com" />
                            <InputError :message="form.errors.email" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="role">Role</Label>
                            <Input id="role" ref="role" v-model="form.role" type="text" class="mt-1 block w-full"
                                autocomplete="street-role" placeholder="Enter  role" />
                            <InputError :message="form.errors.role" />
                        </div>

                        <div class="flex items-center gap-4">
                            <Button :disabled="form.processing">Create Invitation</Button>

                            <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                                <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Created
                                    successfully.</p>
                            </Transition>
                        </div>
                    </form>
                </div>
            </Card>
        </div>
    </AppLayout>
</template>
