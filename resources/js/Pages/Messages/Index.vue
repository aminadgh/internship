<template>
  <AuthenticatedLayout>
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Send SMS Alert</h1>
          <p class="mt-2 text-gray-600 dark:text-gray-400">Send important alerts and notifications to your contact groups</p>
        </div>

        <!-- Progress Modal -->
        <div v-if="showProgress" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
          <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3 text-center">
              <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900">
                <PaperAirplaneIcon class="h-6 w-6 text-blue-600 dark:text-blue-400" />
              </div>
              <h3 class="text-lg font-medium text-gray-900 dark:text-white mt-4">Sending SMS Messages</h3>
              <div class="mt-4">
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                  <div 
                    class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" 
                    :style="{ width: progressPercentage + '%' }"
                  ></div>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ progressMessage }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">{{ sentCount }} of {{ totalCount }} sent</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Summary Modal -->
        <div v-if="showSummary" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
          <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3 text-center">
              <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full" :class="summaryIconClass">
                <component :is="summaryIcon" class="h-6 w-6" :class="summaryIconColor" />
              </div>
              <h3 class="text-lg font-medium text-gray-900 dark:text-white mt-4">{{ summaryTitle }}</h3>
              <div class="mt-4 space-y-2">
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600 dark:text-gray-400">Total Recipients:</span>
                  <span class="font-medium">{{ summaryStats.total }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-green-600 dark:text-green-400">Successfully Sent:</span>
                  <span class="font-medium text-green-600 dark:text-green-400">{{ summaryStats.sent }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-red-600 dark:text-red-400">Failed:</span>
                  <span class="font-medium text-red-600 dark:text-red-400">{{ summaryStats.failed }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-yellow-600 dark:text-yellow-400">Pending:</span>
                  <span class="font-medium text-yellow-600 dark:text-yellow-400">{{ summaryStats.pending }}</span>
                </div>
              </div>
              <div class="mt-6 flex space-x-3">
                <button
                  @click="closeSummary"
                  class="flex-1 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                >
                  Close
                </button>
                <Link
                  :href="route('messages.history')"
                  class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                  View History
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Smart Suggestions Modal -->
        <div v-if="showSmartSuggestions" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
          <div class="relative top-10 mx-auto p-6 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                <LightBulbIcon class="w-8 h-8 text-yellow-500 mr-3" />
                Smart Suggestions
              </h3>
              <button
                @click="showSmartSuggestions = false"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
              >
                <XMarkIcon class="w-6 h-6" />
              </button>
            </div>

            <!-- Smart Group Suggestions -->
            <div v-if="smartSuggestions.groups.length > 0" class="mb-8">
              <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <UserGroupIcon class="w-5 h-5 text-blue-500 mr-2" />
                Suggested Groups
              </h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div
                  v-for="suggestion in smartSuggestions.groups.slice(0, 6)"
                  :key="suggestion.group.id"
                  class="p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:border-blue-300 dark:hover:border-blue-600 transition-colors cursor-pointer"
                  @click="applySmartGroups([suggestion.group.id])"
                >
                  <div class="flex justify-between items-start mb-2">
                    <h5 class="font-medium text-gray-900 dark:text-white">{{ suggestion.group.name }}</h5>
                    <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs rounded-full">
                      Score: {{ suggestion.score }}
                    </span>
                  </div>
                  <p class="text-sm text-gray-600 dark:text-gray-400">{{ suggestion.reason }}</p>
                  <div class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                    {{ suggestion.group.contacts_count }} contacts
                  </div>
                </div>
              </div>
            </div>

            <!-- Smart Group Combinations -->
            <div v-if="smartSuggestions.combinations.length > 0" class="mb-8">
              <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <SparklesIcon class="w-5 h-5 text-purple-500 mr-2" />
                Smart Combinations
              </h4>
              <div class="space-y-3">
                <div
                  v-for="combination in smartSuggestions.combinations.slice(0, 4)"
                  :key="combination.name"
                  class="p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:border-purple-300 dark:hover:border-purple-600 transition-colors cursor-pointer"
                  @click="applySmartGroups(combination.groups)"
                >
                  <div class="flex justify-between items-start mb-2">
                    <h5 class="font-medium text-gray-900 dark:text-white">{{ combination.name }}</h5>
                    <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 text-xs rounded-full">
                      Score: {{ combination.score }}
                    </span>
                  </div>
                  <p class="text-sm text-gray-600 dark:text-gray-400">{{ combination.reason }}</p>
                </div>
              </div>
            </div>

            <!-- Smart Template Suggestions -->
            <div v-if="smartSuggestions.templates.length > 0">
              <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <SparklesIcon class="w-5 h-5 text-green-500 mr-2" />
                Template Suggestions
              </h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div
                  v-for="suggestion in smartSuggestions.templates.slice(0, 4)"
                  :key="suggestion.template.id"
                  class="p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:border-green-300 dark:hover:border-green-600 transition-colors cursor-pointer"
                  @click="applySmartTemplate(suggestion.template)"
                >
                  <div class="flex justify-between items-start mb-2">
                    <h5 class="font-medium text-gray-900 dark:text-white">{{ suggestion.template.name }}</h5>
                    <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs rounded-full">
                      Score: {{ suggestion.score }}
                    </span>
                  </div>
                  <p class="text-sm text-gray-600 dark:text-gray-400">{{ suggestion.template.content.substring(0, 80) }}...</p>
                  <div class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                    Matches: {{ suggestion.matched_keywords.join(', ') }}
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-8 flex justify-end">
              <button
                @click="showSmartSuggestions = false"
                class="px-6 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
              >
                Close
              </button>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
          <!-- Main Form - Takes more space -->
          <div class="xl:col-span-3">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
              <div class="p-8">
                <div class="mb-8">
                  <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">Compose Message</h3>
                  <p class="text-gray-600 dark:text-gray-400">Create and send your SMS alert to selected groups</p>
                </div>
                
                <form @submit.prevent="submit">
                  <div class="space-y-8">
                    <!-- Group Selection -->
                    <div>
                      <label class="block text-lg font-medium text-gray-900 dark:text-white mb-4">Select Groups</label>
                      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="group in groups" :key="group.id" class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                          <input
                            type="checkbox"
                            :id="'group-' + group.id"
                            :value="group.id"
                            v-model="form.group_ids"
                            class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded"
                          />
                          <label :for="'group-' + group.id" class="ml-3 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                            <div class="font-medium">{{ group.name }}</div>
                            <div class="text-gray-500 dark:text-gray-400">{{ group.contacts_count }} contacts</div>
                          </label>
                        </div>
                      </div>
                      <div v-if="form.errors.group_ids" class="text-red-500 text-sm mt-2">{{ form.errors.group_ids }}</div>
                      
                      <!-- Debug info -->
                      <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        <div>Selected groups: {{ form.group_ids.length }}</div>
                        <div>Total recipients: {{ totalRecipients }}</div>
                        <div>Broadcast type: {{ form.broadcast_type }}</div>
                        <div>Emergency mode: {{ form.is_emergency ? 'Yes' : 'No' }}</div>
                      </div>
                    </div>

                    <!-- Message Content -->
                    <div>
                      <label class="block text-lg font-medium text-gray-900 dark:text-white mb-4">
                        Message Content
                        <span class="text-gray-500 dark:text-gray-400 text-base font-normal">({{ form.content.length }}/160 characters)</span>
                      </label>
                      <textarea 
                        v-model="form.content" 
                        rows="6" 
                        placeholder="Type your message here..."
                        class="w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white text-base py-3 px-4 resize-none"
                        :class="{ 'border-red-300 dark:border-red-500': form.content.length > 160 }"
                      ></textarea>
                      <div v-if="form.errors.content" class="text-red-500 text-sm mt-2">{{ form.errors.content }}</div>
                      <div v-if="form.content.length > 160" class="text-red-500 text-sm mt-2">
                        Message is too long. Maximum 160 characters allowed.
                      </div>
                      
                      <!-- Smart Suggestions Button -->
                      <div class="mt-3">
                        <button
                          type="button"
                          @click="getSmartSuggestions"
                          :disabled="!form.content.trim() || isLoadingSuggestions"
                          class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-500 to-blue-500 text-white text-sm font-medium rounded-lg hover:from-purple-600 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                        >
                          <div v-if="isLoadingSuggestions" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                          <SparklesIcon v-else class="w-4 h-4 mr-2" />
                          {{ isLoadingSuggestions ? 'Getting Suggestions...' : 'Get Smart Suggestions' }}
                        </button>
                      </div>
                    </div>

                    <!-- Cost Preview -->
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                          <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599 1"></path>
                          </svg>
                          <span class="text-sm font-medium text-green-800 dark:text-green-200">Cost Preview</span>
                        </div>
                        <div class="text-right">
                          <div class="text-lg font-bold text-green-800 dark:text-green-200">
                            ${{ calculateEstimatedCost().toFixed(4) }}
                          </div>
                          <div class="text-xs text-green-600 dark:text-green-400">
                            {{ totalRecipients }} recipients × ${{ (calculateEstimatedCost() / totalRecipients).toFixed(4) }} per SMS
                          </div>
                        </div>
                      </div>
                      <div class="mt-2 text-xs text-green-600 dark:text-green-400">
                        Based on Twilio US rates: $0.0079 per SMS segment (160 characters)
                      </div>
                    </div>

                    <!-- Message Priority and Scheduling Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                      <!-- Emergency Broadcast System -->
                      <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                          <div class="p-2 bg-red-100 dark:bg-red-800 rounded-lg mr-3">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                          </div>
                          <div>
                            <h3 class="text-lg font-semibold text-red-900 dark:text-red-100">Emergency Broadcast System</h3>
                            <p class="text-sm text-red-700 dark:text-red-300">Send urgent alerts to all contacts immediately</p>
                          </div>
                        </div>
                        
                        <!-- Emergency Toggle -->
                        <div class="mb-4">
                          <label class="flex items-center cursor-pointer">
                            <input
                              type="checkbox"
                              v-model="form.is_emergency"
                              class="h-5 w-5 text-red-600 focus:ring-red-500 border-gray-300 dark:border-gray-600 rounded"
                            />
                            <span class="ml-3 text-lg font-medium text-red-900 dark:text-red-100">🚨 EMERGENCY BROADCAST</span>
                          </label>
                          <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                            When enabled, message will be sent to ALL contacts immediately with highest priority
                          </p>
                        </div>

                        <!-- Emergency Category (shown when emergency is enabled) -->
                        <div v-if="form.is_emergency" class="mb-4">
                          <label class="block text-sm font-medium text-red-900 dark:text-red-100 mb-2">Emergency Category *</label>
                          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div v-for="category in emergencyCategories" :key="category.value" class="flex items-center">
                              <input
                                type="radio"
                                :id="'category-' + category.value"
                                :value="category.value"
                                v-model="form.emergency_category"
                                class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 dark:border-gray-600"
                                required
                              />
                              <label :for="'category-' + category.value" class="ml-2 text-sm cursor-pointer">
                                <span :class="category.color">{{ category.label }}</span>
                              </label>
                            </div>
                          </div>
                        </div>

                        <!-- Broadcast Type (shown when emergency is enabled) -->
                        <div v-if="form.is_emergency" class="mb-4">
                          <label class="block text-sm font-medium text-red-900 dark:text-red-100 mb-2">Broadcast Type</label>
                          <div class="space-y-3">
                            <div v-for="type in broadcastTypes" :key="type.value" class="flex items-center">
                              <input
                                type="radio"
                                :id="'broadcast-' + type.value"
                                :value="type.value"
                                v-model="form.broadcast_type"
                                class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 dark:border-gray-600"
                              />
                              <label :for="'broadcast-' + type.value" class="ml-2 text-sm cursor-pointer">
                                <div class="font-medium text-red-900 dark:text-red-100">{{ type.label }}</div>
                                <div class="text-xs text-red-700 dark:text-red-300">{{ type.description }}</div>
                              </label>
                            </div>
                          </div>
                        </div>

                        <!-- Emergency Warning -->
                        <div v-if="form.is_emergency" class="bg-red-100 dark:bg-red-800 border border-red-300 dark:border-red-700 rounded-lg p-4">
                          <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <div class="text-sm text-red-800 dark:text-red-200">
                              <strong>Emergency Mode Active:</strong> This message will be sent immediately to all selected recipients with highest priority. 
                              Non-admin users will require approval before sending.
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Message Priority -->
                      <div>
                        <label class="block text-lg font-medium text-gray-900 dark:text-white mb-4">Message Priority</label>
                        <div class="space-y-4">
                          <div class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <input
                              type="radio"
                              id="priority-low"
                              value="low"
                              v-model="form.priority"
                              :disabled="form.is_emergency"
                              class="h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300 dark:border-gray-600 disabled:opacity-50"
                            />
                            <label for="priority-low" class="ml-3 text-sm text-gray-700 dark:text-gray-300 flex items-center cursor-pointer">
                              <span class="w-3 h-3 bg-green-500 rounded-full mr-3"></span>
                              <div>
                                <div class="font-medium">Low</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Standard delivery</div>
                              </div>
                            </label>
                          </div>
                          <div class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <input
                              type="radio"
                              id="priority-normal"
                              value="normal"
                              v-model="form.priority"
                              :disabled="form.is_emergency"
                              class="h-5 w-5 text-yellow-600 focus:ring-yellow-500 border-gray-300 dark:border-gray-600 disabled:opacity-50"
                            />
                            <label for="priority-normal" class="ml-3 text-sm text-gray-700 dark:text-gray-300 flex items-center cursor-pointer">
                              <span class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></span>
                              <div>
                                <div class="font-medium">Normal</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Priority delivery</div>
                              </div>
                            </label>
                          </div>
                          <div class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <input
                              type="radio"
                              id="priority-high"
                              value="high"
                              v-model="form.priority"
                              :disabled="form.is_emergency"
                              class="h-5 w-5 text-red-600 focus:ring-red-500 border-gray-300 dark:border-gray-600 disabled:opacity-50"
                            />
                            <label for="priority-high" class="ml-3 text-sm text-gray-700 dark:text-gray-300 flex items-center cursor-pointer">
                              <span class="w-3 h-3 bg-red-500 rounded-full mr-3"></span>
                              <div>
                                <div class="font-medium">High</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Immediate delivery</div>
                              </div>
                            </label>
                          </div>
                          <div class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <input
                              type="radio"
                              id="priority-critical"
                              value="critical"
                              v-model="form.priority"
                              :disabled="form.is_emergency"
                              class="h-5 w-5 text-red-600 focus:ring-red-500 border-gray-300 dark:border-gray-600 disabled:opacity-50"
                            />
                            <label for="priority-critical" class="ml-3 text-sm text-gray-700 dark:text-gray-300 flex items-center cursor-pointer">
                              <span class="w-3 h-3 bg-red-500 rounded-full mr-3"></span>
                              <div>
                                <div class="font-medium">Critical</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Highest priority delivery</div>
                              </div>
                            </label>
                          </div>
                        </div>
                      </div>

                      <!-- Message Scheduling -->
                      <div>
                        <label class="block text-lg font-medium text-gray-900 dark:text-white mb-4">Message Scheduling</label>
                        <div class="space-y-4">
                          <div class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <input
                              type="radio"
                              id="send-now"
                              value="now"
                              v-model="form.schedule_type"
                              :disabled="form.is_emergency"
                              class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 disabled:opacity-50"
                            />
                            <label for="send-now" class="ml-3 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                              <div class="font-medium">Send immediately</div>
                              <div class="text-xs text-gray-500 dark:text-gray-400">Message will be sent right away</div>
                            </label>
                          </div>
                          <div class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <input
                              type="radio"
                              id="send-later"
                              value="later"
                              v-model="form.schedule_type"
                              :disabled="form.is_emergency"
                              class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 disabled:opacity-50"
                            />
                            <label for="send-later" class="ml-3 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                              <div class="font-medium">Schedule for later</div>
                              <div class="text-xs text-gray-500 dark:text-gray-400">Choose date and time</div>
                            </label>
                          </div>
                          
                          <!-- Schedule Date/Time (shown when "later" is selected) -->
                          <div v-if="form.schedule_type === 'later'" class="ml-6 space-y-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                              <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date</label>
                                <input
                                  type="date"
                                  v-model="form.schedule_date"
                                  :min="new Date().toISOString().split('T')[0]"
                                  class="w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:text-white py-2 px-3"
                                />
                              </div>
                              <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time</label>
                                <input
                                  type="time"
                                  v-model="form.schedule_time"
                                  class="w-full border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:text-white py-2 px-3"
                                />
                              </div>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg">
                              ⏰ Message will be sent at {{ formatScheduledTime() }}
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                      <button 
                        type="submit" 
                        :disabled="isSubmitting || form.content.length > 160 || (!form.is_emergency && form.group_ids.length === 0)"
                        class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed flex items-center text-lg font-medium transition-colors"
                      >
                        <PaperAirplaneIcon v-if="!isSubmitting" class="w-6 h-6 mr-3" />
                        <div v-else class="w-6 h-6 mr-3 animate-spin rounded-full border-2 border-white border-t-transparent"></div>
                        {{ isSubmitting ? 'Sending...' : (form.schedule_type === 'later' ? 'Schedule SMS' : 'Send SMS') }}
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- Sidebar - Takes less space but still functional -->
          <div class="xl:col-span-1">
            <!-- Recipients Preview -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 border border-gray-200 dark:border-gray-700">
              <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Recipients Preview</h3>
                <div v-if="selectedGroups.length === 0" class="text-center py-6">
                  <UserGroupIcon class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" />
                  <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Select groups to see recipients</p>
                </div>
                <div v-else class="space-y-4">
                  <div v-for="group in selectedGroups" :key="group.id" class="border-l-4 border-indigo-500 pl-4 py-2">
                    <h4 class="font-medium text-sm text-gray-900 dark:text-white">{{ group.name }}</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ group.contacts_count }} contacts</p>
                  </div>
                  <div class="pt-4 border-t border-gray-200 dark:border-gray-600">
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                      Total: {{ totalRecipients }} recipients
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Templates -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
              <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Quick Templates</h3>
                
                <!-- Template Categories -->
                <div class="space-y-5">
                  <!-- Emergency Templates -->
                  <div>
                    <h4 class="text-sm font-semibold text-red-700 dark:text-red-400 mb-3 flex items-center">
                      <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                      Emergency
                    </h4>
                    <div class="space-y-2">
                      <button
                        v-for="(tpl, idx) in alertTemplates.filter(t => t.category === 'emergency')"
                        :key="tpl.name"
                        @click="selectTemplate(tpl)"
                        class="w-full text-left p-3 border border-red-200 dark:border-red-800 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 focus:outline-none focus:ring-2 focus:ring-red-500 text-sm transition-colors"
                      >
                        <div class="font-medium text-red-900 dark:text-red-100">{{ tpl.name }}</div>
                        <div class="text-xs text-red-600 dark:text-red-400 mt-1">{{ tpl.content.substring(0, 50) }}...</div>
                      </button>
                    </div>
                  </div>
                  
                  <!-- Service Templates -->
                  <div>
                    <h4 class="text-sm font-semibold text-blue-700 dark:text-blue-400 mb-3 flex items-center">
                      <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                      Service
                    </h4>
                    <div class="space-y-2">
                      <button
                        v-for="(tpl, idx) in alertTemplates.filter(t => t.category === 'service' || t.category === 'maintenance')"
                        :key="tpl.name"
                        @click="selectTemplate(tpl)"
                        class="w-full text-left p-3 border border-blue-200 dark:border-blue-800 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm transition-colors"
                      >
                        <div class="font-medium text-blue-900 dark:text-blue-100">{{ tpl.name }}</div>
                        <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">{{ tpl.content.substring(0, 50) }}...</div>
                      </button>
                    </div>
                  </div>
                  
                  <!-- Community Templates -->
                  <div>
                    <h4 class="text-sm font-semibold text-green-700 dark:text-green-400 mb-3 flex items-center">
                      <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                      Community
                    </h4>
                    <div class="space-y-2">
                      <button
                        v-for="(tpl, idx) in alertTemplates.filter(t => t.category === 'community')"
                        :key="tpl.name"
                        @click="selectTemplate(tpl)"
                        class="w-full text-left p-3 border border-green-200 dark:border-green-800 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm transition-colors"
                      >
                        <div class="font-medium text-green-900 dark:text-green-100">{{ tpl.name }}</div>
                        <div class="text-xs text-green-600 dark:text-green-400 mt-1">{{ tpl.content.substring(0, 50) }}...</div>
                      </button>
                    </div>
                  </div>
                  
                  <!-- Custom Template -->
                  <div>
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                      <span class="w-2 h-2 bg-gray-500 rounded-full mr-2"></span>
                      Custom
                    </h4>
                    <div class="space-y-2">
                      <button
                        v-for="(tpl, idx) in alertTemplates.filter(t => t.category === 'custom')"
                        :key="tpl.name"
                        @click="selectTemplate(tpl)"
                        class="w-full text-left p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm transition-colors"
                      >
                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ tpl.name }}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ tpl.content.substring(0, 50) }}...</div>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
  PaperAirplaneIcon,
  UserGroupIcon,
  CheckCircleIcon,
  XCircleIcon,
  ExclamationTriangleIcon,
  LightBulbIcon,
  SparklesIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
  groups: Array
});

const form = useForm({
  group_ids: [],
  content: '',
  is_emergency: false,
  priority: 'normal',
  broadcast_type: 'group',
  emergency_category: '',
  schedule_type: 'now',
  schedule_date: '',
  schedule_time: '',
  requires_approval: false
});

// Emergency categories
const emergencyCategories = [
  { value: 'health', label: '🏥 Health Emergency', color: 'text-red-600' },
  { value: 'safety', label: '🚨 Safety Alert', color: 'text-orange-600' },
  { value: 'utility', label: '💧 Utility Interruption', color: 'text-blue-600' },
  { value: 'weather', label: '⛈️ Weather Warning', color: 'text-yellow-600' },
  { value: 'security', label: '🔒 Security Alert', color: 'text-purple-600' },
  { value: 'community', label: '📢 Community Notice', color: 'text-green-600' },
  { value: 'other', label: '⚠️ Other Emergency', color: 'text-gray-600' }
];

// Priority levels
const priorityLevels = [
  { value: 'low', label: 'Low Priority', color: 'text-gray-600', bg: 'bg-gray-100' },
  { value: 'normal', label: 'Normal Priority', color: 'text-blue-600', bg: 'bg-blue-100' },
  { value: 'high', label: 'High Priority', color: 'text-orange-600', bg: 'bg-orange-100' },
  { value: 'critical', label: 'Critical Priority', color: 'text-red-600', bg: 'bg-red-100' }
];

// Broadcast types
const broadcastTypes = [
  { value: 'group', label: 'Specific Groups', description: 'Send to selected contact groups' },
  { value: 'zone', label: 'Geographic Zone', description: 'Send to contacts in specific areas' },
  { value: 'all', label: 'All Contacts', description: 'Send to ALL contacts in the system' }
];

const alertTemplates = [
  // Emergency Templates
  { 
    name: '🚨 Emergency Alert', 
    content: 'EMERGENCY ALERT: [details]. Please follow official instructions immediately.',
    category: 'emergency',
    value: 'other'
  },
  { 
    name: '🏥 Health Warning', 
    content: 'HEALTH ALERT: [details]. Please take necessary precautions.',
    category: 'emergency',
    value: 'health'
  },
  { 
    name: '⛈️ Weather Warning', 
    content: 'WEATHER WARNING: [details]. Stay safe and avoid unnecessary travel.',
    category: 'emergency',
    value: 'weather'
  },
  
  // Service Interruption Templates
  { 
    name: '💧 Water Outage', 
    content: 'Water service interruption in your area. Expected restoration: [time]. We apologize for the inconvenience.',
    category: 'service'
  },
  { 
    name: '⚡ Power Outage', 
    content: 'Power interruption due to maintenance. Expected restoration: [time]. Thank you for your understanding.',
    category: 'service'
  },
  { 
    name: '🔧 Scheduled Maintenance', 
    content: 'Scheduled maintenance in your area. Service will be restored by [time].',
    category: 'maintenance'
  },
  
  // Community Templates
  { 
    name: '📢 Community Notice', 
    content: 'COMMUNITY NOTICE: [details]. For more information, contact [contact].',
    category: 'community'
  },
  { 
    name: '🎉 Event Announcement', 
    content: 'EVENT: [event details] on [date] at [location]. All welcome!',
    category: 'community'
  },
  
  // Custom Template
  { 
    name: '✏️ Custom Message', 
    content: '[Your custom message here]',
    category: 'custom'
  }
];

const isSubmitting = ref(false);
const showProgress = ref(false);
const progressPercentage = ref(0);
const progressMessage = ref('Preparing to send messages...');
const sentCount = ref(0);
const totalCount = ref(0);
const showSummary = ref(false);
const summaryStats = ref({ total: 0, sent: 0, failed: 0, pending: 0 });
const summaryTitle = ref('Message Sent Successfully!');
const summaryIcon = ref(CheckCircleIcon);
const summaryIconClass = ref('bg-green-100 dark:bg-green-900');
const summaryIconColor = ref('text-green-600 dark:text-green-400');

// Smart suggestions
const smartSuggestions = ref({
  groups: [],
  combinations: [],
  templates: []
});
const showSmartSuggestions = ref(false);
const isLoadingSuggestions = ref(false);

// Computed properties
const selectedGroups = computed(() => {
  return props.groups.filter(group => form.group_ids.includes(group.id));
});

const totalRecipients = computed(() => {
  // For emergency broadcasts to all contacts, count all contacts
  if (form.is_emergency && form.broadcast_type === 'all') {
    return props.groups.reduce((total, group) => total + group.contacts_count, 0);
  }
  // For group-based broadcasts, count only selected groups
  return selectedGroups.value.reduce((total, group) => total + group.contacts_count, 0);
});

function selectTemplate(template) {
  form.content = template.content;
  
  // Auto-set emergency mode if it's an emergency template
  if (template.category === 'emergency') {
    form.is_emergency = true;
    form.broadcast_type = 'all';
    form.emergency_category = template.value || 'other';
    form.priority = 'high';
  } else {
    form.is_emergency = false;
    form.broadcast_type = 'group';
    form.emergency_category = '';
    form.priority = 'normal';
  }
}

function formatScheduledTime() {
  if (!form.schedule_date || !form.schedule_time) return 'Please select date and time';
  
  const date = new Date(`${form.schedule_date}T${form.schedule_time}`);
  return date.toLocaleString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}

function closeSummary() {
  showSummary.value = false;
  // Reset form when closing
  form.reset();
  form.is_emergency = false;
  form.broadcast_type = 'group';
  form.emergency_category = '';
  form.requires_approval = false;
}

function calculateEstimatedCost() {
  if (totalRecipients.value === 0) return 0;
  
  // Twilio US rate: $0.0079 per SMS segment (160 characters)
  const costPerSegment = 0.0079;
  const messageLength = form.content.length;
  const segments = Math.ceil(messageLength / 160);
  const costPerMessage = costPerSegment * segments;
  
  return costPerMessage * totalRecipients.value;
}

// Get smart suggestions based on content
async function getSmartSuggestions() {
  if (!form.content.trim()) return;
  
  isLoadingSuggestions.value = true;
  // Simulate API delay
  setTimeout(() => {
      const content = form.content.toLowerCase();
      let mockSuggestions = { groups: [], combinations: [], templates: [] };
      
      // Generate suggestions based on content keywords
      if (content.includes('weather') || content.includes('météo') || content.includes('tempête')) {
        mockSuggestions.groups.push(
          {
            group: { id: 14, name: 'Farmers Association', contacts_count: 25 },
            score: 4,
            reason: 'Matches: weather, agriculture, crops'
          },
          {
            group: { id: 21, name: 'Transport Workers', contacts_count: 15 },
            score: 3,
            reason: 'Matches: weather, travel, road conditions'
          },
          {
            group: { id: 16, name: 'General Residents', contacts_count: 100 },
            score: 2,
            reason: 'Matches: weather affects general population'
          }
        );
        
        mockSuggestions.combinations.push({
          name: 'Weather-Affected Groups',
          groups: [14, 21, 16],
          reason: 'Weather alerts affect farming, transport, and general population',
          score: 5
        });
        
        mockSuggestions.templates.push({
          template: {
            id: 1,
            name: 'Weather Alert - Sandstorm',
            content: '⚠️ ALERTE MÉTÉO: Tempête de sable prévue dans votre région. Restez à l\'intérieur et fermez toutes les ouvertures. Évitez de conduire.'
          },
          score: 4,
          matched_keywords: ['weather', 'météo', 'tempête']
        });
      }
      
      if (content.includes('health') || content.includes('santé') || content.includes('medical')) {
        mockSuggestions.groups.push(
          {
            group: { id: 19, name: 'Healthcare Workers', contacts_count: 20 },
            score: 4,
            reason: 'Matches: health, medical, healthcare'
          },
          {
            group: { id: 18, name: 'Elderly Care', contacts_count: 30 },
            score: 3,
            reason: 'Matches: health, elderly, vulnerable'
          }
        );
        
        mockSuggestions.templates.push({
          template: {
            id: 3,
            name: 'Health Update',
            content: '🏥 MISE À JOUR SANTÉ: Campagne de vaccination disponible au centre de santé local. Rendez-vous sur place ou contactez-nous.'
          },
          score: 4,
          matched_keywords: ['health', 'santé', 'vaccination']
        });
      }
      
      if (content.includes('security') || content.includes('sécurité') || content.includes('police')) {
        mockSuggestions.groups.push(
          {
            group: { id: 20, name: 'Security Personnel', contacts_count: 12 },
            score: 4,
            reason: 'Matches: security, police, safety'
          },
          {
            group: { id: 13, name: 'Local Officials', contacts_count: 8 },
            score: 3,
            reason: 'Matches: security, officials, authority'
          }
        );
        
        mockSuggestions.combinations.push({
          name: 'Security Network',
          groups: [20, 13, 16],
          reason: 'Security incidents require coordinated response',
          score: 4
        });
      }
      
      // Agriculture targeting including typo-tolerant matches (e.g., "agricultre")
      if (
        content.includes('agricultre') ||
        content.includes('agriculture') ||
        content.includes('farm') ||
        content.includes('ferme') ||
        content.includes('récolte') ||
        content.includes('recolte') ||
        content.includes('crops')
      ) {
        mockSuggestions.groups.push(
          {
            group: { id: 14, name: 'Farmers Association', contacts_count: 25 },
            score: 5,
            reason: 'Matches: agriculture/farming keywords'
          }
        );
      }

      // Water / utility issues (EN/FR)
      if (content.includes('water') || content.includes('eau')) {
        mockSuggestions.groups.push(
          {
            group: { id: 15, name: 'Business Owners', contacts_count: 18 },
            score: 3,
            reason: 'Water/utility issues affect businesses'
          },
          {
            group: { id: 16, name: 'General Residents', contacts_count: 100 },
            score: 4,
            reason: 'Water/utility issues affect residents'
          },
          {
            group: { id: 13, name: 'Local Officials', contacts_count: 8 },
            score: 3,
            reason: 'Officials should be informed of utility issues'
          }
        );
        mockSuggestions.combinations.push({
          name: 'Water Supply Issue',
          groups: [16, 13, 15],
          reason: 'Coordinate residents, officials, and businesses for water issue',
          score: 4
        });
      }

      // Threat/menace keywords → officials + security + residents
      if (content.includes('menace') || content.includes('threat') || content.includes('danger')) {
        mockSuggestions.groups.push(
          {
            group: { id: 13, name: 'Local Officials', contacts_count: 8 },
            score: 4,
            reason: 'Threats/alerts require official oversight'
          },
          {
            group: { id: 20, name: 'Security Personnel', contacts_count: 12 },
            score: 4,
            reason: 'Coordinate security response'
          },
          {
            group: { id: 16, name: 'General Residents', contacts_count: 100 },
            score: 3,
            reason: 'Inform the population as needed'
          }
        );
        mockSuggestions.combinations.push({
          name: 'Emergency Response Team',
          groups: [13, 20, 16],
          reason: 'Officials + security + residents for coordinated alerting',
          score: 5
        });
      }
      
      // If no specific matches, show general suggestions
      if (mockSuggestions.groups.length === 0) {
        mockSuggestions.groups.push(
          {
            group: { id: 16, name: 'General Residents', contacts_count: 100 },
            score: 1,
            reason: 'General relevance for community announcements'
          }
        );
      }
      
      smartSuggestions.value = mockSuggestions;
      showSmartSuggestions.value = true;
      isLoadingSuggestions.value = false;
    }, 800);
}

// Apply smart group suggestions
function applySmartGroups(groupIds) {
  form.group_ids = groupIds;
}

// Apply smart template
function applySmartTemplate(template) {
  form.content = template.content;
  getSmartSuggestions(); // Refresh suggestions
}

// Watch content changes for smart suggestions
watch(() => form.content, (newContent) => {
  if (newContent.length > 15) {
    // Debounce the API call with longer delay
    setTimeout(() => {
      getSmartSuggestions();
    }, 2000);
  }
});

function submit() {
  // Basic validation
  if (form.content.length === 0 || form.group_ids.length === 0) {
    return;
  }
  
  form.processing = true;
  showProgress.value = true;
  progressPercentage.value = 0;
  progressMessage.value = 'Preparing to send messages...';
  totalCount.value = totalRecipients.value;
  sentCount.value = 0;
  
  // Prepare the data to send
  const submitData = {
    content: form.content,
    is_emergency: form.is_emergency,
    priority: form.priority,
    schedule_date: form.schedule_date,
    schedule_time: form.schedule_time,
    group_ids: form.group_ids
  };
  
  // Only include emergency fields if it's an emergency
  if (form.is_emergency) {
    submitData.broadcast_type = form.broadcast_type;
    submitData.emergency_category = form.emergency_category;
    submitData.requires_approval = form.requires_approval;
  }
  
  console.log('Submitting SMS form:', submitData);
  
  // Use Inertia's router.post with custom handling
  router.post(route('messages.store'), submitData, {
    preserveState: false,
    preserveScroll: false,
    onBefore: () => {
      // Show progress immediately
      progressPercentage.value = 10;
      progressMessage.value = 'Sending messages...';
    },
    onSuccess: (page) => {
      showProgress.value = false;
      form.processing = false;
      progressPercentage.value = 100;
      progressMessage.value = 'Messages sent successfully!';
      
      // Check if we got a JSON response (success case)
      if (page.props.flash && page.props.flash.success) {
        // Handle success redirect case
        showSummary.value = true;
        summaryStats.value = {
          total: totalRecipients.value,
          sent: totalRecipients.value,
          failed: 0,
          pending: 0
        };
        summaryTitle.value = page.props.flash.success;
        summaryIcon.value = CheckCircleIcon;
        summaryIconClass.value = 'bg-green-100 dark:bg-green-900';
        summaryIconColor.value = 'text-green-600 dark:text-green-400';
      } else {
        // Handle JSON response case
        try {
          const data = JSON.parse(page.props.flash?.data || '{}');
          if (data.success) {
            summaryStats.value = data.stats;
            summaryTitle.value = data.message;
            
            if (data.priority === 'urgent' || data.priority === 'high') {
              summaryIcon.value = ExclamationTriangleIcon;
              summaryIconClass.value = 'bg-red-100 dark:bg-red-900';
              summaryIconColor.value = 'text-red-600 dark:text-red-400';
            } else {
              summaryIcon.value = CheckCircleIcon;
              summaryIconClass.value = 'bg-green-100 dark:bg-green-900';
              summaryIconColor.value = 'text-green-600 dark:text-green-400';
            }
            
            showSummary.value = true;
          }
        } catch (e) {
          // Fallback to simple success
          showSummary.value = true;
          summaryStats.value = {
            total: totalRecipients.value,
            sent: totalRecipients.value,
            failed: 0,
            pending: 0
          };
          summaryTitle.value = 'Messages sent successfully!';
        }
      }
      
      // Dispatch custom event for notifications
      window.dispatchEvent(new CustomEvent('sms-sent', {
        detail: { message: summaryTitle.value }
      }));
    },
    onError: (errors) => {
      showProgress.value = false;
      form.processing = false;
      console.error('SMS sending failed:', errors);
      alert('Failed to send SMS: ' + (Object.values(errors).flat().join(', ') || 'Unknown error'));
    }
    });
}
</script> 