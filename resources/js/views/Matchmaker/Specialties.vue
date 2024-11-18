<template>
  <div class="max-w-4xl mx-auto p-6 bg-white rounded-sm shadow-sm">
    <h2 class="text-2xl font-semibold mb-4">Matchmaker Specialties</h2>

    <form class="w-full" @submit.prevent="postSpecialties">
      <!-- Age Range Inputs -->
      <div class="mb-4">
        <label class="block uppercase font-bold text-xs text-gray-700 mb-2">
          My Clients are between the ages of
        </label>
        <div class="flex space-x-2">
          <input
            type="number"
            v-model="specialties.minage"
            class="w-full p-2 border border-gray-300 rounded-md"
            placeholder="Min Age"
            min="18"
            id="minage"
            required
          />
          <input
            type="number"
            v-model="specialties.maxage"
            class="w-full p-2 border border-gray-300 rounded-md"
            placeholder="Max Age"
            min="18"
            id="maxage"
            required
          />
        </div>
      </div>

      <!-- Gender Select Option -->
      <div class="mb-6">
        <label class="block uppercase font-bold text-xs text-gray-700 mb-2">
          My Clients are
        </label>
        <select
          v-model="specialties.gender"
          class="w-full p-2 border border-gray-300 rounded-md"
          required
        >
          <option disabled value="">Select Gender</option>
          <option value="male">Male</option>
          <option value="female">Female</option>
          <option value="male_female">Male & Female</option>
        </select>
      </div>

      <!-- Selected Locations -->
      <div class="mb-4">
        <label class="block uppercase font-bold text-xs text-gray-700 mb-2">
          My Clients are from the following locations (Select up to 5 locations)
        </label>
        <div class="flex flex-wrap gap-2 mb-2">
          <span
            v-for="location in selectedLocations"
            :key="location"
            class="bg-blue-500 text-white rounded-full px-4 py-1 flex items-center"
          >
            {{ location }}
            <button
              type="button"
              @click="removeLocation(location)"
              class="ml-2 text-white hover:text-red-400"
            >
              ×
            </button>
          </span>
        </div>

        <!-- Location Search -->
        <div class="relative" ref="dropdownRef">
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Search for locations..."
            class="w-full p-2 border border-gray-300 rounded-md"
            @focus="showDropdown = true"
            @input="onSearchInput"
          />

          <!-- Location Dropdown -->
          <div
            v-if="showDropdown && filteredLocations.length"
            class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto"
          >
            <button
              v-for="location in filteredLocations"
              :key="location"
              type="button"
              @click="addLocation(location)"
              class="w-full px-4 py-2 text-left hover:bg-gray-100 focus:outline-none"
            >
              {{ location }}
            </button>
          </div>
        </div>
      </div>

      <!-- Submit Button -->
      <div class="flex justify-end mt-6">
        <button
          type="submit"
          :disabled="processing"
          class="bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600 disabled:opacity-50"
        >
          {{ processing ? 'Saving...' : 'Save' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { countries } from '../../components/countries' // Ensure this path is correct
import axios from 'axios'
import { useStore } from 'vuex'

export default {
  name: 'Specialties',

  setup() {
    const store = useStore()
    const authorization = store.state.auth.authorization

    // Use the countries array directly since it's already an array of strings
    const availableLocations = countries

    // Reactive references
    const specialties = ref({
      minage: '',
      maxage: '',
      gender: '',
      locations: []
    })
    const selectedLocations = ref([])
    const searchQuery = ref('')
    const showDropdown = ref(false)
    const processing = ref(false)

    // Reference to the dropdown wrapper (input + dropdown)
    const dropdownRef = ref(null)

    // Computed property for filtered locations
    const filteredLocations = computed(() => {
      if (!searchQuery.value) {
        return availableLocations.slice(0, 10) // Show first 10 countries when no search
      }

      const query = searchQuery.value.toLowerCase()
      return availableLocations.filter(country => 
        country.toLowerCase().includes(query) && 
        !selectedLocations.value.includes(country)
      )
    })

    // Method to handle click outside the dropdown
    const handleClickOutside = (event) => {
      const dropdownElement = dropdownRef.value
      if (dropdownElement && !dropdownElement.contains(event.target)) {
        showDropdown.value = false
      }
    }

    // Add event listener for clicks outside the dropdown
    onMounted(() => {
      document.addEventListener('click', handleClickOutside)
      getSpecialties()
    })

    // Remove event listener when component is unmounted
    onBeforeUnmount(() => {
      document.removeEventListener('click', handleClickOutside)
    })

    // Add location method
    const addLocation = (country) => {
      if (selectedLocations.value.length >= 5) {
        alert('You can only select up to 5 locations')
        return
      }

      if (!selectedLocations.value.includes(country)) {
        selectedLocations.value.push(country)
        specialties.value.locations = selectedLocations.value // Keep specialties.locations in sync
        searchQuery.value = ''
        showDropdown.value = false
      }
    }

    // Remove location method
    const removeLocation = (country) => {
      const index = selectedLocations.value.indexOf(country)
      if (index > -1) {
        selectedLocations.value.splice(index, 1)
        specialties.value.locations = selectedLocations.value // Keep specialties.locations in sync
      }
    }

    // Handle input in search field
    const onSearchInput = () => {
      showDropdown.value = true
    }

    // Post the specialties data to the server
    const postSpecialties = async () => {
      try {
        processing.value = true

        // Create a copy of specialties with locations
        const specialtiesData = {
          ...specialties.value,
          locations: selectedLocations.value // Send as array; server handles JSON encoding
        }

        const response = await axios.put('/api/specialties/update', 
          { specialties: specialtiesData },
          { headers: { Authorization: `Bearer ${authorization.token}` }}
        )

        if (response.data.success) {
          alert('Specialties updated successfully')
        }
      } catch (error) {
        console.error('Error updating specialties:', error.response?.data)
        const errorMessage = error.response?.data?.message || 
                             'Failed to update specialties. Please try again.'
        alert(errorMessage)
      } finally {
        processing.value = false
      }
    }

    // Fetch specialties from the server
    const getSpecialties = async () => {
      try {
        const response = await axios.get('/api/specialties/getspecialties', {
          headers: { Authorization: `Bearer ${authorization.token}` }
        })

        if (response.data.success && response.data.data) {
          const specialtiesData = response.data.data
          
          // Update the form data
          specialties.value = {
            minage: specialtiesData.minage || '',
            maxage: specialtiesData.maxage || '',
            gender: specialtiesData.gender || '',
            locations: specialtiesData.locations || []
          }

          // Parse the locations string into an array
          selectedLocations.value = specialtiesData.locations ? 
            JSON.parse(specialtiesData.locations) : []
        }
      } catch (error) {
        console.error('Error fetching specialties:', error)
        selectedLocations.value = []
      }
    }

    return {
      specialties,
      selectedLocations,
      searchQuery,
      showDropdown,
      processing,
      availableLocations,
      filteredLocations,
      addLocation,
      removeLocation,
      postSpecialties,
      getSpecialties
    }
  }
}
</script>

<style scoped>
/* Add any component-specific styles here */
</style>
