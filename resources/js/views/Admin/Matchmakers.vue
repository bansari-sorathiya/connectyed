<!-- resources/js/components/Matchmakers.vue -->
<template>
  <div class="p-8 bg-gray-100 min-h-screen">
    <h1 class="text-2xl font-bold mb-6 text-gray-700">Matchmakers to be Approved</h1>

    <div v-if="candidates.length === 0" class="text-gray-500">
      No candidates to approve.
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="candidate in candidates"
        :key="candidate.id"
        class="p-6 bg-white shadow-md rounded-lg"
      >
        <!-- Profile Image 1 -->
        <div v-if="candidate.profile && candidate.profile.profile_image1">
          <img
            :src="candidate.profile.profile_image1"
            alt="Profile Image 1"
            class="w-full h-48 object-cover rounded-lg"
          />
        </div>

        <!-- Name and Email -->
        <h2 class="text-lg font-semibold text-[#213366] mt-4">{{ candidate.name }}</h2>
        <p class="text-gray-500">{{ candidate.email }}</p>

        <!-- Location -->
        <p class="text-gray-500">
          {{ candidate.profile.city }}, {{ candidate.profile.state }}, {{ candidate.profile.country }}
        </p>

        <!-- Age and Years of Experience -->
        <p class="text-gray-500">Age: {{ candidate.profile.age }}</p>
        <p class="text-gray-500">Years of Experience: {{ candidate.profile.yearsexperience }}</p>

        <!-- Bio -->
        <p class="mt-2 text-gray-700">{{ candidate.profile.bio }}</p>

        <!-- Profile Image 2 -->
        <div v-if="candidate.profile && candidate.profile.profile_image2" class="mt-4">
          <img
            :src="candidate.profile.profile_image2"
            alt="Profile Image 2"
            class="w-full h-48 object-cover rounded-lg"
          />
        </div>

        <!-- Approve Button -->
        <div class="mt-4">
          <button
            @click="approveCandidate(candidate.id)"
            class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600"
          >
            Approve
          </button>
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
      authorization: this.$store.state.auth.authorization,
      candidates: [],
    };
  },
  mounted() {
    this.fetchCandidates();
  },
  methods: {
    async fetchCandidates() {
      this.processing = true;
      axios.defaults.headers.common.Authorization = `Bearer ${this.authorization.token}`;
      await axios
        .get('/api/admin/candidates')
        .then((response) => {
          this.candidates = response.data.data;
        })
        .catch((error) => {
          console.error(error);
        })
        .finally(() => {
          this.processing = false;
        });
    },
    async approveCandidate(userId) {
      this.processing = true;
      axios.defaults.headers.common.Authorization = `Bearer ${this.authorization.token}`;
      await axios
        .post('/api/admin/candidates/approve', {
          user_id: userId,
        })
        .then(({ data }) => {
          this.candidates = this.candidates.filter((candidate) => candidate.id !== userId);
        })
        .catch((error) => {
          console.error(error);
        })
        .finally(() => {
          this.processing = false;
        });
    },
  },
};
</script>

<style scoped>
/* Adjust image sizes */
img {
  object-fit: cover;
}

/* Adjust card styles */
.p-6 {
  padding: 1.5rem;
}

.bg-white {
  background-color: #fff;
}

/* Add hover effects */
button:hover {
  background-color: #e07b39;
}
</style>
