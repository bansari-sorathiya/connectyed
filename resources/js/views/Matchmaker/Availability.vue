<!-- resources/js/views/Matchmaker/Availability.vue -->
<template>
    <div>
      <!-- Availability Form -->
      <div class="max-w-7xl mx-auto p-6 bg-white rounded-sm shadow-xs">
        <h2 class="text-xl font-bold mb-4">Manage Your Availability</h2>

        <form @submit.prevent="submitAvailability">
          <!-- Availability Slots -->
          <div
            v-for="(availability, index) in selectedAvailabilities"
            :key="index"
            class="mb-4 p-4 border rounded-lg"
          >
            <div class="flex flex-wrap -mx-2">
              <!-- Start Date -->
              <div class="w-full sm:w-1/2 px-2 mb-2 sm:mb-0">
                <label class="block text-gray-700 font-bold mb-2">Start Date:</label>
                <input
                  type="date"
                  class="w-full border border-gray-300 rounded p-2"
                  v-model="availability.start_date"
                  required
                />
              </div>

              <!-- End Date -->
              <div class="w-full sm:w-1/2 px-2 mb-2 sm:mb-0">
                <label class="block text-gray-700 font-bold mb-2">End Date:</label>
                <input
                  type="date"
                  class="w-full border border-gray-300 rounded p-2"
                  v-model="availability.end_date"
                  required
                  :min="availability.start_date"
                />
              </div>
            </div>

            <!-- Time Slots -->
            <div class="flex flex-wrap -mx-2 mt-4">
              <!-- Start Time -->
              <div class="w-full sm:w-1/2 px-2 mb-2 sm:mb-0">
                <label class="block text-gray-700 font-bold mb-2">Start Time:</label>
                <input
                  type="time"
                  class="w-full border border-gray-300 rounded p-2"
                  v-model="availability.start_time"
                  :disabled="availability.is_all_day"
                  :required="!availability.is_all_day"
                />
              </div>

              <!-- End Time -->
              <div class="w-full sm:w-1/2 px-2 mb-2 sm:mb-0">
                <label class="block text-gray-700 font-bold mb-2">End Time:</label>
                <input
                  type="time"
                  class="w-full border border-gray-300 rounded p-2"
                  v-model="availability.end_time"
                  :disabled="availability.is_all_day"
                  :required="!availability.is_all_day"
                  :min="availability.start_time"
                />
              </div>
            </div>

            <!-- All Day Checkbox -->
            <div class="flex items-center mt-4">
              <input
                type="checkbox"
                id="is_all_day"
                v-model="availability.is_all_day"
                class="mr-2"
              />
              <label for="is_all_day" class="text-gray-700">All Day Availability</label>
            </div>

            <!-- Remove Availability Button -->
            <div class="mt-4">
              <button
                type="button"
                class="bg-red-500 text-white font-bold py-1 px-3 rounded hover:bg-red-600"
                @click="removeAvailabilitySlot(index)"
              >
                Remove Availability
              </button>
            </div>
          </div>

          <!-- Add New Availability Button -->
          <div class="mb-4">
            <button
              type="button"
              class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600"
              @click="addNewAvailability"
            >
              Add Another Availability
            </button>
          </div>

          <!-- Submit Button -->
          <div class="mt-6 text-right">
            <button
              type="submit"
              class="bg-green-500 text-white font-bold py-2 px-6 rounded hover:bg-green-600"
              :disabled="processing"
            >
              {{ processing ? 'Saving...' : 'Save Availability' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Availability List -->
      <div class="max-w-7xl mx-auto p-6 bg-white rounded-sm shadow-xs mt-8">
        <h2 class="text-xl font-bold mb-4">Your Availability</h2>

        <table class="min-w-full bg-white border">
          <thead>
            <tr class="bg-gray-100 border-b">
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-if="listAvailabilities.length"
              v-for="(availability, index) in listAvailabilities"
              :key="availability.id"
              class="border-b"
            >
              <td class="px-4 py-2">{{ formatDate(availability.start_date) }}</td>
              <td class="px-4 py-2">{{ formatDate(availability.end_date) }}</td>
              <td class="px-4 py-2">
                <span v-if="!availability.is_all_day && availability.start_time && availability.end_time">
                  {{ formatTime(availability.start_time) }} to {{ formatTime(availability.end_time) }}
                </span>
                <span v-else>
                  All Day
                </span>
              </td>
              <td class="px-4 py-2">
                <button
                  class="bg-red-500 text-white font-bold py-1 px-3 rounded hover:bg-red-600"
                  @click="removeAvailability(availability.id)"
                >
                  Remove
                </button>
              </td>
            </tr>
            <tr v-else>
              <td colspan="4" class="px-4 py-2 text-center text-gray-600">No availabilities found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </template>

  <script>
  import axios from 'axios';

  export default {
    name: 'Availability',
    data() {
      return {
        processing: false,
        selectedAvailabilities: [
          { start_date: '', end_date: '', start_time: '', end_time: '', is_all_day: false }
        ],
        listAvailabilities: [],
      };
    },
    computed: {
      user() {
        return this.$store.state.auth.user; // Correctly access the user object
      },
      authorization() {
        return this.$store.state.auth.authorization; // Correctly access the authorization token
      },
    },
    mounted() {
      this.fetchAvailabilities();
      console.log('User:', this.user); // Debugging: Verify user object
      console.log('Authorization:', this.authorization); // Debugging: Verify authorization token
    },
    methods: {
      /**
       * Add a new availability slot.
       */
      addNewAvailability() {
        this.selectedAvailabilities.push({
          start_date: '',
          end_date: '',
          start_time: '',
          end_time: '',
          is_all_day: false
        });
      },

      /**
       * Remove an availability slot by index.
       * @param {number} index - Index of the availability to remove.
       */
      removeAvailabilitySlot(index) {
        this.selectedAvailabilities.splice(index, 1);
      },

      /**
       * Remove an availability from the backend and refresh the list.
       * @param {number} id - ID of the availability to remove.
       */
      async removeAvailability(id) {
        if (!confirm('Are you sure you want to remove this availability?')) {
          return;
        }

        try {
          await axios.delete(`/api/availabilities/${id}`, {
            headers: {
              Authorization: `Bearer ${this.authorization.token}`,
            },
          });
          alert('Availability removed successfully.');
          this.fetchAvailabilities();
        } catch (error) {
          console.error('Error removing availability:', error);
          if (error.response && error.response.status === 403) {
            alert('You are not authorized to remove this availability.');
          } else if (error.response && error.response.status === 404) {
            alert('Availability not found.');
          } else {
            alert('Failed to remove availability. Please try again.');
          }
        }
      },

      /**
       * Submit all availability slots to the backend.
       */
      async submitAvailability() {
        this.processing = true;
        let availabilities = [];

        // Prepare the availabilities array
        this.selectedAvailabilities.forEach(slot => {
          if (slot.start_date && slot.end_date) {
            availabilities.push({
              start_date: slot.start_date,
              end_date: slot.end_date,
              is_all_day: slot.is_all_day,
              start_time: slot.is_all_day ? null : slot.start_time,
              end_time: slot.is_all_day ? null : slot.end_time,
            });
          }
        });

        // Debugging: Log the payload
        console.log('Submitting availabilities:', availabilities);

        if (availabilities.length === 0) {
          alert('Please add at least one availability slot with valid dates.');
          this.processing = false;
          return;
        }

        try {
          // POST request to the backend without 'user_id'
          const response = await axios.post('/api/availabilities', {
            availabilities,
          }, {
            headers: {
              Authorization: `Bearer ${this.authorization.token}`,
            },
          });

          // Debugging: Log the response
          console.log('Response from server:', response);

          if (response.data.success) {
            alert('Availability saved successfully!');
            // Reset the form
            this.selectedAvailabilities = [
              { start_date: '', end_date: '', start_time: '', end_time: '', is_all_day: false }
            ];
            // Refresh the availability list
            this.fetchAvailabilities();
          } else {
            console.error('Failed to save availability:', response.data.message);
            alert(response.data.message || 'Failed to save availability.');
          }
        } catch (error) {
          console.error('Error submitting availability:', error);
          if (error.response) {
            console.error('Response data:', error.response.data);
            if (error.response.status === 422) {
              const errors = Object.values(error.response.data.errors).flat();
              alert('Validation Error:\n' + errors.join('\n'));
            } else if (error.response.status === 403) {
              alert('You are not authorized to perform this action.');
            } else {
              alert('Failed to save availability. Please try again.');
            }
          } else if (error.request) {
            console.error('No response received:', error.request);
            alert('No response from server. Please check your network connection.');
          } else {
            console.error('Error setting up request:', error.message);
            alert('An unexpected error occurred.');
          }
        } finally {
          this.processing = false;
          // Debugging: Indicate that processing has finished
          console.log('Processing completed.');
        }
      },

      /**
       * Format date string to a readable format.
       * @param {string} date - Date string in YYYY-MM-DD format.
       * @returns {string} - Formatted date.
       */
      formatDate(date) {
        return new Date(date).toLocaleDateString();
      },

      /**
       * Format time string to a readable format.
       * @param {string} time - Time string in HH:MM:SS format.
       * @returns {string} - Formatted time.
       */
      formatTime(time) {
        if (!time) return 'All Day';
        const [hour, minute, second] = time.split(':');
        const date = new Date();
        date.setHours(parseInt(hour), parseInt(minute), parseInt(second));
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      },

      /**
       * Fetch all availabilities for the authenticated matchmaker.
       */
      async fetchAvailabilities() {
        try {
          const response = await axios.get(`/api/availabilities/${this.user.id}`, {
            headers: {
              Authorization: `Bearer ${this.authorization.token}`,
            },
          });
          if (response.data.success) {
            this.listAvailabilities = response.data.data;
            console.log('Availabilities:', this.listAvailabilities);
          } else {
            console.error('Failed to fetch availabilities:', response.data.message);
            alert('Failed to fetch availabilities. Please try again later.');
          }
        } catch (error) {
          console.error('Error fetching availabilities:', error);
          if (error.response && error.response.status === 403) {
            alert('You are not authorized to view availabilities.');
          } else {
            alert('An error occurred while fetching availabilities.');
          }
        }
      },
    },
  };
  </script>

  <style scoped>
  /* Container Styling */
  .max-w-7xl {
    max-width: 80rem;
  }

  .mx-auto {
    margin-left: auto;
    margin-right: auto;
  }

  .p-6 {
    padding: 1.5rem;
  }

  .bg-white {
    background-color: #ffffff;
  }

  .rounded-sm {
    border-radius: 0.125rem;
  }

  .shadow-xs {
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  }

  .text-xl {
    font-size: 1.25rem;
  }

  .font-bold {
    font-weight: 700;
  }

  .mb-4 {
    margin-bottom: 1rem;
  }

  .mb-2 {
    margin-bottom: 0.5rem;
  }

  .p-4 {
    padding: 1rem;
  }

  .border {
    border: 1px solid #e2e8f0;
  }

  .rounded-lg {
    border-radius: 0.5rem;
  }

  .flex {
    display: flex;
  }

  .flex-wrap {
    flex-wrap: wrap;
  }

  .items-center {
    align-items: center;
  }

  .space-x-2 > * + * {
    margin-left: 0.5rem;
  }

  .space-x-4 > * + * {
    margin-left: 1rem;
  }

  .mt-4 {
    margin-top: 1rem;
  }

  .mt-6 {
    margin-top: 1.5rem;
  }

  .mt-8 {
    margin-top: 2rem;
  }

  .bg-blue-500 {
    background-color: #4299e1;
  }

  .bg-blue-600 {
    background-color: #3182ce;
  }

  .bg-red-500 {
    background-color: #f56565;
  }

  .bg-red-600 {
    background-color: #c53030;
  }

  .bg-green-500 {
    background-color: #48bb78;
  }

  .bg-green-600 {
    background-color: #38a169;
  }

  .text-white {
    color: #ffffff;
  }

  .text-gray-700 {
    color: #4a5568;
  }

  .text-gray-500 {
    color: #a0aec0;
  }

  .text-gray-600 {
    color: #4a5568;
  }

  .hover\:bg-blue-600:hover {
    background-color: #3182ce;
  }

  .hover\:bg-blue-500:hover {
    background-color: #4299e1;
  }

  .hover\:bg-red-600:hover {
    background-color: #c53030;
  }

  .hover\:bg-red-500:hover {
    background-color: #f56565;
  }

  .hover\:bg-green-600:hover {
    background-color: #38a169;
  }

  .hover\:bg-green-500:hover {
    background-color: #48bb78;
  }

  .hover\:text-red-700:hover {
    color: #c53030;
  }

  .hover\:text-gray-700:hover {
    color: #2d3748;
  }

  .float-right {
    float: right;
  }

  .text-center {
    text-align: center;
  }

  .font-medium {
    font-weight: 500;
  }

  .uppercase {
    text-transform: uppercase;
  }

  .tracking-wider {
    letter-spacing: 0.05em;
  }

  .underline {
    text-decoration: underline;
  }

  .cursor-pointer {
    cursor: pointer;
  }

  .disabled\:opacity-50:disabled {
    opacity: 0.5;
  }

  /* Dropdown Styles */
  select {
    appearance: none;
    background-color: #fff;
    border: 1px solid #cbd5e0;
    padding: 0.5rem;
    border-radius: 0.375rem;
    width: 100%;
  }

  option {
    padding: 0.5rem;
  }
  </style>
