<template>
  <div class="max-w-4xl mx-auto p-6 bg-white rounded-sm shadow-sm">
    <h2 class="text-2xl font-semibold mb-4">Matchmaker Profile</h2>
        
    <!-- Profile Pictures -->
    <div class="mb-6 flex gap-4">
      <!-- Profile Image 1 -->
      <div class="w-1/3">
        <h3 class="text-sm font-medium mb-2">Profile Image 1</h3>
        <div class="mb-2" v-if="profile.profile_image1">
          <img :src="profile.profile_image1" alt="Profile Image 1" class="rounded-lg object-cover w-48 h-48" />
        </div>
        <label class="cursor-pointer block">
          <span class="text-connectyed-button-dark bg-connectyed-button-light hover:bg-connectyed-button-hover hover:text-connectyed-button-hover-dark focus:outline-none focus:ring-2 focus:ring-blue-500 px-4 py-2 block text-center">
            Upload Image 1
          </span>
          <input
            type="file"
            class="hidden"
            @change="uploadImage($event, 1)"
            accept="image/*"
          />
        </label>
      </div>

      <!-- Profile Image 2 -->
      <div class="w-1/3">
        <h3 class="text-sm font-medium mb-2">Profile Image 2</h3>
        <div class="mb-2" v-if="profile.profile_image2">
          <img :src="profile.profile_image2" alt="Profile Image 2" class="rounded-lg object-cover w-48 h-48" />
        </div>
        <label class="cursor-pointer block">
          <span class="text-connectyed-button-dark bg-connectyed-button-light hover:bg-connectyed-button-hover hover:text-connectyed-button-hover-dark focus:outline-none focus:ring-2 focus:ring-blue-500 px-4 py-2 block text-center">
            Upload Image 2
          </span>
          <input
            type="file"
            class="hidden"
            @change="uploadImage($event, 2)"
            accept="image/*"
          />
        </label>
      </div>
    </div>
              
    <form class="w-full" @submit.prevent="postProfile">
      <!-- Name -->
      <div class="flex flex-wrap -mx-3 mb-4">
        <div class="w-full px-3">
          <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="name">
            Full Name
          </label>
          <input 
            class="appearance-none block w-full border border-gray-500 rounded p-2 leading-tight focus:outline-none focus:bg-white" 
            type="text" 
            v-model="profile.name" 
            id="name"
          >
        </div>
      </div>

      <!-- Experience and Job Title -->
      <div class="flex flex-wrap -mx-3 mb-4">
        <div class="w-full md:w-1/2 px-3 mb-4 md:mb-0">
          <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="yearsexperience">
            Years of Experience
          </label>
          <input 
            class="appearance-none block w-full border border-gray-200 rounded p-2 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" 
            v-model="profile.yearsexperience" 
            id="yearsexperience" 
            type="number"
          >
        </div>
        <div class="w-full md:w-1/2 px-3">
          <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="jobtitle">
            Job Title
          </label>
          <input 
            class="appearance-none block w-full border border-gray-200 rounded p-2 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" 
            v-model="profile.jobtitle" 
            id="jobtitle" 
            type="text"
          >
        </div>
      </div>

      <!-- Location Fields -->
      <div class="flex flex-wrap -mx-3 mb-4">
        <div class="w-full md:w-1/3 px-3 mb-4 md:mb-0">
          <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="city">
            City
          </label>
          <input 
            class="appearance-none block w-full border border-gray-200 rounded p-2 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" 
            v-model="profile.city" 
            id="city" 
            type="text"
          >
        </div>
        
        <div class="w-full md:w-1/3 px-3 mb-4 md:mb-0">
          <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="state">
            State
          </label>
          <input 
            class="appearance-none block w-full border border-gray-200 rounded p-2 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" 
            v-model="profile.state" 
            id="state" 
            type="text"
          >
        </div>

        <div class="w-full md:w-1/3 px-3 mb-4 md:mb-0">
          <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="country">
            Country
          </label>
          <input 
            class="appearance-none block w-full border border-gray-200 rounded p-2 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" 
            v-model="profile.country" 
            id="country" 
            type="text"
          >
        </div>
      </div>

      <!-- Bio -->
      <div class="flex flex-wrap -mx-3 mb-6">
        <div class="w-full px-3">
          <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="bio">
            Bio
          </label>
          <textarea 
            class="appearance-none block w-full border border-gray-200 rounded p-2 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" 
            v-model="profile.bio" 
            id="bio"
            rows="4"
          ></textarea>
        </div>
      </div>

      <!-- Submit Button -->
      <div class="flex items-center justify-end">
        <button
          class="bg-connectyed-button-light text-connectyed-button-dark hover:bg-connectyed-button-hover hover:text-connectyed-button-hover-dark font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
          type="submit"
          :disabled="processing"
        >
          {{ processing ? 'Saving...' : 'Save Profile' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      profile: {
        name: '',
        city: '',
        state: '',
        country: '',
        yearsexperience: '',
        jobtitle: '',
        bio: '',
        profile_image1: '',
        profile_image2: '',
      },
      processing: false,
      authorization: this.$store.state.auth.authorization,
    }
  },
  
  mounted() {
    this.getProfile()
  },

  methods: {
    async postProfile() {
      try {
        this.processing = true
        axios.defaults.headers.common.Authorization = `Bearer ${this.authorization.token}`
        
        const response = await axios.put('/api/profile/update', {
          profile: this.profile
        })

        if (response.data.success) {
          alert('Profile updated successfully')
        }
      } catch (error) {
        console.error('Error updating profile:', error)
        alert('Failed to update profile. Please try again.')
      } finally {
        this.processing = false
      }
    },

    async uploadImage(event, imageNumber) {
      const file = event.target.files[0]
      if (!file) return

      try {
        this.processing = true
        const formData = new FormData()
        formData.append('file', file)
        formData.append('image_number', imageNumber)

        axios.defaults.headers.common.Authorization = `Bearer ${this.authorization.token}`
        const response = await axios.post('/api/profile/uploadimage', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })

        if (response.data.success) {
          this.profile[`profile_image${imageNumber}`] = response.data.data
        }
      } catch (error) {
        console.error('Error uploading image:', error)
        alert('Failed to upload image. Please try again.')
      } finally {
        this.processing = false
      }
    },

    async getProfile() {
      try {
        this.processing = true
        axios.defaults.headers.common.Authorization = `Bearer ${this.authorization.token}`
        
        const response = await axios.get('/api/profile/getprofile')
        
        if (response.data.success) {
          this.profile = {
            ...response.data.data,
            name: response.data.data.name || response.data.data.user.name,
          }
        }
      } catch (error) {
        console.error('Error fetching profile:', error)
        alert('Failed to load profile. Please refresh the page.')
      } finally {
        this.processing = false
      }
    }
  }
}
</script>