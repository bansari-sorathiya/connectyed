<template>
  <div class="max-w-7xl mx-auto p-6 bg-white rounded-sm shadow-xs">
    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="text-red-500 p-4 text-center">
      {{ error }}
      <div v-if="debugInfo" class="mt-2 text-sm">
        <p>Debug Info:</p>
        <pre class="bg-gray-100 p-2 mt-1 rounded overflow-auto">{{ debugInfo }}</pre>
      </div>
    </div>

    <!-- Content when authenticated -->
    <div v-else>
      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Matchmaker's Clients</h2>
        <!-- Add Client Button -->
        <button
          @click="toggleAddClientForm"
          class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
        >
          {{ showAddClientForm ? 'Cancel' : 'Add Client' }}
        </button>
      </div>

      <!-- Success Message -->
      <div v-if="successMessage" class="mb-4 text-green-500">
        {{ successMessage }}
      </div>

      <!-- Add/Edit Client Form -->
      <div v-if="showAddClientForm" class="mb-10">
        <h3 class="text-xl font-semibold mb-4">
          {{ newClient.isUpdating ? 'Edit Client' : 'Add New Client' }}
        </h3>

        <form @submit.prevent="newClient.isUpdating ? updateClient() : addClient()" enctype="multipart/form-data">
          <!-- Display Validation Errors -->
          <div v-if="Object.keys(validationErrors).length > 0" class="mb-4">
            <div class="alert alert-danger">
              <ul class="mb-0">
                <li
                  class="text-red-500 text-xs italic"
                  v-for="(errors, field) in validationErrors"
                  :key="field"
                >
                  {{ errors[0] }}
                </li>
              </ul>
            </div>
          </div>

          <!-- Thumbnail Image -->
          <div class="mb-4">
            <label class="block text-gray-700">
              Upload Thumbnail Image <span class="text-red-500">*</span>
            </label>
            <input
              type="file"
              @change="onFileChange($event, 'avatar')"
              accept="image/*"
              :required="!newClient.isUpdating"
              class="mt-2 block w-full text-sm text-gray-500
                     file:mr-4 file:py-2 file:px-4
                     file:rounded-md file:border-0
                     file:text-sm file:font-semibold
                     file:bg-blue-50 file:text-blue-700
                     hover:file:bg-blue-100"
            />
            <p v-if="validationErrors.avatar" class="text-red-500 text-xs italic">
              {{ validationErrors.avatar }}
            </p>
            <div v-if="preview" class="mt-2">
              <img
                :src="preview"
                alt="Image Preview"
                class="w-48 h-48 object-cover rounded-md shadow-lg"
              />
            </div>
          </div>

          <!-- Additional Images -->
          <div class="mb-4">
            <label class="block text-gray-700">Upload Additional Images</label>
            <input
              type="file"
              @change="onMultipleFileChange($event)"
              accept="image/*"
              multiple
              class="mt-2 block w-full text-sm text-gray-500
                     file:mr-4 file:py-2 file:px-4
                     file:rounded-md file:border-0
                     file:text-sm file:font-semibold
                     file:bg-blue-50 file:text-blue-700
                     hover:file:bg-blue-100"
            />
            <p v-if="validationErrors.additional_images" class="text-red-500 text-xs italic">
              {{ validationErrors.additional_images }}
            </p>
          </div>

          <!-- Personal Information -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input-text
              label="Name"
              v-model="newClient.name"
              :required="true"
              :error="!!validationErrors.name"
              :errorMessage="validationErrors.name"
            />
            <input-text
              label="Email"
              v-model="newClient.email"
              :required="true"
              :error="!!validationErrors.email"
              :errorMessage="validationErrors.email"
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input-text
              label="Age"
              v-model="newClient.age"
              type="number"
              :required="true"
              :error="!!validationErrors.age"
              :errorMessage="validationErrors.age"
              min="18"
              max="100"
            />
            <input-text
              label="Hair Color"
              v-model="newClient.hairColor"
              :required="true"
              :error="!!validationErrors.hairColor"
              :errorMessage="validationErrors.hairColor"
              maxlength="50"
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <select-option
              label="Body Type"
              :options="bodyTypes"
              v-model="newClient.bodytype"
              :required="true"
              :error="!!validationErrors.bodytype"
              :errorMessage="validationErrors.bodytype"
            />
            <div class="flex gap-4">
              <input-text
                label="Height (Feet)"
                v-model="newClient.heightFeet"
                type="number"
                :required="true"
                :error="!!validationErrors.heightFeet"
                :errorMessage="validationErrors.heightFeet"
                min="1"
                max="8"
              />
              <input-text
                label="Inches"
                v-model="newClient.heightInches"
                type="number"
                :required="true"
                :error="!!validationErrors.heightInches"
                :errorMessage="validationErrors.heightInches"
                min="0"
                max="11"
              />
            </div>
          </div>

          <!-- Location Details -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input-text
              label="City"
              v-model="newClient.city"
              :required="true"
              :error="!!validationErrors.city"
              :errorMessage="validationErrors.city"
            />
            <input-text
              label="State"
              v-model="newClient.state"
              :required="true"
              :error="!!validationErrors.state"
              :errorMessage="validationErrors.state"
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <select-option
              label="Country"
              :options="countries"
              v-model="newClient.country"
              :required="true"
              :error="!!validationErrors.country"
              :errorMessage="validationErrors.country"
            />
            <input-text
              label="Current Location (City)"
              v-model="newClient.currentLocation"
              :required="true"
              :error="!!validationErrors.currentLocation"
              :errorMessage="validationErrors.currentLocation"
            />
          </div>

          <!-- Lifestyle Information -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <select-option
              label="Marital Status"
              :options="maritalStatuses"
              v-model="newClient.maritalStatus"
              :required="true"
              :error="!!validationErrors.maritalStatus"
              :errorMessage="validationErrors.maritalStatus"
            />
            <select-option
              label="Children"
              :options="childrenOptions"
              v-model="newClient.children"
              :required="true"
              :error="!!validationErrors.children"
              :errorMessage="validationErrors.children"
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <select-option
              label="Religion"
              :options="religions"
              v-model="newClient.religion"
              :required="true"
              :error="!!validationErrors.religion"
              :errorMessage="validationErrors.religion"
            />
            <select-option
              label="Smoker"
              :options="yesNoOptions"
              v-model="newClient.smoker"
              :required="true"
              :error="!!validationErrors.smoker"
              :errorMessage="validationErrors.smoker"
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <select-option
              label="Drinker"
              :options="drinkerOptions"
              v-model="newClient.drinker"
              :required="true"
              :error="!!validationErrors.drinker"
              :errorMessage="validationErrors.drinker"
            />
            <select-option
              label="English Level"
              :options="englishLevels"
              v-model="newClient.englishLevel"
              :required="true"
              :error="!!validationErrors.englishLevel"
              :errorMessage="validationErrors.englishLevel"
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input-text
              label="Languages"
              v-model="newClient.languages"
              :required="true"
              :error="!!validationErrors.languages"
              :errorMessage="validationErrors.languages"
              maxlength="100"
            />
            <input-text
              label="Interests"
              v-model="newClient.interests"
              :required="true"
              :error="!!validationErrors.interests"
              :errorMessage="validationErrors.interests"
              maxlength="500"
            />
          </div>

          <!-- Professional and Hobbies -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input-text
              label="Education"
              v-model="newClient.education"
              :required="true"
              :error="!!validationErrors.education"
              :errorMessage="validationErrors.education"
              maxlength="100"
            />
            <input-text
              label="Job Title"
              v-model="newClient.jobTitle"
              :required="true"
              :error="!!validationErrors.jobTitle"
              :errorMessage="validationErrors.jobTitle"
              maxlength="100"
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input-text
              label="Sports"
              v-model="newClient.sports"
              :required="true"
              :error="!!validationErrors.sports"
              :errorMessage="validationErrors.sports"
              maxlength="100"
            />
            <input-text
              label="Hobbies"
              v-model="newClient.hobbies"
              :required="true"
              :error="!!validationErrors.hobbies"
              :errorMessage="validationErrors.hobbies"
              maxlength="100"
            />
          </div>

          <!-- Submit Button -->
          <div class="mt-6 flex justify-end">
            <button
              class="bg-connectyed-button-light hover:bg-connectyed-button-hover-light text-connectyed-button-hover-light hover:text-connectyed-button-hover-dark py-2 px-4 rounded"
              type="submit"
              :disabled="processing"
            >
              {{ processing ? "Please wait" : newClient.isUpdating ? "Update" : "Save" }}
            </button>
          </div>
        </form>
      </div>

      <!-- List of Clients -->
      <div v-if="clients.length > 0">
        <h3 class="text-xl font-semibold mb-4">Clients List</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="client in clients"
            :key="client.id"
            class="p-4 bg-gray-100 rounded-lg shadow-md flex items-start"
          >
            <!-- Client Image -->
            <img
              :src="getImageUrl(client.thumbnail_image)"
              :alt="client.user.name"
              class="w-24 h-24 rounded-full object-cover mr-4"
            />

            <!-- Client Details -->
            <div>
              <h4 class="text-lg font-bold">
                <router-link :to="'/' + client.user.username">
                  {{ client.user.name }}
                </router-link>
              </h4>
              <p class="text-gray-500 text-sm">Age: {{ client.age }}</p>
              <p class="text-gray-500 text-sm">
                Location: {{ client.city }}, {{ client.state }}, {{ client.country }}
              </p>
              <p
                class="text-orange-500 text-sm py-2 cursor-pointer"
                @click="editClient(client.id)"
              >
                Edit
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- No Clients Message -->
      <div v-else class="mt-10 text-gray-500">No clients added yet.</div>
    </div>
  </div>
</template>

<script>
import InputText from '../../components/InputText.vue';
import SelectOption from '../../components/SelectOption.vue';
import axios from 'axios';
import { countries } from '../../components/countries';

export default {
  name: "MatchmakerClient",
  components: {
    InputText,
    SelectOption,
  },
  data() {
    return {
      loading: true,
      error: null,
      debugInfo: null, // Added for debugging purposes
      processing: false,
      showAddClientForm: false,
      user: null,
      authorization: null,
      newClient: {
        userId: "",
        isUpdating: false,
        name: "",
        email: "",
        city: "",
        state: "",
        country: "",
        currentLocation: "",
        age: "",
        hairColor: "",
        bodytype: "",
        heightFeet: "",
        heightInches: "",
        maritalStatus: "",
        children: "",
        religion: "",
        smoker: "",
        drinker: "",
        education: "",
        jobTitle: "",
        sports: "",
        hobbies: "",
        englishLevel: "",
        languages: "",
        interests: "",
        avatar: null,
      },
      validationErrors: {},
      clients: [],
      countries: countries, // Imported countries array
      bodyTypes: ['Slender', 'Average', 'Athletic', 'Curvy', 'Big and Beautiful'],
      maritalStatuses: ['Single', 'Separated', 'Divorced', 'Married'],
      childrenOptions: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10'],
      religions: [
        'Buddhism', 'Catholic', 'Christian', 'Confucianism', 'Hinduism',
        'Islam', 'Jainism', 'Judaism', 'Shinto', 'Sikhism',
        'Taoism', 'Zoroastrianism', 'Other'
      ],
      yesNoOptions: ['Yes', 'No'],
      drinkerOptions: ['None', 'Occasionally', 'Often'],
      englishLevels: ['Beginner', 'Intermediate', 'Proficient'],
      successMessage: '',
      preview: null,
      files: {
        avatar: null,
        additional_images: [],
      },
    };
  },
  methods: {
    /**
     * Generate a full image URL or return a default image if not available.
     * @param {String} path - The image path.
     * @returns {String} - The full image URL.
     */
    getImageUrl(path) {
      if (!path) return '/upload/images/profiles/default.png';
      return path.startsWith('http') ? path : `/storage/${path}`;
    },

    /**
     * Initialize the component by checking authentication and fetching clients.
     */
    async initializeComponent() {
      try {
        console.log('Initializing component...');

        // Debug: Log the entire Vuex state
        console.log('Full Vuex State:', this.$store.state);

        // Check if store is ready
        if (!this.$store?.state?.auth) {
          console.log('Store or auth state is missing');
          throw new Error('Authentication state not found');
        }

        const auth = this.$store.state.auth;
        console.log('Auth State:', auth);

        // Create debug info
        this.debugInfo = {
          storeExists: !!this.$store,
          authStateExists: !!auth,
          userExists: !!auth?.user,
          userDetails: auth?.user,
          token: auth?.authorization?.token ? 'Exists' : 'Missing'
        };

        // Check if user is authenticated and is a matchmaker
        if (!auth.authenticated || !auth.user || !auth.authorization?.token) {
          throw new Error('Please log in to continue');
        }

        if (auth.user.role !== 'matchmaker') {
          throw new Error('Access restricted to matchmakers only');
        }

        // Store user and authorization
        this.user = auth.user;
        this.authorization = auth.authorization;

        console.log('User authenticated:', {
          id: this.user.id,
          role: this.user.role
        });

        await this.getClient();

      } catch (error) {
        console.error('Initialization error:', error);
        this.error = error.message || 'An error occurred while loading';
      } finally {
        this.loading = false;
      }
    },

    /**
     * Toggle the visibility of the Add/Edit Client form.
     */
    toggleAddClientForm() {
      this.showAddClientForm = !this.showAddClientForm;
      if (!this.showAddClientForm) {
        this.resetForm();
      }
    },

    /**
     * Handle single file input changes.
     * @param {Event} event - The file input change event.
     * @param {String} field - The form field to update.
     */
    onFileChange(event, field) {
      const file = event.target.files[0];
      if (field === 'avatar') {
        this.files.avatar = file;
        if (file) {
          this.preview = URL.createObjectURL(file);
        } else {
          this.preview = null;
        }
      }
    },

    /**
     * Handle multiple file input changes.
     * @param {Event} event - The file input change event.
     */
    onMultipleFileChange(event) {
      this.files.additional_images = Array.from(event.target.files);
    },

    /**
     * Reset the Add/Edit Client form and related data.
     */
    resetForm() {
      this.newClient = {
        userId: "",
        isUpdating: false,
        name: "",
        email: "",
        city: "",
        state: "",
        country: "",
        currentLocation: "",
        age: "",
        hairColor: "",
        bodytype: "",
        heightFeet: "",
        heightInches: "",
        maritalStatus: "",
        children: "",
        religion: "",
        smoker: "",
        drinker: "",
        education: "",
        jobTitle: "",
        sports: "",
        hobbies: "",
        englishLevel: "",
        languages: "",
        interests: "",
        avatar: null,
      };
      this.validationErrors = {};
      this.successMessage = '';
      this.preview = null;
      this.files.avatar = null;
      this.files.additional_images = [];
    },

    /**
     * Add a new client.
     */
    async addClient() {
      if (!this.user || !this.authorization) {
        this.error = 'Please log in to continue';
        return;
      }

      this.processing = true;
      this.clearErrors();

      const formData = new FormData();

      // Append form fields (only non-null values)
      Object.keys(this.newClient).forEach(key => {
        if (key !== 'avatar' && key !== 'additional_images' && this.newClient[key] !== null && this.newClient[key] !== '') {
          formData.append(key, this.newClient[key]);
        }
      });

      // Append files
      if (this.files.avatar) {
        formData.append('avatar', this.files.avatar);
      }

      if (this.files.additional_images.length > 0) {
        this.files.additional_images.forEach((file, index) => {
          formData.append(`additional_images[${index}]`, file);
        });
      }

      try {
        const response = await axios.post('/api/matchmaker/clients', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            'Authorization': `Bearer ${this.authorization.token}`
          }
        });

        if (response.data.success) {
          this.successMessage = 'Client added successfully';
          this.resetForm();
          await this.getClient(); // Refresh client list
          this.showAddClientForm = false; // Optionally close the form after adding
        }
      } catch (error) {
        console.error('Error adding client:', error);
        if (error.response?.data?.errors) {
          this.validationErrors = error.response.data.errors;
        } else if (error.response?.data?.message) {
          this.validationErrors = { general: error.response.data.message };
        } else {
          this.validationErrors = { general: 'An error occurred while adding the client' };
        }
      } finally {
        this.processing = false;
      }
    },

    /**
     * Fetch clients associated with the authenticated matchmaker.
     */
    async getClient() {
      if (!this.user?.id || !this.authorization?.token) {
        console.error('Missing required data for getClient:', {
          userId: this.user?.id,
          hasToken: !!this.authorization?.token
        });
        throw new Error('Authentication required');
      }

      try {
        console.log('Fetching clients for user:', this.user.id);

        const response = await axios.get(`/api/matchmaker/clients/${this.user.id}`, {
          headers: {
            'Authorization': `Bearer ${this.authorization.token}`,
          },
        });

        console.log('Client response:', response.data);

        if (response.data.success) {
          // Transform data to ensure all fields are present
          this.clients = response.data.data.map(client => ({
            ...client,
            user: client.user || {},
            profile: client.profile || {},
            thumbnail_image: client.thumbnail_image,
            additional_images: Array.isArray(client.additional_images) 
              ? client.additional_images 
              : JSON.parse(client.additional_images || '[]')
          }));
        } else {
          this.error = response.data.message || 'Failed to fetch clients';
        }
      } catch (error) {
        console.error('getClient error:', error.response || error);
        this.error = error.response?.data?.message || 'Error fetching clients';
      }
    },

    /**
     * Edit an existing client's information.
     * @param {Number} id - The ID of the client to edit.
     */
    async editClient(id) {
      const selectedClient = this.clients.find(client => client.id === id);
      if (!selectedClient) {
        alert('Client not found.');
        return;
      }

      // Get the profile data if it exists
      const profile = selectedClient.profile || {};

      this.newClient = {
        userId: selectedClient.user_id,
        isUpdating: true,
        name: selectedClient.user?.name || '',
        email: selectedClient.user?.email || '',
        city: selectedClient.city || profile.city || '',
        state: selectedClient.state || profile.state || '',
        country: selectedClient.country || profile.country || '',
        currentLocation: profile.currentLocation || selectedClient.city || '',
        age: selectedClient.age || profile.age || '',
        hairColor: profile.hairColor || '',
        bodytype: selectedClient.body_type || profile.bodytype || '',
        heightFeet: selectedClient.height_feet || profile.height || '',
        heightInches: selectedClient.height_inches || profile.inches || '',
        maritalStatus: selectedClient.marital_status || profile.maritalstatus || '',
        children: (selectedClient.children || profile.children || 0).toString(),
        religion: selectedClient.religion || profile.religion || '',
        smoker: profile.smoker ? 'Yes' : 'No',
        drinker: profile.drinker || 'None',
        education: profile.education || '',
        jobTitle: profile.jobtitle || '',
        sports: profile.sports || '',
        hobbies: profile.hobbies || '',
        englishLevel: profile.english_level || 'Intermediate',
        languages: profile.languages || '',
        interests: selectedClient.interests || '',
        avatar: null,
      };

      this.preview = this.getImageUrl(selectedClient.thumbnail_image);
      this.validationErrors = {};
      this.successMessage = '';
      this.showAddClientForm = true;
    },

    /**
     * Update an existing client's information.
     */
    async updateClient() {
      if (!this.user || !this.authorization) {
        this.error = 'Please log in to continue';
        return;
      }

      this.processing = true;
      this.clearErrors();

      const formData = new FormData();
      
      // Append all fields except the ones we want to exclude for update
      Object.keys(this.newClient).forEach(key => {
        if (!['userId', 'isUpdating', 'avatar'].includes(key) && this.newClient[key] !== null) {
          formData.append(key, this.newClient[key]);
        }
      });

      // Add files if they exist
      if (this.files.avatar) {
        formData.append('avatar', this.files.avatar);
      }

      if (this.files.additional_images.length > 0) {
        this.files.additional_images.forEach((file, index) => {
          formData.append(`additional_images[${index}]`, file);
        });
      }

      try {
        const response = await axios.post(
          `/api/matchmaker/clients/${this.newClient.userId}`, 
          formData,
          {
            headers: {
              'Content-Type': 'multipart/form-data',
              'Authorization': `Bearer ${this.authorization.token}`
            }
          }
        );

        if (response.data.success) {
          this.successMessage = 'Client updated successfully';
          this.resetForm();
          await this.getClient(); // Refresh client list
          this.showAddClientForm = false;
        }
      } catch (error) {
        console.error('Error updating client:', error);
        this.handleError(error);
      } finally {
        this.processing = false;
      }
    },

    /**
     * Handle errors by setting validation errors appropriately.
     * @param {Object} error - The error object from the catch block.
     */
    handleError(error) {
      if (error.response?.data?.errors) {
        this.validationErrors = error.response.data.errors;
      } else if (error.response?.data?.message) {
        this.validationErrors = { general: error.response.data.message };
      } else {
        this.validationErrors = { general: 'An error occurred' };
      }
    },

    /**
     * Clear error messages.
     */
    clearErrors() {
      this.validationErrors = {};
      this.error = null;
    },
  },
  async created() {
    console.log('Component created');
    // Wait for store to be initialized
    if (!this.$store.getters['auth/isInitialized']) {
      await this.$store.dispatch('auth/initialize');
    }
    await this.initializeComponent();
  },
  mounted() {
    console.log('Component mounted');
    console.log('Current Vuex State:', this.$store.state);
  }
};
</script>

<style scoped>
/* Component-specific styles */

/* Error Message Styles */
.text-red-500 {
  color: #f56565;
}
.text-green-500 {
  color: #48bb78;
}
.text-gray-700 {
  color: #4a5568;
}
.text-gray-500 {
  color: #a0aec0;
}
.text-xs {
  font-size: 0.75rem;
}
.text-sm {
  font-size: 0.875rem;
}
.text-lg {
  font-size: 1.125rem;
}
.text-xl {
  font-size: 1.25rem;
}
.text-2xl {
  font-size: 1.5rem;
}
.text-orange-500 {
  color: #ed8936;
}
.text-connectyed-button-light {
  background-color: #4ade80; /* Example color */
}
.text-connectyed-button-dark {
  color: #065f46; /* Example color */
}
.text-connectyed-button-hover-light:hover {
  background-color: #38b2ac; /* Example hover color */
}
.text-connectyed-button-hover-dark:hover {
  color: #047857; /* Example hover color */
}
.bg-connectyed-button-light {
  background-color: #4ade80; /* Example color */
}
.bg-connectyed-button-pagination-light {
  background-color: #38b2ac; /* Example color */
}
.bg-connectyed-button-hover-light:hover {
  background-color: #2c7a7b; /* Example hover color */
}
.bg-connectyed-button-dark {
  background-color: #065f46; /* Example color */
}
.hover:text-connectyed-button-hover-dark:hover {
  color: #047857; /* Example hover color */
}
.alert-danger {
  background-color: #f8d7da;
  border-color: #f5c6cb;
  padding: 1rem;
  border-radius: 0.25rem;
}

/* Spinner Animation */
@keyframes spin {
  to { transform: rotate(360deg); }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Debug Info Styles */
pre {
  white-space: pre-wrap;
  word-wrap: break-word;
}
</style>
