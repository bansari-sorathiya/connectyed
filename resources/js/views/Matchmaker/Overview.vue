<template>
  <div class="p-6">
    <!-- Dashboard Heading -->
    <h1 class="text-3xl font-bold mb-6">Matchmaker Dashboard</h1>

    <!-- Grid Layout for Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      
      <!-- Total Clients -->
      <div class="bg-white shadow-sm rounded-lg p-6">
        <h2 class="text-xl font-bold mb-2">Total Clients</h2>
        <p class="text-4xl font-semibold">{{ totalClients }}</p>
      </div>

      <!-- Active Matches -->
      <div class="bg-white shadow-sm rounded-lg p-6">
        <h2 class="text-xl font-bold mb-2">Active Matches</h2>
        <p class="text-4xl font-semibold">{{ activeMatches }}</p>
      </div>

      <!-- Pending Match Requests -->
      <div class="bg-white shadow-sm rounded-lg p-6">
        <h2 class="text-xl font-bold mb-2">Pending Match Requests</h2>
        <p class="text-4xl font-semibold">{{ pendingRequests }}</p>
      </div>

      <!-- Upcoming Meetings -->
      <div class="bg-white shadow-sm rounded-lg p-6">
        <h2 class="text-xl font-bold mb-2">Upcoming Meetings</h2>
        <p>{{ upcomingMeeting.name }} - {{ upcomingMeeting.time }}</p>
      </div>

      <!-- Match Success Rate -->
      <div class="bg-white shadow-sm rounded-lg p-6">
        <h2 class="text-xl font-bold mb-2">Match Success Rate</h2>
        <p class="text-4xl font-semibold">{{ successRate }}%</p>
      </div>

      <!-- Recently Added Clients -->
      <div class="bg-white shadow-sm rounded-lg p-6">
        <h2 class="text-xl font-bold mb-2">Recently Added Clients</h2>
        <ul class="list-disc list-inside">
          <li v-for="client in recentClients" :key="client.id">{{ client.name }} - {{ client.joinedAt }}</li>
        </ul>
      </div>

    </div>

    <!-- Recent Activity -->
    <div class="bg-white shadow-sm rounded-lg p-6 mt-6">
      <h2 class="text-xl font-bold mb-4">Recent Activity</h2>
      <ul class="list-disc list-inside">
        <li v-for="activity in recentActivity" :key="activity.id">{{ activity.message }}</li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  name: "Overview",
  data() {
    return {
      totalClients: 24,
      activeMatches: 8,
      pendingRequests: 3,
      upcomingMeeting: {
        name: 'John Doe',
        time: '2 PM on September 22, 2024',
      },
      successRate: 60,
      recentClients: [
        { id: 1, name: 'Jane Smith', joinedAt: '2024-09-20' },
        { id: 2, name: 'John Doe', joinedAt: '2024-09-18' },
        { id: 3, name: 'Mary Johnson', joinedAt: '2024-09-17' },
      ],
      recentActivity: [
        { id: 1, message: 'Added Jane Smith to the system.' },
        { id: 2, message: 'Matched John Doe with Mary Johnson.' },
        { id: 3, message: 'Completed consultation with Michael Brown.' },
      ],
    };
  },
};
</script>
  