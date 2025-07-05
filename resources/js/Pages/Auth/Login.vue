<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { EnvelopeIcon, LockClosedIcon, EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />
        <div class="flex flex-col items-center mb-6">
            <span class="text-3xl font-extrabold text-blue-900 tracking-wide mb-2">SwiftNotify</span>
            <span class="text-lg text-gray-500">Sign in to your account</span>
        </div>
        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>
        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="email" value="Email" />
                <div class="relative">
                    <EnvelopeIcon class="w-5 h-5 absolute left-3 top-3 text-gray-400" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full pl-12 py-3 text-base"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="you@email.com"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div>
                <InputLabel for="password" value="Password" />
                <div class="relative">
                    <LockClosedIcon class="w-5 h-5 absolute left-3 top-3 text-gray-400" />
                    <TextInput
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="mt-1 block w-full pl-12 pr-12 py-3 text-base"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="Your password"
                    />
                    <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-3 text-gray-400 focus:outline-none">
                        <EyeIcon v-if="!showPassword" class="w-5 h-5" />
                        <EyeSlashIcon v-else class="w-5 h-5" />
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>
            <div class="block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Forgot your password?
                </Link>
                <PrimaryButton
                    class="ms-4 px-8 py-2 text-base"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
