<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { UserIcon, EnvelopeIcon, LockClosedIcon, EyeIcon, EyeSlashIcon, ChatBubbleLeftRightIcon, ShieldCheckIcon, PhoneIcon, BuildingOfficeIcon } from '@heroicons/vue/24/outline';

const form = useForm({
    name: '',
    email: '',
    phone_number: '',
    company: '',
    role: 'agent', // Default role
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

const passwordStrengthText = computed(() => {
    const strength = passwordStrength.value;
    if (strength === 0) return { text: 'Too short', color: 'text-red-500', bg: 'bg-red-500' };
    if (strength === 1) return { text: 'Weak', color: 'text-red-500', bg: 'bg-red-500' };
    if (strength === 2) return { text: 'Fair', color: 'text-yellow-500', bg: 'bg-yellow-500' };
    if (strength === 3) return { text: 'Good', color: 'text-blue-500', bg: 'bg-blue-500' };
    return { text: 'Strong', color: 'text-green-600', bg: 'bg-green-500' };
});

const passwordStrengthWidth = computed(() => {
    const strength = passwordStrength.value;
    if (strength === 0) return 'w-0';
    if (strength === 1) return 'w-1/4';
    if (strength === 2) return 'w-2/4';
    if (strength === 3) return 'w-3/4';
    return 'w-full';
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
        
        <!-- Hero Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full mb-4">
                <ChatBubbleLeftRightIcon class="w-8 h-8 text-white" />
            </div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">
                SwiftNotify
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">Join us and start managing your SMS campaigns</p>
        </div>

        <!-- Registration Form -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 dark:border-gray-700/50 p-8 relative overflow-hidden">
            <!-- Decorative background for form -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-purple-50/50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-3xl"></div>
            <div class="relative z-10">
                <form @submit.prevent="submit" class="space-y-6">
                <!-- Personal Information Section -->
                <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <UserIcon class="w-5 h-5 mr-2 text-blue-600" />
                        Personal Information
                    </h3>
                    
                    <!-- Name Field -->
                    <div class="mb-4">
                        <InputLabel for="name" value="Full Name *" class="text-sm font-semibold text-gray-700 dark:text-gray-300" />
                        <div class="relative mt-2">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <UserIcon class="h-5 w-5 text-gray-400" />
                            </div>
                            <TextInput
                                id="name"
                                type="text"
                                class="block w-full pl-12 pr-4 py-4 text-base border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="Enter your full name"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <!-- Email Field -->
                    <div class="mb-4">
                        <InputLabel for="email" value="Email Address *" class="text-sm font-semibold text-gray-700 dark:text-gray-300" />
                        <div class="relative mt-2">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <EnvelopeIcon class="h-5 w-5 text-gray-400" />
                            </div>
                            <TextInput
                                id="email"
                                type="email"
                                class="block w-full pl-12 pr-4 py-4 text-base border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                                v-model="form.email"
                                required
                                autocomplete="username"
                                placeholder="Enter your email address"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <!-- Phone Number Field -->
                    <div class="mb-4">
                        <InputLabel for="phone_number" value="Phone Number *" class="text-sm font-semibold text-gray-700 dark:text-gray-300" />
                        <div class="relative mt-2">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <PhoneIcon class="h-5 w-5 text-gray-400" />
                            </div>
                            <TextInput
                                id="phone_number"
                                type="tel"
                                class="block w-full pl-12 pr-4 py-4 text-base border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                                v-model="form.phone_number"
                                required
                                autocomplete="tel"
                                placeholder="Enter your phone number"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.phone_number" />
                    </div>

                    <!-- Company/Organization Field -->
                    <div>
                        <InputLabel for="company" value="Company/Organization" class="text-sm font-semibold text-gray-700 dark:text-gray-300" />
                        <div class="relative mt-2">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <BuildingOfficeIcon class="h-5 w-5 text-gray-400" />
                            </div>
                            <TextInput
                                id="company"
                                type="text"
                                class="block w-full pl-12 pr-4 py-4 text-base border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                                v-model="form.company"
                                autocomplete="organization"
                                placeholder="Enter your company name (optional)"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.company" />
                    </div>
                </div>

                <!-- Account Type Section -->
                <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <ShieldCheckIcon class="w-5 h-5 mr-2 text-green-600" />
                        Account Type
                    </h3>
                    
                    <InputLabel for="role" value="Select your account type *" class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3" />
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="relative flex cursor-pointer rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 p-4 focus:outline-none hover:border-blue-500 dark:hover:border-blue-400 transition-all duration-200">
                            <input type="radio" name="role" value="agent" v-model="form.role" class="sr-only" />
                            <div class="flex w-full items-center justify-between">
                                <div class="flex items-center">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900 dark:text-white">Agent</div>
                                        <div class="text-gray-500 dark:text-gray-400">Send SMS campaigns to your contacts</div>
                                        <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">Perfect for marketing teams</div>
                                    </div>
                                </div>
                                <div class="shrink-0 text-blue-600" v-if="form.role === 'agent'">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="12" r="12" fill="currentColor" opacity="0.2"/>
                                        <path d="M7 13l3 3 7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                        </label>
                        <label class="relative flex cursor-pointer rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 p-4 focus:outline-none hover:border-blue-500 dark:hover:border-blue-400 transition-all duration-200">
                            <input type="radio" name="role" value="admin" v-model="form.role" class="sr-only" />
                            <div class="flex w-full items-center justify-between">
                                <div class="flex items-center">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900 dark:text-white">Admin</div>
                                        <div class="text-gray-500 dark:text-gray-400">Manage everything and all users</div>
                                        <div class="text-xs text-purple-600 dark:text-purple-400 mt-1">For system administrators</div>
                                    </div>
                                </div>
                                <div class="shrink-0 text-blue-600" v-if="form.role === 'admin'">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="12" r="12" fill="currentColor" opacity="0.2"/>
                                        <path d="M7 13l3 3 7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                        </label>
                    </div>
                    <InputError class="mt-2" :message="form.errors.role" />
                </div>

                <!-- Security Section -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <LockClosedIcon class="w-5 h-5 mr-2 text-red-600" />
                        Security
                    </h3>

                    <!-- Password Field -->
                    <div class="mb-4">
                        <InputLabel for="password" value="Password *" class="text-sm font-semibold text-gray-700 dark:text-gray-300" />
                        <div class="relative mt-2">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <LockClosedIcon class="h-5 w-5 text-gray-400" />
                            </div>
                            <TextInput
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                class="block w-full pl-12 pr-12 py-4 text-base border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                                v-model="form.password"
                                required
                                autocomplete="new-password"
                                placeholder="Create a strong password"
                            />
                            <button 
                                type="button" 
                                @click="showPassword = !showPassword" 
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200"
                            >
                                <EyeIcon v-if="!showPassword" class="h-5 w-5" />
                                <EyeSlashIcon v-else class="h-5 w-5" />
                            </button>
                        </div>
                        
                        <!-- Password Strength Indicator -->
                        <div class="mt-3" v-if="form.password">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Password strength:</span>
                                <span :class="passwordStrengthText.color" class="text-sm font-medium">
                                    {{ passwordStrengthText.text }}
                                </span>
                            </div>
                            <div class="h-2 w-full bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                                <div 
                                    :class="[passwordStrengthWidth, passwordStrengthText.bg]"
                                    class="h-2 rounded-full transition-all duration-300"
                                ></div>
                            </div>
                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                <ul class="space-y-1">
                                    <li :class="form.password.length >= 8 ? 'text-green-600' : 'text-gray-400'">
                                        ✓ At least 8 characters
                                    </li>
                                    <li :class="/[A-Z]/.test(form.password) ? 'text-green-600' : 'text-gray-400'">
                                        ✓ One uppercase letter
                                    </li>
                                    <li :class="/[0-9]/.test(form.password) ? 'text-green-600' : 'text-gray-400'">
                                        ✓ One number
                                    </li>
                                    <li :class="/[^A-Za-z0-9]/.test(form.password) ? 'text-green-600' : 'text-gray-400'">
                                        ✓ One special character
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <!-- Confirm Password Field -->
                    <div>
                        <InputLabel for="password_confirmation" value="Confirm Password *" class="text-sm font-semibold text-gray-700 dark:text-gray-300" />
                        <div class="relative mt-2">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <ShieldCheckIcon class="h-5 w-5 text-gray-400" />
                            </div>
                            <TextInput
                                id="password_confirmation"
                                :type="showPasswordConfirm ? 'text' : 'password'"
                                class="block w-full pl-12 pr-12 py-4 text-base border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Repeat your password"
                            />
                            <button 
                                type="button" 
                                @click="showPasswordConfirm = !showPasswordConfirm" 
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200"
                            >
                                <EyeIcon v-if="!showPasswordConfirm" class="h-5 w-5" />
                                <EyeSlashIcon v-else class="h-5 w-5" />
                            </button>
                        </div>
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6">
                    <PrimaryButton
                        class="w-full py-4 px-6 text-base font-semibold bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing" class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Creating account...
                        </span>
                        <span v-else>Create Account</span>
                    </PrimaryButton>
                </div>
                </form>

                <!-- Google Sign-In Button -->
                <div class="pt-4">
                    <a
                        :href="route('google.redirect')"
                        class="w-full inline-flex items-center justify-center px-6 py-4 border border-gray-300/50 dark:border-gray-600/50 rounded-xl text-base font-medium text-gray-700 dark:text-gray-300 bg-white/80 dark:bg-gray-700/80 hover:bg-white dark:hover:bg-gray-600 backdrop-blur-sm transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                    >
                        <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Continue with Google
                    </a>
                </div>

                <!-- Divider -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300/50 dark:border-gray-600/50"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white/80 dark:bg-gray-800/80 text-gray-500 dark:text-gray-400">Already have an account?</span>
                    </div>
                </div>

                <!-- Login Link -->
                <div class="text-center">
                    <Link
                        :href="route('login')"
                        class="inline-flex items-center px-6 py-3 border border-gray-300/50 dark:border-gray-600/50 rounded-xl text-base font-medium text-gray-700 dark:text-gray-300 bg-white/80 dark:bg-gray-700/80 hover:bg-white dark:hover:bg-gray-600 backdrop-blur-sm transition-all duration-200"
                    >
                        Sign in instead
                    </Link>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                By creating an account, you agree to our Terms of Service and Privacy Policy.
            </p>
        </div>
    </GuestLayout>
</template>
