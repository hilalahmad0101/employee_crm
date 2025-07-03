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
        title: 'Admins',
        href: '/admins',
    },
    {
        title: 'Update admin',
        href: '/admins/update',
    },
];
const props = defineProps({
    user: {
        type: Object,
        default: null,
    },
});
const name = ref<HTMLInputElement | null>(null);
const emailInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    role: props.user.role
});

const roles = [
    'admin',
    'company'
]

const rolesRef = ref(roles);


const updateAdmin = () => {
    form.post(route('admins.update', props.user.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: (errors: any) => {
            if (errors.name) {
                if (name.value instanceof HTMLInputElement) {
                    name.value.focus();
                }
            }

            if (errors.email) {
                if (emailInput.value instanceof HTMLInputElement) {
                    emailInput.value.focus();
                }
            }
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Update admin" />

        <div class="m-2 p-4">
            <Card class="p-10">
                <div class="space-y-6">
                    <HeadingSmall title="Update new admin"
                        description="Update admin to your system with basic information" />

                    <form @submit.prevent="updateAdmin" class="space-y-6">
                        <div class="grid gap-2">
                            <Label for="name">name</Label>
                            <Input id="name" ref="name" v-model="form.name" type="text" class="mt-1 block w-full"
                                autocomplete="organization" placeholder="Enter Admin name" />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email">Email address</Label>
                            <Input id="email" ref="emailInput" v-model="form.email" type="email"
                                class="mt-1 block w-full" autocomplete="email" placeholder="admin@example.com" />
                            <InputError :message="form.errors.email" />
                        </div>


                        <div class="grid gap-2">
                            <Label for="role">Select Role</Label>
                            <select id="role" v-model="form.role"
                                class="mt-1 block border w-full outline-none p-2 text-white bg-gray-800 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="" class="bg-gray-800 text-white">Select a company</option>
                                <option v-for="(role, index) in rolesRef" :key="index" :value="role"
                                    class="bg-gray-800 text-white">
                                    {{ role }}
                                </option>
                            </select>
                            <InputError :message="form.errors.role" />
                        </div>

                        <div class="flex items-center gap-4">
                            <Button :disabled="form.processing">Update Admin</Button>

                            <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                                <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Updated
                                    successfully.</p>
                            </Transition>
                        </div>
                    </form>
                </div>
            </Card>
        </div>
    </AppLayout>
</template>
