<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900"></div>
        
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <!-- Floating Circles -->
            <div class="absolute top-20 left-10 w-72 h-72 bg-blue-200 dark:bg-blue-900/20 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob"></div>
            <div class="absolute top-40 right-10 w-72 h-72 bg-purple-200 dark:bg-purple-900/20 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-200 dark:bg-pink-900/20 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000"></div>
            
            
            <!-- Lightning Bolts -->
            <div class="absolute top-0 left-1/3 w-1 h-48 bg-gradient-to-b from-transparent via-yellow-400 to-transparent opacity-0 animate-lightning-bolt-1 transform -skew-x-12"></div>
            <div class="absolute top-15 right-1/2 w-1 h-64 bg-gradient-to-b from-transparent via-blue-400 to-transparent opacity-0 animate-lightning-bolt-2 transform skew-x-12"></div>
            <div class="absolute top-25 left-2/3 w-1 h-40 bg-gradient-to-b from-transparent via-purple-400 to-transparent opacity-0 animate-lightning-bolt-3 transform -skew-x-6"></div>
            
            <!-- Additional Lightning Strikes -->
            <div class="absolute top-0 left-1/6 w-3 h-screen bg-gradient-to-b from-transparent via-yellow-200 to-transparent opacity-0 animate-lightning-5"></div>
            <div class="absolute top-0 right-1/6 w-3 h-screen bg-gradient-to-b from-transparent via-blue-200 to-transparent opacity-0 animate-lightning-6"></div>
            <div class="absolute top-0 left-3/4 w-2 h-screen bg-gradient-to-b from-transparent via-purple-200 to-transparent opacity-0 animate-lightning-7"></div>
            
            <!-- Classic Lightning Bolts -->
            <svg class="absolute inset-0 w-full h-full pointer-events-none opacity-0 animate-lightning-bolt-svg-1" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M30 0 L25 20 L35 30 L20 50 L40 60 L15 80 L45 100" 
                      stroke="url(#yellowGradient)" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                <defs>
                    <linearGradient id="yellowGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" style="stop-color:#fbbf24;stop-opacity:0" />
                        <stop offset="50%" style="stop-color:#fbbf24;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#fbbf24;stop-opacity:0" />
                    </linearGradient>
                </defs>
            </svg>
            
            <svg class="absolute inset-0 w-full h-full pointer-events-none opacity-0 animate-lightning-bolt-svg-2" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M70 0 L65 20 L75 30 L60 50 L80 60 L55 80 L85 100" 
                      stroke="url(#blueGradient)" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                <defs>
                    <linearGradient id="blueGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:0" />
                        <stop offset="50%" style="stop-color:#3b82f6;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#3b82f6;stop-opacity:0" />
                    </linearGradient>
                </defs>
            </svg>
            
            <svg class="absolute inset-0 w-full h-full pointer-events-none opacity-0 animate-lightning-bolt-svg-3" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M50 0 L45 20 L55 30 L40 50 L60 60 L35 80 L65 100" 
                      stroke="url(#purpleGradient)" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                <defs>
                    <linearGradient id="purpleGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" style="stop-color:#8b5cf6;stop-opacity:0" />
                        <stop offset="50%" style="stop-color:#8b5cf6;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#8b5cf6;stop-opacity:0" />
                    </linearGradient>
                </defs>
            </svg>
            
            <svg class="absolute inset-0 w-full h-full pointer-events-none opacity-0 animate-lightning-bolt-svg-4" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M20 0 L15 20 L25 30 L10 50 L30 60 L5 80 L35 100" 
                      stroke="url(#cyanGradient)" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                <defs>
                    <linearGradient id="cyanGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" style="stop-color:#06b6d4;stop-opacity:0" />
                        <stop offset="50%" style="stop-color:#06b6d4;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#06b6d4;stop-opacity:0" />
                    </linearGradient>
                </defs>
            </svg>
            
            <!-- Grid Pattern -->
            <div class="absolute inset-0 opacity-30">
                <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgba(156, 146, 172, 0.05) 1px, transparent 0); background-size: 60px 60px;"></div>
            </div>
        </div>

        <!-- Content Container -->
        <div class="relative z-10 flex min-h-screen flex-col items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
            <div class="w-full max-w-md">
            <slot />
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-white/80 dark:from-gray-900/80 to-transparent pointer-events-none"></div>
        <div class="absolute top-0 left-0 right-0 h-32 bg-gradient-to-b from-white/80 dark:from-gray-900/80 to-transparent pointer-events-none"></div>
    </div>
</template>

<style scoped>
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

@keyframes lightning-1 {
    0%, 95%, 100% { opacity: 0; }
    5%, 10% { opacity: 0.8; }
    15%, 20% { opacity: 0.4; }
    25%, 30% { opacity: 0.9; }
    35%, 40% { opacity: 0; }
}

@keyframes lightning-2 {
    0%, 90%, 100% { opacity: 0; }
    10%, 15% { opacity: 0.7; }
    20%, 25% { opacity: 0.3; }
    30%, 35% { opacity: 0.8; }
    40%, 45% { opacity: 0; }
}

@keyframes lightning-3 {
    0%, 85%, 100% { opacity: 0; }
    15%, 20% { opacity: 0.6; }
    25%, 30% { opacity: 0.2; }
    35%, 40% { opacity: 0.7; }
    45%, 50% { opacity: 0; }
}

@keyframes lightning-4 {
    0%, 80%, 100% { opacity: 0; }
    20%, 25% { opacity: 0.5; }
    30%, 35% { opacity: 0.1; }
    40%, 45% { opacity: 0.6; }
    50%, 55% { opacity: 0; }
}

@keyframes lightning-5 {
    0%, 75%, 100% { opacity: 0; }
    25%, 30% { opacity: 0.9; }
    35%, 40% { opacity: 0.3; }
    45%, 50% { opacity: 0.8; }
    55%, 60% { opacity: 0; }
}

@keyframes lightning-6 {
    0%, 70%, 100% { opacity: 0; }
    30%, 35% { opacity: 0.8; }
    40%, 45% { opacity: 0.2; }
    50%, 55% { opacity: 0.7; }
    60%, 65% { opacity: 0; }
}

@keyframes lightning-7 {
    0%, 65%, 100% { opacity: 0; }
    35%, 40% { opacity: 0.7; }
    45%, 50% { opacity: 0.1; }
    55%, 60% { opacity: 0.6; }
    65%, 70% { opacity: 0; }
}

@keyframes lightning-bolt-1 {
    0%, 92%, 100% { opacity: 0; transform: translateY(-10px) -skew-x-12; }
    3%, 8% { opacity: 1; transform: translateY(0) -skew-x-12; }
    13%, 18% { opacity: 0.5; transform: translateY(5px) -skew-x-12; }
    23%, 28% { opacity: 1; transform: translateY(0) -skew-x-12; }
    33%, 38% { opacity: 0; transform: translateY(-5px) -skew-x-12; }
}

@keyframes lightning-bolt-2 {
    0%, 88%, 100% { opacity: 0; transform: translateY(-15px) skew-x-12; }
    8%, 13% { opacity: 1; transform: translateY(0) skew-x-12; }
    18%, 23% { opacity: 0.4; transform: translateY(8px) skew-x-12; }
    28%, 33% { opacity: 1; transform: translateY(0) skew-x-12; }
    38%, 43% { opacity: 0; transform: translateY(-8px) skew-x-12; }
}

@keyframes lightning-bolt-3 {
    0%, 85%, 100% { opacity: 0; transform: translateY(-12px) -skew-x-6; }
    12%, 17% { opacity: 1; transform: translateY(0) -skew-x-6; }
    22%, 27% { opacity: 0.6; transform: translateY(6px) -skew-x-6; }
    32%, 37% { opacity: 1; transform: translateY(0) -skew-x-6; }
    42%, 47% { opacity: 0; transform: translateY(-6px) -skew-x-6; }
}

@keyframes lightning-bolt-svg {
    0%, 90%, 100% { opacity: 0; }
    5%, 10% { opacity: 1; }
    15%, 20% { opacity: 0.3; }
    25%, 30% { opacity: 1; }
    35%, 40% { opacity: 0.5; }
    45%, 50% { opacity: 1; }
    55%, 60% { opacity: 0; }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.animate-lightning-1 {
    animation: lightning-1 8s infinite;
}

.animate-lightning-2 {
    animation: lightning-2 10s infinite;
    animation-delay: 2s;
}

.animate-lightning-3 {
    animation: lightning-3 12s infinite;
    animation-delay: 4s;
}

.animate-lightning-4 {
    animation: lightning-4 9s infinite;
    animation-delay: 6s;
}

.animate-lightning-5 {
    animation: lightning-5 13s infinite;
    animation-delay: 1s;
}

.animate-lightning-6 {
    animation: lightning-6 15s infinite;
    animation-delay: 3s;
}

.animate-lightning-7 {
    animation: lightning-7 17s infinite;
    animation-delay: 5s;
}

.animate-lightning-bolt-1 {
    animation: lightning-bolt-1 7s infinite;
    animation-delay: 1s;
}

.animate-lightning-bolt-2 {
    animation: lightning-bolt-2 9s infinite;
    animation-delay: 3s;
}

.animate-lightning-bolt-3 {
    animation: lightning-bolt-3 11s infinite;
    animation-delay: 5s;
}

.animate-lightning-bolt-svg-1 {
    animation: lightning-bolt-svg 8s infinite;
    animation-delay: 1s;
}

.animate-lightning-bolt-svg-2 {
    animation: lightning-bolt-svg 10s infinite;
    animation-delay: 3s;
}

.animate-lightning-bolt-svg-3 {
    animation: lightning-bolt-svg 12s infinite;
    animation-delay: 5s;
}

.animate-lightning-bolt-svg-4 {
    animation: lightning-bolt-svg 9s infinite;
    animation-delay: 7s;
}
</style>
