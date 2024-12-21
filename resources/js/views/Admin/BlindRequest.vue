<template>
    <div class="p-8 bg-gray-100 min-h-screen">
        <h1 class="text-2xl font-bold mb-6 text-gray-700">Request For 1 on 1 Blind Date</h1>

        <div v-if="clients.length === 0" class="text-gray-500">
            No clients Requested
        </div>

        <!-- CLIENT SELECTION -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">Select Clients</h3>
            <div class="relative">
                <input v-model="searchQuery" @focus="showDropdown = true" @blur="hideDropdown"
                    @input="showDropdown = true" type="text" placeholder="Search for clients..."
                    class="w-full px-3 py-2 border rounded" ref="clientInput" />
                <ul v-if="showDropdown"
                    class="absolute z-10 w-full bg-white border rounded shadow mt-1 max-h-60 overflow-y-auto">
                    <li v-for="client in filteredClients" :key="client.id" @click="selectClient(client)"
                        class="px-3 py-2 hover:bg-gray-100 cursor-pointer">
                        {{ client.name }}
                    </li>
                    <li v-if="filteredClients.length === 0" class="px-3 py-2 text-gray-500">
                        No clients found.
                    </li>
                </ul>
            </div>
            <!-- Selected Clients -->
            <div class="mt-2">
                <span v-for="client in selectedClients" :key="client.id"
                    class="inline-flex items-center bg-blue-100 text-blue-800 text-sm px-2 py-1 rounded mr-2">
                    {{ client.name }}
                    <button @click="removeClient(client.id)" class="ml-1 text-red-500">✖️</button>
                </span>
            </div>
        </div>

        <!-- Meeting Scheduling Form -->
        <div class="mb-6">
          <h3 class="text-lg font-semibold mb-2">Select Date and Time</h3>
          <input
            v-model="startTime"
            type="datetime-local"
            class="w-full px-3 py-2 border rounded"
          />
        </div>

        <!-- Duration Selection -->
        <div class="mb-6">
          <h3 class="text-lg font-semibold mb-2">Select Duration</h3>
          <select v-model="duration" class="w-full px-3 py-2 border rounded">
            <option disabled value="">Please select duration</option>
            <option value="15">15 Minutes</option>
            <option value="30">30 Minutes</option>
            <option value="60">60 Minutes</option>
          </select>
        </div>

        <!-- Schedule Meeting Button -->
        <button
          @click="scheduleMeeting"
          :disabled="processing || selectedClients.length < 2"
          class="w-full py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50 transition duration-200"
        >
          {{ processing ? 'Scheduling...' : 'Schedule Meeting' }}
        </button>
    </div>

</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            authorization: this.$store.state.auth.authorization,
            clients: [],
            searchQuery: '',
            showDropdown: false,
            selectedClients: [],
            processing: false, 
            duration: 30, // Default duration
            startTime: '', // Selected start time
        };
    },
    computed: {
        filteredClients() {
            const query = this.searchQuery.toLowerCase();
            return this.clients.filter(
                (client) =>
                    client.name &&
                    client.name.toLowerCase().includes(query) &&
                    !this.selectedClients.find((c) => c.id === client.id)
            );
        },
    },
    mounted() {
        this.fetchBlindRequests();
    },
    methods: {
        async fetchBlindRequests() {
            this.processing = true
            axios.defaults.headers.common.Authorization = `Bearer ${this.authorization.token}`
            await axios.get('/api/get-blind-request')
                .then((response) => {
                    console.log(response.data.data)
                    this.clients = response.data.data.flat();

                }).catch((error) => {
                    console.error(error);
                }).finally(() => {
                    this.processing = false
                })
        },
        selectClient(client) {
            if (this.selectedClients.length < 2) {
                this.selectedClients.push(client);
                this.searchQuery = '';
                this.showDropdown = false;
                this.$refs.clientInput.blur();
            } else {
                alert('You can select up to 2 clients only.');
            }
        },
        removeClient(clientId) {
            this.selectedClients = this.selectedClients.filter(
                (client) => client.id !== clientId
            );
        },

        /**
         * Schedule a meeting by sending a request to the backend.
        */
        async scheduleMeeting() {
            if (this.selectedClients.length === 0) {
                alert('Please select at least one client.');
                return;
            }
            if (!this.startTime) {
                alert('Please select a meeting date and time.');
                return;
            }

            if (!this.duration) {
                alert('Please select a meeting duration.');
                return;
            }
            // Validate that the selected time is in the future
            const selectedDateTime = new Date(this.startTime);
            if (selectedDateTime < new Date()) {
                alert('Please select a future date and time for the meeting.');
                return;
            }

            // Validate that duration is one of the allowed values
            if (![15, 30, 60].includes(parseInt(this.duration))) {
                alert('Invalid duration selected.');
                return;
            }
            this.processing = true;

            try {
                const response = await axios.post(
                    '/api/google/create-free-blind-meeting',
                    {
                        client_ids: this.selectedClients.map((client) => client.id),
                        start_time: this.startTime,
                        duration: parseInt(this.duration), // Ensure it's an integer
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${this.authorization.token}`,
                        },
                    }
                );

                if (response.status) {
                    alert(response.data.message);
                    // Optionally, you can redirect or perform additional actions here.

                    // Reset form
                    this.selectedClients = [];
                    this.startTime = '';
                    this.duration = 30; // Reset to default
                } else {
                    alert(response.data.message || 'Failed to schedule meeting.');
                }
            } catch (error) {
                console.error(error);
                alert('Failed to schedule meeting. Please try again.');
            } finally {
                this.processing = false;
            }
        },

    },
};
</script>