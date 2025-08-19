<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-100 flex flex-col">
    <header class="py-8 px-4 flex flex-col items-center">
      <div class="flex items-center space-x-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
        <span class="text-3xl font-extrabold text-blue-900 tracking-wide">SwiftNotify</span>
      </div>
      <h1 class="mt-6 text-4xl font-bold text-gray-800 text-center max-w-2xl">Effortless Group SMS Alerts </h1>
      <p class="mt-4 text-lg text-gray-600 text-center max-w-xl">
        SwiftNotify lets organizations, teams, and businesses manage contacts, organize groups, and send instant SMS alerts to everyone who matters—right from your browser. <span class='font-semibold text-blue-800'>If you are a citizen or recipient, you do not need to register or log in.</span> This platform is for authorized senders only.
      </p>
      <div class="mt-8 flex gap-4">
        <a href="/register" class="bg-blue-700 hover:bg-blue-800 text-white px-8 py-3 rounded-lg font-semibold shadow transition">Get Started</a>
        <a href="/login" class="bg-white border border-blue-700 text-blue-700 px-8 py-3 rounded-lg font-semibold shadow hover:bg-blue-50 transition">Log In</a>
      </div>
    </header>
    <main class="flex-1 flex flex-col items-center justify-center px-4">
      <section class="max-w-4xl w-full mt-12">
        <div class="mb-10 bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
          <h2 class="text-xl font-bold text-blue-900 mb-2">Are you a citizen or recipient?</h2>
          <p class="text-blue-800">You do <span class="font-bold">not</span> need to register or log in. SwiftNotify is used by organizations to send you important SMS alerts. If you have questions about a message you received, please contact the sender directly.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="flex flex-col items-center text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-indigo-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2v-8a2 2 0 012-2h2M12 15v2m0-6v2m0-6v2" /></svg>
            <h3 class="font-bold text-lg text-gray-800">Bulk SMS Alerts</h3>
            <p class="text-gray-500">Send messages to entire groups or select contacts in seconds.</p>
          </div>
          <div class="flex flex-col items-center text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8a9 9 0 100-18 9 9 0 000 18z" /></svg>
            <h3 class="font-bold text-lg text-gray-800">Easy Group Management</h3>
            <p class="text-gray-500">Organize contacts into groups for targeted communication.</p>
          </div>
          <div class="flex flex-col items-center text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-pink-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
            <h3 class="font-bold text-lg text-gray-800">Admin & Agent Roles</h3>
            <p class="text-gray-500">Control access, manage users, and keep your data secure.</p>
          </div>
        </div>
      </section>
    </main>
    <footer class="mt-16 py-6 text-center text-gray-400 text-sm">
      &copy; {{ new Date().getFullYear() }} SwiftNotify. All rights reserved.
    </footer>
  </div>
</template> 