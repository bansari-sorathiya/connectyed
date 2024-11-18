<template>
  <div class="max-w-6xl mx-auto p-6 bg-white rounded-sm shadow-sm">
    <h2 class="text-2xl font-semibold mb-6">Match Management</h2>

    <div class="mb-6">
      <h2 class="text-2xl font-semibold mb-4">Search Clients</h2>

      <form @submit.prevent="searchClients">
        <div class="flex items-center mb-4">
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Enter client name or email..."
            class="w-full py-2 px-4 text-gray-700 border rounded-lg shadow-sm focus:outline-none focus:ring"
          />
          <button
            type="submit"
            class="ml-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
          >
            Search
          </button>
        </div>
      </form>
    </div>

    <!-- Client Search Results -->
    <div v-if="clients.length > 0">
      <h3 class="text-xl font-semibold mb-4">Client Results</h3>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div
          v-for="client in clients"
          :key="client.id"
          class="p-4 bg-gray-100 rounded-lg shadow-md flex items-start"
        >
          <!-- Client Image -->
          <img
            :src="client.image"
            alt="Client Profile Image"
            class="w-24 h-24 rounded-full object-cover mr-4"
          />

          <!-- Client Summary Profile -->
          <div>
            <h4 class="text-lg font-bold">{{ client.name }}</h4>
            <p class="text-gray-600">{{ client.email }}</p>
            <p class="text-gray-500 text-sm">Age: {{ client.age }}</p>
            <p class="text-gray-500 text-sm">Location: {{ client.location }}</p>

            <!-- Send Request Button -->
            <button
              @click="sendSessionRequest(client.id)"
              class="mt-3 bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded focus:outline-none focus:shadow-outline"
            >
              Send Session Request
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- No Results Message -->
    <div v-else-if="searchPerformed" class="mt-4">
      <p class="text-gray-600">No clients found for "{{ searchQuery }}".</p>
    </div>

    <!-- Client List -->
    <div class="mb-6">
      <h3 class="text-xl font-semibold mb-4">Clients</h3>
      
      <div v-if="clients.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div 
          v-for="client in clients" 
          :key="client.id" 
          class="p-4 bg-gray-100 rounded-lg shadow-md flex items-center justify-between"
        >
          <div>
            <h4 class="text-lg font-bold">{{ client.name }}</h4>
            <p class="text-gray-600">{{ client.email }}</p>
          </div>

          <button
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
            @click="sendSessionRequest(client.id)"
          >
            Send Session Request
          </button>
        </div>
      </div>

      <!-- No Clients Available -->
      <div v-else>
        <p class="text-gray-600">No clients found.</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      clients: [        
        { id: 1, name: 'John Doe', email: 'john@example.com' },
        { id: 2, name: 'Jane Smith', email: 'jane@example.com' },
      ],
      searchQuery: '', // Search input
      clients: [], // To store the search results
      searchPerformed: false // Flag to indicate if the search was performed
    };
  },
  methods: {
    searchClients() {
      // Example: You would typically make an API request here
      this.searchPerformed = true;
      
      // Simulated search results (replace this with actual API call)
      const allClients = [
        {
          id: 1,
          name: 'John Doe',
          email: 'john@example.com',
          age: 30,
          location: 'New York, USA',
          image: 'https://randomuser.me/api/portraits/men/1.jpg'
        },
        {
          id: 2,
          name: 'Jane Smith',
          email: 'jane@example.com',
          age: 28,
          location: 'Los Angeles, USA',
          image: 'https://randomuser.me/api/portraits/women/2.jpg'
        }
      ];

      // Filter the clients based on the search query
      this.clients = allClients.filter(client =>
        client.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
        client.email.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    },
    sendSessionRequest(clientId) {
      // Handle session request sending (make API request here)
      console.log('Session request sent to client ID:', clientId);
    }
  }
};
</script>

<style scoped>
/* Optional custom styles */
</style>