<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const props=defineProps<{
    token?: string;
}>();

const form = useForm({});

const acceptInvitation=()=>{
    console.log(props.token)
    form.get(route('accept.invitation',props.token))
}

const rejectInvitation=()=>{
    console.log(props.token)
    form.get(route('reject.invitation',props.token))
}
</script>

<template>
    <AuthBase title="Accept Invitation" description="Accept invitation to join our company">

        <Head title="Invitation" />


        <Button @click="acceptInvitation" type="submit" class="mt-4 w-full cursor-pointer" :tabindex="4">
            Accept
        </Button>
        <Button @click="rejectInvitation" type="submit" class="mt-1 w-full bg-red-400 text-white cursor-pointer hover:bg-red-500" :tabindex="4">
            Reject
        </Button>
    </AuthBase>
</template>
