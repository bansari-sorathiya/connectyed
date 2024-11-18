<template>
    <div>
      <h2 class="text-xl font-bold mb-4">Billing</h2>

      <!-- Existing dashboard content can be placed here if needed -->

      <h2 class="text-xl font-semibold mt-6">Earnings</h2>
      <p>Total Earnings: ${{ totalEarnings.toFixed(2) }}</p>
      <button
        @click="requestWithdrawal"
        class="mt-4 py-2 px-4 bg-green-500 text-white rounded"
      >
        Request Withdrawal
      </button>
    </div>
  </template>

  <script>
  import axios from 'axios';
  import { mapState } from 'vuex';

  export default {
    name: "Billing",
    data() {
      return {
        totalEarnings: 0,
      };
    },
    computed: {
      ...mapState({
        authToken: state => state.auth.authorization.token,
      }),
    },
    mounted() {
      this.fetchEarnings();
    },
    methods: {
      async fetchEarnings() {
        try {
          const response = await axios.get('/api/matchmaker/earnings', {
            headers: {
              Authorization: `Bearer ${this.authToken}`,
            },
          });
          this.totalEarnings = response.data.totalEarnings;
        } catch (error) {
          console.error('Error fetching earnings:', error);
          // Optionally, handle the error in the UI
        }
      },
      async requestWithdrawal() {
        try {
          const response = await axios.post('/api/matchmaker/request-withdrawal', {}, {
            headers: {
              Authorization: `Bearer ${this.authToken}`,
            },
          });
          alert('Withdrawal request sent successfully.');
          // Optionally, refresh earnings or update the UI accordingly
        } catch (error) {
          console.error('Error requesting withdrawal:', error);
          alert('An error occurred while requesting withdrawal.');
        }
      },
    },
  };
  </script>

  <style scoped>
  /* Add any component-specific styles here */
  </style>
