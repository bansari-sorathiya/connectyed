<template>
  <div class="p-8 bg-gray-100 min-h-screen">
    <h1 class="text-2xl font-bold mb-6 text-gray-700">List of registered clients</h1>

    <div v-if="clients.length === 0" class="text-gray-500">
      No clients Registered
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="client in clients" :key="client.id" class="p-6 bg-white shadow-md rounded-lg">
        <h2 class="text-lg font-semibold text-[#213366]">{{ client.name }}</h2>
        <p class="text-gray-500">{{ client.email }}</p>
        <div class="mt-4">
          <button v-if="!client.email_verified_at"
            @click="approveClient(client.id)"
            class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600"
          >
            Verify
          </button>
          <span v-else class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Verified</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      authorization:this.$store.state.auth.authorization,
      clients: [],
    };
  },
  mounted() {
    this.fetchClients();
  },
  methods: {
    async fetchClients() {
        this.processing = true
        axios.defaults.headers.common.Authorization = `Bearer ${this.authorization.token}` 
        await axios.get('/api/admin/clients')
          .then((response) => {                                              
            this.clients = response.data.data;
        }).catch((error)=>{
            console.error(error);
        }).finally(()=>{
            this.processing = false
        })
    }    
  },
};
</script>

