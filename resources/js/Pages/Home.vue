<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 relative overflow-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0 overflow-hidden">
      <!-- Floating Orbs -->
      <div class="absolute top-20 left-10 w-72 h-72 bg-blue-500/20 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob"></div>
      <div class="absolute top-40 right-10 w-72 h-72 bg-purple-500/20 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob animation-delay-2000"></div>
      <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-500/20 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob animation-delay-4000"></div>
      
      <!-- Lightning Effects -->
      <div class="absolute top-0 left-1/4 w-1 h-screen bg-gradient-to-b from-transparent via-yellow-300 to-transparent opacity-0 animate-lightning-1"></div>
      <div class="absolute top-10 right-1/3 w-1 h-screen bg-gradient-to-b from-transparent via-blue-300 to-transparent opacity-0 animate-lightning-2"></div>
      <div class="absolute top-20 left-1/2 w-1 h-screen bg-gradient-to-b from-transparent via-purple-300 to-transparent opacity-0 animate-lightning-3"></div>
      
      <!-- Grid Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgba(255, 255, 255, 0.1) 1px, transparent 0); background-size: 60px 60px;"></div>
      </div>
    </div>

    <!-- Content -->
    <div class="relative z-10">
      <!-- Header -->
      <header class="py-12 px-6 flex flex-col items-center">
        <div class="flex items-center space-x-4 mb-8">
          <div class="relative">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full blur-lg opacity-75 animate-pulse"></div>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-white relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </div>
          <span class="text-5xl font-black text-white tracking-tight bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">SwiftNotify</span>
        </div>
        
        <h1 class="text-6xl md:text-7xl font-black text-white text-center max-w-5xl leading-tight mb-6">
          <span class="bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
            Effortless
          </span>
          <br />
          <span class="text-white">Group SMS Alerts</span>
        </h1>
        
        <p class="text-xl md:text-2xl text-gray-300 text-center max-w-4xl leading-relaxed mb-10">
          Transform your communication with <span class="font-bold text-blue-400">SwiftNotify</span> - the ultimate platform for organizations to manage contacts, organize groups, and send instant SMS alerts to everyone who matters.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-6 mb-16">
          <a href="/register" class="group relative px-10 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-2xl font-bold text-lg shadow-2xl hover:shadow-blue-500/25 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1">
            <span class="relative z-10">Get Started Free</span>
            <div class="absolute inset-0 bg-gradient-to-r from-blue-700 to-purple-700 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          </a>
          <a href="/login" class="group px-10 py-4 bg-white/10 backdrop-blur-sm border border-white/20 text-white rounded-2xl font-bold text-lg shadow-2xl hover:bg-white/20 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1">
            Sign In
          </a>
        </div>
      </header>

      <!-- Main Content -->
      <main class="px-6 pb-20">
        <!-- Citizen Notice -->
        <section class="max-w-4xl mx-auto mb-20">
          <div class="bg-gradient-to-r from-blue-500/20 to-purple-500/20 backdrop-blur-sm border border-white/20 rounded-3xl p-8 text-center">
            <div class="flex items-center justify-center mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <h2 class="text-2xl font-bold text-white">For Citizens & Recipients</h2>
            </div>
            <p class="text-lg text-gray-300 leading-relaxed">
              <span class="font-bold text-blue-400">You do NOT need to register or log in.</span> SwiftNotify is used by organizations to send you important SMS alerts. 
              If you have questions about a message you received, please contact the sender directly.
            </p>
          </div>
        </section>

        <!-- Features Grid -->
        <section class="max-w-7xl mx-auto mb-20">
          <h2 class="text-4xl font-bold text-white text-center mb-16">
            Why Choose <span class="bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">SwiftNotify</span>?
          </h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="group bg-gradient-to-br from-blue-500/10 to-purple-500/10 backdrop-blur-sm border border-white/10 rounded-2xl p-8 hover:border-white/30 transition-all duration-300 transform hover:scale-105">
              <div class="flex items-center mb-6">
                <div class="p-3 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                  </svg>
                </div>
                <h3 class="text-xl font-bold text-white">Lightning Fast SMS</h3>
              </div>
              <p class="text-gray-300 leading-relaxed">
                Send bulk SMS alerts to entire groups or select contacts in seconds. 
                Real-time delivery with instant confirmation.
              </p>
            </div>

            <!-- Feature 2 -->
            <div class="group bg-gradient-to-br from-green-500/10 to-blue-500/10 backdrop-blur-sm border border-white/10 rounded-2xl p-8 hover:border-white/30 transition-all duration-300 transform hover:scale-105">
              <div class="flex items-center mb-6">
                <div class="p-3 bg-gradient-to-r from-green-500 to-green-600 rounded-xl mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
                <h3 class="text-xl font-bold text-white">Smart Group Management</h3>
              </div>
              <p class="text-gray-300 leading-relaxed">
                Organize contacts into intelligent groups with map-based area selection. 
                Perfect for targeted communication and emergency alerts.
              </p>
            </div>

            <!-- Feature 3 -->
            <div class="group bg-gradient-to-br from-purple-500/10 to-pink-500/10 backdrop-blur-sm border border-white/10 rounded-2xl p-8 hover:border-white/30 transition-all duration-300 transform hover:scale-105">
              <div class="flex items-center mb-6">
                <div class="p-3 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                  </svg>
                </div>
                <h3 class="text-xl font-bold text-white">Enterprise Security</h3>
              </div>
              <p class="text-gray-300 leading-relaxed">
                Role-based access control with admin and agent permissions. 
                Secure, encrypted communication with detailed audit trails.
              </p>
            </div>

            <!-- Feature 4 -->
            <div class="group bg-gradient-to-br from-pink-500/10 to-red-500/10 backdrop-blur-sm border border-white/10 rounded-2xl p-8 hover:border-white/30 transition-all duration-300 transform hover:scale-105">
              <div class="flex items-center mb-6">
                <div class="p-3 bg-gradient-to-r from-pink-500 to-pink-600 rounded-xl mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                  </svg>
                </div>
                <h3 class="text-xl font-bold text-white">Advanced Analytics</h3>
              </div>
              <p class="text-gray-300 leading-relaxed">
                Track message delivery, open rates, and engagement metrics. 
                Comprehensive reporting for campaign optimization.
              </p>
            </div>

            <!-- Feature 5 -->
            <div class="group bg-gradient-to-br from-yellow-500/10 to-orange-500/10 backdrop-blur-sm border border-white/10 rounded-2xl p-8 hover:border-white/30 transition-all duration-300 transform hover:scale-105">
              <div class="flex items-center mb-6">
                <div class="p-3 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <h3 class="text-xl font-bold text-white">Scheduled Messages</h3>
              </div>
              <p class="text-gray-300 leading-relaxed">
                Schedule messages for optimal delivery times. 
                Set up recurring alerts and automated notifications.
              </p>
            </div>

            <!-- Feature 6 -->
            <div class="group bg-gradient-to-br from-indigo-500/10 to-blue-500/10 backdrop-blur-sm border border-white/10 rounded-2xl p-8 hover:border-white/30 transition-all duration-300 transform hover:scale-105">
              <div class="flex items-center mb-6">
                <div class="p-3 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                  </svg>
                </div>
                <h3 class="text-xl font-bold text-white">User-Friendly Interface</h3>
              </div>
              <p class="text-gray-300 leading-relaxed">
                Intuitive dashboard with drag-and-drop functionality. 
                Mobile-responsive design for on-the-go management.
              </p>
            </div>
          </div>
        </section>

        <!-- Stats Section -->
        <section class="max-w-6xl mx-auto mb-20">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="text-center">
              <div class="text-4xl font-bold text-blue-400 mb-2">99.9%</div>
              <div class="text-gray-300">Delivery Rate</div>
            </div>
            <div class="text-center">
              <div class="text-4xl font-bold text-purple-400 mb-2">10M+</div>
              <div class="text-gray-300">Messages Sent</div>
            </div>
            <div class="text-center">
              <div class="text-4xl font-bold text-green-400 mb-2">500+</div>
              <div class="text-gray-300">Organizations</div>
            </div>
            <div class="text-center">
              <div class="text-4xl font-bold text-pink-400 mb-2">24/7</div>
              <div class="text-gray-300">Support</div>
            </div>
          </div>
        </section>

        <!-- CTA Section -->
        <section class="max-w-4xl mx-auto text-center">
          <div class="bg-gradient-to-r from-blue-600/20 to-purple-600/20 backdrop-blur-sm border border-white/20 rounded-3xl p-12">
            <h2 class="text-3xl font-bold text-white mb-6">
              Ready to Transform Your Communication?
            </h2>
            <p class="text-xl text-gray-300 mb-8 leading-relaxed">
              Join thousands of organizations already using SwiftNotify to deliver critical messages instantly and reliably.
            </p>
            <div class="flex flex-col sm:flex-row gap-6 justify-center">
              <a href="/register" class="group px-10 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-2xl font-bold text-lg shadow-2xl hover:shadow-blue-500/25 transition-all duration-300 transform hover:scale-105">
                Start Free Trial
              </a>
              <a href="/login" class="px-10 py-4 bg-white/10 backdrop-blur-sm border border-white/20 text-white rounded-2xl font-bold text-lg hover:bg-white/20 transition-all duration-300">
                Sign In
              </a>
            </div>
          </div>
        </section>
      </main>

      <!-- Footer -->
      <footer class="mt-20 py-8 text-center text-gray-400 border-t border-white/10">
        <div class="flex items-center justify-center mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
          <span class="text-lg font-bold text-white">SwiftNotify</span>
        </div>
        <p class="text-sm">
          &copy; {{ new Date().getFullYear() }} SwiftNotify. All rights reserved. | 
          <a href="#" class="hover:text-blue-400 transition-colors">Privacy Policy</a> | 
          <a href="#" class="hover:text-blue-400 transition-colors">Terms of Service</a>
        </p>
      </footer>
    </div>
  </div>
</template>

<style scoped>
@keyframes blob {
  0% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(30px, -50px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
  100% { transform: translate(0px, 0px) scale(1); }
}

@keyframes lightning-1 {
  0%, 95%, 100% { opacity: 0; }
  5%, 10% { opacity: 0.8; }
  15%, 20% { opacity: 0.4; }
  25%, 30% { opacity: 0.9; }
  35%, 40% { opacity: 0; }
}

@keyframes lightning-2 {
  0%, 85%, 100% { opacity: 0; }
  10%, 15% { opacity: 0.7; }
  20%, 25% { opacity: 0.3; }
  30%, 35% { opacity: 0.8; }
  40%, 45% { opacity: 0; }
}

@keyframes lightning-3 {
  0%, 90%, 100% { opacity: 0; }
  15%, 20% { opacity: 0.6; }
  25%, 30% { opacity: 0.2; }
  35%, 40% { opacity: 0.7; }
  45%, 50% { opacity: 0; }
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
</style> 