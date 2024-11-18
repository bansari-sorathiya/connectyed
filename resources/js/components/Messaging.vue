<!-- resources/js/components/Messaging.vue -->
<template>
    <div class="messaging-container">
      <h3 class="text-lg font-semibold mb-4">Messages</h3>

      <!-- Messages List -->
      <div class="messages-list mb-4">
        <div v-if="loading" class="text-center">Loading messages...</div>
        <div v-else>
          <div v-if="messages.length === 0" class="text-center text-gray-500">
            No messages found.
          </div>
          <ul v-else>
            <li
              v-for="message in messages"
              :key="message.id"
              class="mb-2 p-4 border rounded bg-gray-50"
            >
              <p><strong>{{ getOtherUser(message).name }}:</strong> {{ message.message }}</p>
              <p class="text-xs text-gray-500">{{ formatDate(message.created_at) }}</p>
            </li>
          </ul>
        </div>
      </div>

      <!-- Send Message Form -->
      <div class="send-message-form">
        <h4 class="text-md font-semibold mb-2">Send a Message</h4>
        <form @submit.prevent="sendMessage">
          <div class="mb-4">
            <label for="receiver" class="block text-sm font-medium text-gray-700">To:</label>
            <select
              v-model="newMessage.receiver_id"
              required
              class="w-full px-3 py-2 border rounded"
            >
              <option disabled value="">Select a user</option>
              <option
                v-for="user in users"
                :key="user.id"
                :value="user.id"
              >
                {{ user.name }}
              </option>
            </select>
          </div>
          <div class="mb-4">
            <label for="message" class="block text-sm font-medium text-gray-700">Message:</label>
            <textarea
              v-model="newMessage.message"
              required
              rows="3"
              class="w-full px-3 py-2 border rounded"
              placeholder="Type your message here..."
            ></textarea>
          </div>
          <button
            type="submit"
            :disabled="sending"
            class="w-full py-2 bg-green-500 text-white rounded-lg disabled:opacity-50"
          >
            {{ sending ? 'Sending...' : 'Send Message' }}
          </button>
        </form>
      </div>
    </div>
  </template>

  <script>
  import axios from 'axios';

  export default {
    name: 'Messaging',
    props: {
      currentUser: {
        type: Object,
        required: true,
      },
      authorization: {
        type: Object,
        required: true,
      },
    },
    data() {
      return {
        messages: [],
        users: [],
        loading: false,
        sending: false,
        newMessage: {
          receiver_id: null,
          message: '',
        },
      };
    },
    computed: {
      user() {
        return this.currentUser;
      },
    },
    mounted() {
      this.fetchMessages();
      this.fetchUsers();
    },
    methods: {
      /**
       * Fetch messages for the authenticated user.
       */
      async fetchMessages() {
        this.loading = true;
        try {
          const response = await axios.get('/api/messages', {
            headers: {
              Authorization: `Bearer ${this.authorization.token}`,
            },
          });
          if (response.data.success) {
            this.messages = response.data.data;
          } else {
            alert(response.data.message || 'Failed to fetch messages.');
          }
        } catch (error) {
          console.error('Error fetching messages:', error.response || error);
          if (error.response && error.response.status === 403) {
            alert('You are not authorized to view messages.');
          } else {
            alert('Failed to fetch messages. Please try again.');
          }
        } finally {
          this.loading = false;
        }
      },

      /**
       * Fetch users to whom messages can be sent.
       * If the current user is a matchmaker, fetch clients.
       * If the current user is a client, fetch matchmakers.
       */
      async fetchUsers() {
        try {
          if (!this.user) {
            console.error('User is not defined');
            return;
          }

    if (this.user.role === 'matchmaker') {
      // Fetch ALL clients with fetch_all=true parameter
      const response = await axios.get('/api/clients/all?fetch_all=true', {
        headers: {
          Authorization: `Bearer ${this.authorization.token}`,
        },
      });
            if (response.data.success) {
              this.users = response.data.data;
            } else {
              alert(response.data.message || 'Failed to fetch clients.');
            }
          } else if (this.user.role === 'client') {
            // Fetch matchmakers
            const response = await axios.get('/api/matchmakers/', {
              headers: {
                Authorization: `Bearer ${this.authorization.token}`,
              },
            });
            if (response.data.success) {
              this.users = response.data.data;
            } else {
              alert(response.data.message || 'Failed to fetch matchmakers.');
            }
          } else {
            // Handle other roles if necessary
            alert('Your role does not have messaging capabilities.');
          }
        } catch (error) {
          console.error('Error fetching users for messaging:', error.response || error);
          if (error.response && error.response.status === 403) {
            alert('You are not authorized to view users.');
          } else {
            alert('Failed to fetch users for messaging.');
          }
        }
      },

      /**
       * Send a new message.
       */
      async sendMessage() {
        if (!this.newMessage.receiver_id || !this.newMessage.message.trim()) {
          alert('Please select a user and type a message.');
          return;
        }

        this.sending = true;
        try {
          const response = await axios.post(
            '/api/messages/send',
            this.newMessage,
            {
              headers: {
                Authorization: `Bearer ${this.authorization.token}`,
              },
            }
          );

          if (response.data.success) {
            alert('Message sent successfully.');
            this.newMessage.message = '';
            this.fetchMessages(); // Refresh messages
          } else {
            alert(response.data.message || 'Failed to send message.');
          }
        } catch (error) {
          console.error('Error sending message:', error.response || error);
          if (error.response && error.response.status === 403) {
            alert('You are not authorized to send messages.');
          } else {
            alert('Failed to send message. Please try again.');
          }
        } finally {
          this.sending = false;
        }
      },

      /**
       * Get the other user in the conversation.
       * If the current user is the sender, return the receiver, and vice versa.
       */
      getOtherUser(message) {
        return message.sender_id === this.user.id ? message.receiver : message.sender;
      },

      /**
       * Format date and time for display.
       */
      formatDate(dateTime) {
        const options = {
          year: 'numeric',
          month: 'long',
          day: 'numeric',
          hour: '2-digit',
          minute: '2-digit',
        };
        return new Date(dateTime).toLocaleDateString(undefined, options);
      },
    },
  };
  </script>

  <style scoped>
  .messaging-container {
    max-width: 600px;
    margin: 0 auto;
  }

  .messages-list {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
    border-radius: 0.375rem;
    padding: 1rem;
    background-color: #f9fafb;
  }

  .message-item {
    padding: 10px;
    border-bottom: 1px solid #ccc;
  }

  .send-message-form {
    border: 1px solid #e5e7eb;
    border-radius: 0.375rem;
    padding: 1rem;
    background-color: #ffffff;
  }

  .send-message-form textarea {
    resize: vertical;
  }
  </style>
