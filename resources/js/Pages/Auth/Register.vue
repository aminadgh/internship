<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { UserIcon, EnvelopeIcon, LockClosedIcon, EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showPasswordConfirm = ref(false);

const passwordStrength = computed(() => {
    const val = form.password;
    if (!val) return 0;
    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    return score;
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />
        <div class="flex flex-col items-center mb-6">
            <span class="text-3xl font-extrabold text-blue-900 tracking-wide mb-2">SwiftNotify</span>
            <span class="text-lg text-gray-500">Create your account</span>
        </div>
        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="name" value="Name" />
                <div class="relative">
                    <UserIcon class="w-5 h-5 absolute left-3 top-3 text-gray-400" />
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full pl-12 py-3 text-base"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Your full name"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
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
                        autocomplete="new-password"
                        placeholder="Create a password"
                    />
                    <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-3 text-gray-400 focus:outline-none">
                        <EyeIcon v-if="!showPassword" class="w-5 h-5" />
                        <EyeSlashIcon v-else class="w-5 h-5" />
                    </button>
                </div>
                <div class="mt-2">
                    <div class="h-2 w-full bg-gray-200 rounded">
                        <div :class="[
                            'h-2 rounded',
                            passwordStrength === 0 ? 'w-0' :
                            passwordStrength === 1 ? 'w-1/4 bg-red-400' :
                            passwordStrength === 2 ? 'w-2/4 bg-yellow-400' :
                            passwordStrength === 3 ? 'w-3/4 bg-blue-400' :
                            'w-full bg-green-500'
                        ]"></div>
                    </div>
                    <span v-if="form.password" class="text-xs text-gray-500">
                        Password strength: 
                        <span :class="[
                            passwordStrength <= 1 ? 'text-red-500' :
                            passwordStrength === 2 ? 'text-yellow-500' :
                            passwordStrength === 3 ? 'text-blue-500' :
                            'text-green-600'
                        ]">
                            {{
                                passwordStrength === 0 ? 'Too short' :
                                passwordStrength === 1 ? 'Weak' :
                                passwordStrength === 2 ? 'Fair' :
                                passwordStrength === 3 ? 'Good' :
                                'Strong'
                            }}
                        </span>
                    </span>
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>
            <div>
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <div class="relative">
                    <LockClosedIcon class="w-5 h-5 absolute left-3 top-3 text-gray-400" />
                    <TextInput
                        id="password_confirmation"
                        :type="showPasswordConfirm ? 'text' : 'password'"
                        class="mt-1 block w-full pl-12 pr-12 py-3 text-base"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Repeat your password"
                    />
                    <button type="button" @click="showPasswordConfirm = !showPasswordConfirm" class="absolute right-3 top-3 text-gray-400 focus:outline-none">
                        <EyeIcon v-if="!showPasswordConfirm" class="w-5 h-5" />
                        <EyeSlashIcon v-else class="w-5 h-5" />
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>
            <div class="mt-4 flex items-center justify-between">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Already registered?
                </Link>
                <PrimaryButton
                    class="ms-4 px-8 py-2 text-base"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
