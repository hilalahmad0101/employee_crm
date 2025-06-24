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

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Companies',
        href: '/companies',
    },
    {
        title: 'Create company',
        href: '/companies/create',
    },
];

const companyNameInput = ref<HTMLInputElement | null>(null);
const emailInput = ref<HTMLInputElement | null>(null);
const addressInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    company_name: '',
    email: '',
    address: '',
});

const createCompany = () => {
    form.post(route('companies.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: (errors: any) => {
            if (errors.company_name) {
                if (companyNameInput.value instanceof HTMLInputElement) {
                    companyNameInput.value.focus();
                }
            }

            if (errors.email) {
                if (emailInput.value instanceof HTMLInputElement) {
                    emailInput.value.focus();
                }
            }

            if (errors.address) {
                if (addressInput.value instanceof HTMLInputElement) {
                    addressInput.value.focus();
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
                            <Label for="company_name">Company name</Label>
                            <Input id="company_name" ref="companyNameInput" v-model="form.company_name" type="text"
                                class="mt-1 block w-full" autocomplete="organization"
                                placeholder="Enter company name" />
                            <InputError :message="form.errors.company_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email">Email address</Label>
                            <Input id="email" ref="emailInput" v-model="form.email" type="email"
                                class="mt-1 block w-full" autocomplete="email" placeholder="company@example.com" />
                            <InputError :message="form.errors.email" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="address">Address</Label>
                            <Input id="address" ref="addressInput" v-model="form.address" type="text"
                                class="mt-1 block w-full" autocomplete="street-address"
                                placeholder="Enter company address" />
                            <InputError :message="form.errors.address" />
                        </div>

                        <div class="flex items-center gap-4">
                            <Button :disabled="form.processing">Create company</Button>

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
