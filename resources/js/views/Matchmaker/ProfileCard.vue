# ProfileCard.vue
<template>
  <div class="profile-card bg-white rounded-xl shadow-lg overflow-hidden">
    <!-- Profile Header -->
    <div class="relative">
      <div class="profile-image w-full h-72 overflow-hidden">
        <img 
          :src="profileImage" 
          :alt="matchmaker.name"
          class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
        />
      </div>
      
      <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
        <h3 class="text-2xl font-bold text-white mb-1">{{ matchmaker.name }}</h3>
        <p v-if="location" class="flex items-center text-white/90 text-sm">
          <span class="mr-1">📍</span>
          {{ location }}
        </p>
      </div>
    </div>

    <!-- Main Content -->
    <div class="profile-info p-5">
      <!-- Key Stats -->
      <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="stat-box bg-gray-50 rounded-lg p-3 text-center">
          <span class="text-sm text-gray-600">Experience</span>
          <p class="text-lg font-semibold text-gray-800">
            {{ matchmaker.profile?.yearsexperience || 0 }} years
          </p>
        </div>
        <div class="stat-box bg-gray-50 rounded-lg p-3 text-center">
          <span class="text-sm text-gray-600">Age</span>
          <p class="text-lg font-semibold text-gray-800">
            {{ matchmaker.profile?.age || 'N/A' }}
          </p>
        </div>
      </div>

      <!-- Specialties Section -->
      <div v-if="matchmaker.specialties" class="mb-4">
        <h4 class="text-sm font-semibold text-gray-700 mb-2">Specialties</h4>
        <div class="space-y-2">
          <div class="flex items-center gap-2">
            <span class="text-sm font-medium text-gray-600">Age Range:</span>
            <span class="text-sm">{{ matchmaker.specialties.minage }} - {{ matchmaker.specialties.maxage }}</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-sm font-medium text-gray-600">Preferred Gender:</span>
            <span class="text-sm capitalize">{{ matchmaker.specialties.gender }}</span>
          </div>
          <div v-if="specialtyLocations.length" class="specialty-locations">
            <span class="text-sm font-medium text-gray-600">Locations:</span>
            <div class="flex flex-wrap gap-1 mt-1">
              <span 
                v-for="location in specialtyLocations" 
                :key="location"
                class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded-full"
              >
                {{ location }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Bio -->
      <div class="bio-section mb-4">
        <h4 class="text-sm font-semibold text-gray-700 mb-2">About</h4>
        <p 
          v-if="matchmaker.profile?.bio" 
          class="text-sm text-gray-600" 
          :class="{ 'line-clamp-3': !expandBio }"
        >
          {{ matchmaker.profile.bio }}
        </p>
        <button 
          v-if="hasLongBio" 
          @click="expandBio = !expandBio"
          class="text-xs text-blue-600 mt-2 hover:underline"
        >
          {{ expandBio ? 'Show Less' : 'Read More' }}
        </button>
      </div>

      <!-- Clients Gallery -->
      <div v-if="clients.length" class="mb-4">
        <h4 class="text-sm font-semibold text-gray-700 mb-2">Recent Clients</h4>
        <div class="grid grid-cols-4 gap-2">
          <div 
            v-for="client in displayedClients" 
            :key="client.id"
            class="client-thumbnail group relative"
          >
            <img 
              :src="client.thumbnail_image || '/upload/images/profiles/default-client-image.png'" 
              :alt="client.name"
              class="w-full h-16 object-cover rounded-lg shadow-sm transition-transform duration-200 group-hover:scale-105"
            />
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-lg flex items-center justify-center">
              <span class="text-white text-xs">{{ client.age }}y</span>
            </div>
          </div>
        </div>
      </div>

      <!-- View More Button -->
      <button 
        @click="showMoreInfo = !showMoreInfo"
        class="w-full py-2 px-4 text-sm font-medium text-gray-600 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200 flex items-center justify-center gap-2"
      >
        <span>{{ showMoreInfo ? 'View Less' : 'View More' }}</span>
        <span :class="{ 'rotate-180': showMoreInfo }" class="transition-transform duration-200">
          ↓
        </span>
      </button>

      <!-- Additional Info (Hidden by default) -->
      <div 
        v-if="showMoreInfo"
        class="mt-4 space-y-4 animate-fade-in"
      >
        <!-- Availability -->
        <div v-if="matchmaker.availability?.length" class="availability-section">
          <h4 class="text-sm font-semibold text-gray-700 mb-2">Availability</h4>
          <div class="space-y-1.5">
            <div 
              v-for="(slot, index) in limitedAvailability" 
              :key="index"
              class="flex items-center text-sm text-gray-600 bg-gray-50 rounded-lg p-2"
            >
              <span class="mr-2">📅</span>
              {{ formatAvailability(slot) }}
            </div>
          </div>
        </div>

        <!-- Add any other sections you want to show in the expanded view -->
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

export default {
  name: "ProfileCard",
  props: {
    matchmaker: {
      type: Object,
      required: true
    }
  },

  setup(props) {
    const clients = ref([])
    const expandBio = ref(false)
    const showMoreInfo = ref(false)
    const MAX_DISPLAYED_CLIENTS = 8

    const profileImage = computed(() => {
      return props.matchmaker.profile?.profile_image1 || '/upload/images/profiles/default.png'
    })

    const location = computed(() => {
      const profile = props.matchmaker.profile
      if (!profile) return null
      
      const parts = [profile.city, profile.country]
        .filter(Boolean)
        .join(', ')
      
      return parts || 'Location not specified'
    })

    const specialtyLocations = computed(() => {
      if (!props.matchmaker.specialties?.locations) return []
      try {
        return JSON.parse(props.matchmaker.specialties.locations)
      } catch {
        return []
      }
    })

    const hasLongBio = computed(() => {
      const bio = props.matchmaker.profile?.bio
      return bio && bio.length > 150
    })

    const displayedClients = computed(() => {
      return clients.value.slice(0, MAX_DISPLAYED_CLIENTS)
    })

    const limitedAvailability = computed(() => {
      return props.matchmaker.availability?.slice(0, 3) || []
    })

    const formatAvailability = (slot) => {
      if (!slot.start_date) return 'Schedule not specified'

      const startDate = new Date(slot.start_date)
      const endDate = new Date(slot.end_date)

      const formatDate = (date) => {
        return date.toLocaleDateString('en-US', {
          month: 'short',
          day: 'numeric'
        })
      }

      if (slot.start_date === slot.end_date) {
        return `${formatDate(startDate)} ${slot.is_all_day ? '(All Day)' : ''}`
      }

      return `${formatDate(startDate)} - ${formatDate(endDate)}`
    }

    const fetchClients = async () => {
      try {
        const response = await axios.get(`/api/public/matchmaker/clients/${props.matchmaker.id}`)
        if (response.data.success) {
          clients.value = response.data.data
        }
      } catch (error) {
        console.error('Error fetching clients:', error)
      }
    }

    onMounted(() => {
      fetchClients()
    })

    return {
      clients,
      expandBio,
      showMoreInfo,
      hasLongBio,
      profileImage,
      location,
      specialtyLocations,
      displayedClients,
      limitedAvailability,
      formatAvailability
    }
  }
}
</script>

<style scoped>
.profile-card {
  height: 700px; /* Reduced height since availability is now hidden by default */
  width: 100%;
  max-width: 400px;
  margin: auto;
  display: flex;
  flex-direction: column;
}

.profile-info {
  flex: 1;
  overflow-y: auto;
  scrollbar-width: thin;
  scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
}

/* Custom scrollbar styling */
.profile-info::-webkit-scrollbar {
  width: 4px;
}

.profile-info::-webkit-scrollbar-track {
  background: transparent;
}

.profile-info::-webkit-scrollbar-thumb {
  background-color: rgba(0, 0, 0, 0.2);
  border-radius: 4px;
}

/* Line clamp utilities */
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Animation for expanding section */
.animate-fade-in {
  animation: fadeIn 0.2s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Smooth transitions */
.client-thumbnail img {
  transition: all 0.2s ease-in-out;
}

/* Add bottom padding to scrollable content */
.profile-info > :last-child {
  margin-bottom: 1rem;
}

/* Ensure text wrapping */
.bio-section p {
  white-space: pre-line;
  word-break: break-word;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .profile-card {
    height: 650px;
  }

  .profile-image {
    height: 250px;
  }
}
</style>