# ProfileCard.vue
<template>
  <div class="profile-card bg-white rounded-lg shadow-lg overflow-hidden">
    <!-- Profile Picture -->
    <div class="profile-image w-full h-56 overflow-hidden">
      <img 
        :src="profileImage" 
        :alt="matchmaker.name"
        class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
      />
    </div>

    <!-- Matchmaker Info -->
    <div class="profile-info p-4">
      <h3 class="text-xl font-semibold text-gray-800 mb-2 truncate">{{ matchmaker.name }}</h3>
      
      <div class="info-grid mb-3">
        <!-- Location -->
        <p v-if="location" class="flex items-center text-sm">
          <span class="font-medium mr-2">📍</span>
          <span class="truncate">{{ location }}</span>
        </p>

        <!-- Age -->
        <p v-if="matchmaker.profile?.age" class="flex items-center text-sm">
          <span class="font-medium mr-2">Age:</span>
          {{ matchmaker.profile.age }}
        </p>

        <!-- Experience -->
        <p v-if="matchmaker.profile?.yearsexperience" class="flex items-center text-sm">
          <span class="font-medium mr-2">Experience:</span>
          {{ matchmaker.profile.yearsexperience }} years
        </p>
      </div>

      <!-- Bio -->
      <div class="bio-section mb-3">
        <p v-if="matchmaker.profile?.bio" class="text-sm text-gray-600" :class="{ 'line-clamp-3': !expandBio }">
          {{ matchmaker.profile.bio }}
        </p>
        <button 
          v-if="hasLongBio" 
          @click="expandBio = !expandBio"
          class="text-xs text-blue-500 mt-1 hover:underline"
        >
          {{ expandBio ? 'Show Less' : 'Show More' }}
        </button>
      </div>

      <!-- Clients Section -->
      <div v-if="clients.length" class="mb-3">
        <h4 class="text-sm font-medium text-gray-700 mb-2">Clients</h4>
        <div class="flex flex-wrap gap-2">
          <div 
            v-for="client in displayedClients" 
            :key="client.id"
            class="client-thumbnail relative"
          >
            <img 
              :src="client.thumbnail_image || '/upload/images/profiles/default-client-image.png'" 
              :alt="client.name"
              class="w-10 h-10 rounded-full object-cover border-2 border-white"
            />
            <div class="client-info absolute bottom-0 right-0 bg-black bg-opacity-50 text-white text-xs px-1 rounded">
              {{ client.age }}
            </div>
          </div>
        </div>
      </div>

      <!-- Availability -->
      <div v-if="matchmaker.availability?.length" class="availability-section">
        <h4 class="text-sm font-medium text-gray-700 mb-1">Available Times</h4>
        <div class="availability-container max-h-20 overflow-y-auto">
          <div class="text-xs text-gray-600 space-y-1">
            <p v-for="(slot, index) in limitedAvailability" :key="index" class="line-clamp-1">
              {{ formatAvailability(slot) }}
            </p>
          </div>
        </div>
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
    const MAX_DISPLAYED_CLIENTS = 6

    const profileImage = computed(() => {
      return props.matchmaker.profile?.profile_image1 || '/upload/images/profiles/default.png'
    })

    const location = computed(() => {
      const profile = props.matchmaker.profile
      if (!profile) return null
      
      const parts = [profile.city, profile.state, profile.country]
        .filter(Boolean)
        .join(', ')
      
      return parts || 'Location not specified'
    })

    const hasLongBio = computed(() => {
      const bio = props.matchmaker.profile?.bio
      return bio && bio.length > 120 // Reduced threshold to show more bios
    })

    const displayedClients = computed(() => {
      return clients.value.slice(0, MAX_DISPLAYED_CLIENTS)
    })

    const limitedAvailability = computed(() => {
      return props.matchmaker.availability?.slice(0, 3) || []
    })

    const formatAvailability = (slot) => {
      if (!slot.start_date) return 'Schedule not specified'

      const startDate = new Date(slot.start_date).toLocaleDateString(undefined, {
        month: 'short',
        day: 'numeric'
      })
      
      const startTime = slot.start_time
        ? new Date(`1970-01-01T${slot.start_time}Z`).toLocaleTimeString([], { 
            hour: '2-digit', 
            minute: '2-digit' 
          })
        : 'All Day'

      return `${startDate} at ${startTime}`
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
      hasLongBio,
      profileImage,
      location,
      displayedClients,
      limitedAvailability,
      formatAvailability
    }
  }
}
</script>

<style scoped>
.profile-card {
  height: 600px;
  width: 100%;
  max-width: 400px;
  margin: auto;
  display: flex;
  flex-direction: column;
}

.profile-image {
  flex-shrink: 0;
}

.profile-info {
  flex: 1;
  overflow-y: auto;
  scrollbar-width: thin;
  scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
}

.info-grid {
  display: grid;
  gap: 0.5rem;
}

.client-thumbnail {
  transition: transform 0.2s;
}

.client-thumbnail:hover {
  transform: scale(1.1);
  z-index: 1;
}

/* Scrollbar styling */
.profile-info::-webkit-scrollbar,
.availability-container::-webkit-scrollbar {
  width: 4px;
}

.profile-info::-webkit-scrollbar-track,
.availability-container::-webkit-scrollbar-track {
  background: transparent;
}

.profile-info::-webkit-scrollbar-thumb,
.availability-container::-webkit-scrollbar-thumb {
  background-color: rgba(0, 0, 0, 0.2);
  border-radius: 4px;
}

/* Line clamp utility */
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Add some breathing room at the bottom of scrollable content */
.profile-info > :last-child {
  margin-bottom: 1rem;
}

/* Ensure bio text wraps properly */
.bio-section p {
  white-space: pre-line;
  word-break: break-word;
}
</style>