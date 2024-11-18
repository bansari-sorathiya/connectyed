<template>
  <div class="container mx-auto p-6">
    <!-- Package Info -->
    <div v-if="user?.purchased_package" class="mb-10 bg-white p-8 rounded-xl shadow-lg">
      <h2 class="text-2xl font-semibold text-gray-800 mb-4">
        Your {{ formatPackageName(user.purchased_package) }} Package
      </h2>
      <p class="text-gray-600 text-lg">
        You can select up to <span class="font-semibold">{{ user.criteria_limit }}</span> criteria
        <span v-if="selectedCriteria.length > 0">
          ({{ selectedCriteria.length }} selected)
        </span>
      </p>
    </div>

    <!-- Criteria Selection -->
    <div class="bg-white p-8 rounded-xl shadow-lg">
      <h3 class="text-xl font-semibold text-gray-800 mb-6">Select Your Match Criteria</h3>
      
      <form @submit.prevent="saveCriteria">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          
          <!-- Age Range -->
          <div class="criteria-item" :class="{ 'opacity-50 cursor-not-allowed': !canSelectMore && !isSelected('age') }">
            <div class="flex items-center mb-3">
              <input
                type="checkbox"
                id="age"
                v-model="selectedCriteriaMap.age"
                :disabled="!canSelectMore && !isSelected('age')"
                class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                @change="handleCriteriaChange('age')"
              />
              <label for="age" class="ml-3 text-lg font-medium text-gray-700">Age Range</label>
            </div>
            <div v-if="isSelected('age')" class="ml-8 mt-2">
              <div class="flex flex-col md:flex-row md:space-x-4">
                <div class="flex-1">
                  <label class="block text-sm text-gray-600 mb-1">Min Age</label>
                  <input
                    type="number"
                    v-model="criteriaValues.age.min"
                    class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    min="18"
                    max="100"
                  />
                </div>
                <div class="flex-1 mt-4 md:mt-0">
                  <label class="block text-sm text-gray-600 mb-1">Max Age</label>
                  <input
                    type="number"
                    v-model="criteriaValues.age.max"
                    class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    min="18"
                    max="100"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Ethnicity -->
          <div class="criteria-item" :class="{ 'opacity-50 cursor-not-allowed': !canSelectMore && !isSelected('ethnicity') }">
            <div class="flex items-center mb-3">
              <input
                type="checkbox"
                id="ethnicity"
                v-model="selectedCriteriaMap.ethnicity"
                :disabled="!canSelectMore && !isSelected('ethnicity')"
                class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                @change="handleCriteriaChange('ethnicity')"
              />
              <label for="ethnicity" class="ml-3 text-lg font-medium text-gray-700">Ethnicity (select up to 4)</label>
            </div>
            <div v-if="isSelected('ethnicity')" class="ml-8 mt-2">
              <div class="grid grid-cols-2 gap-2">
                <label v-for="option in ethnicityOptions" :key="option" class="flex items-center">
                  <input
                    type="checkbox"
                    v-model="criteriaValues.ethnicity"
                    :value="option"
                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    :disabled="criteriaValues.ethnicity.length >= 4 && !criteriaValues.ethnicity.includes(option)"
                  />
                  <span class="ml-2 text-gray-700">{{ option }}</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Children -->
          <div class="criteria-item" :class="{ 'opacity-50 cursor-not-allowed': !canSelectMore && !isSelected('children') }">
            <div class="flex items-center mb-3">
              <input
                type="checkbox"
                id="children"
                v-model="selectedCriteriaMap.children"
                :disabled="!canSelectMore && !isSelected('children')"
                class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                @change="handleCriteriaChange('children')"
              />
              <label for="children" class="ml-3 text-lg font-medium text-gray-700">Children</label>
            </div>
            <div v-if="isSelected('children')" class="ml-8 mt-2">
              <select v-model="criteriaValues.children" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Select preference</option>
                <option value="no">No children</option>
                <option value="yes">Has children</option>
                <option value="doesnt_matter">Doesn't matter</option>
              </select>
            </div>
          </div>

          <!-- Wants Kids -->
          <div class="criteria-item" :class="{ 'opacity-50 cursor-not-allowed': !canSelectMore && !isSelected('wantsKids') }">
            <div class="flex items-center mb-3">
              <input
                type="checkbox"
                id="wantsKids"
                v-model="selectedCriteriaMap.wantsKids"
                :disabled="!canSelectMore && !isSelected('wantsKids')"
                class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                @change="handleCriteriaChange('wantsKids')"
              />
              <label for="wantsKids" class="ml-3 text-lg font-medium text-gray-700">Wants Kids</label>
            </div>
            <div v-if="isSelected('wantsKids')" class="ml-8 mt-2">
              <select v-model="criteriaValues.wantsKids" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Select preference</option>
                <option value="yes">Yes</option>
                <option value="no">No</option>
                <option value="maybe">Maybe</option>
              </select>
            </div>
          </div>

          <!-- Body Type -->
          <div class="criteria-item" :class="{ 'opacity-50 cursor-not-allowed': !canSelectMore && !isSelected('bodyType') }">
            <div class="flex items-center mb-3">
              <input
                type="checkbox"
                id="bodyType"
                v-model="selectedCriteriaMap.bodyType"
                :disabled="!canSelectMore && !isSelected('bodyType')"
                class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                @change="handleCriteriaChange('bodyType')"
              />
              <label for="bodyType" class="ml-3 text-lg font-medium text-gray-700">Body Type</label>
            </div>
            <div v-if="isSelected('bodyType')" class="ml-8 mt-2">
              <select v-model="criteriaValues.bodyType" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Select body type</option>
                <option v-for="type in bodyTypeOptions" :key="type" :value="type">
                  {{ type }}
                </option>
              </select>
            </div>
          </div>

          <!-- Height Range -->
          <div class="criteria-item" :class="{ 'opacity-50 cursor-not-allowed': !canSelectMore && !isSelected('height') }">
            <div class="flex items-center mb-3">
              <input
                type="checkbox"
                id="height"
                v-model="selectedCriteriaMap.height"
                :disabled="!canSelectMore && !isSelected('height')"
                class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                @change="handleCriteriaChange('height')"
              />
              <label for="height" class="ml-3 text-lg font-medium text-gray-700">Height Range</label>
            </div>
            <div v-if="isSelected('height')" class="ml-8 mt-2">
              <div class="flex flex-col md:flex-row md:space-x-4">
                <div class="flex-1">
                  <label class="block text-sm text-gray-600 mb-1">Min Height</label>
                  <select v-model="criteriaValues.height.min" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select height</option>
                    <option v-for="height in heightOptions" :key="height" :value="height">
                      {{ formatHeight(height) }}
                    </option>
                  </select>
                </div>
                <div class="flex-1 mt-4 md:mt-0">
                  <label class="block text-sm text-gray-600 mb-1">Max Height</label>
                  <select v-model="criteriaValues.height.max" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select height</option>
                    <option v-for="height in heightOptions" :key="height" :value="height">
                      {{ formatHeight(height) }}
                    </option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- Religion -->
          <div class="criteria-item" :class="{ 'opacity-50 cursor-not-allowed': !canSelectMore && !isSelected('religion') }">
            <div class="flex items-center mb-3">
              <input
                type="checkbox"
                id="religion"
                v-model="selectedCriteriaMap.religion"
                :disabled="!canSelectMore && !isSelected('religion')"
                class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                @change="handleCriteriaChange('religion')"
              />
              <label for="religion" class="ml-3 text-lg font-medium text-gray-700">Religion (select up to 4)</label>
            </div>
            <div v-if="isSelected('religion')" class="ml-8 mt-2">
              <div class="grid grid-cols-2 gap-2">
                <label v-for="option in religionOptions" :key="option" class="flex items-center">
                  <input
                    type="checkbox"
                    v-model="criteriaValues.religion"
                    :value="option"
                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    :disabled="criteriaValues.religion.length >= 4 && !criteriaValues.religion.includes(option)"
                  />
                  <span class="ml-2 text-gray-700">{{ option }}</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Relationship Status -->
          <div class="criteria-item" :class="{ 'opacity-50 cursor-not-allowed': !canSelectMore && !isSelected('relationshipStatus') }">
            <div class="flex items-center mb-3">
              <input
                type="checkbox"
                id="relationshipStatus"
                v-model="selectedCriteriaMap.relationshipStatus"
                :disabled="!canSelectMore && !isSelected('relationshipStatus')"
                class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                @change="handleCriteriaChange('relationshipStatus')"
              />
              <label for="relationshipStatus" class="ml-3 text-lg font-medium text-gray-700">Relationship Status</label>
            </div>
            <div v-if="isSelected('relationshipStatus')" class="ml-8 mt-2">
              <select v-model="criteriaValues.relationshipStatus" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Select status</option>
                <option value="never_married">Never married</option>
                <option value="divorced">Divorced</option>
                <option value="separated">Separated</option>
              </select>
            </div>
          </div>

          <!-- Location -->
          <div class="criteria-item" :class="{ 'opacity-50 cursor-not-allowed': !canSelectMore && !isSelected('location') }">
            <div class="flex items-center mb-3">
              <input
                type="checkbox"
                id="location"
                v-model="selectedCriteriaMap.location"
                :disabled="!canSelectMore && !isSelected('location')"
                class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                @change="handleCriteriaChange('location')"
              />
              <label for="location" class="ml-3 text-lg font-medium text-gray-700">Location (select up to 4)</label>
            </div>
            <div v-if="isSelected('location')" class="ml-8 mt-2">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div>
                  <label class="block text-sm text-gray-600 mb-1">Country</label>
                  <select v-model="criteriaValues.location.country" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select country</option>
                    <option v-for="country in countryOptions" :key="country" :value="country">
                      {{ country }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm text-gray-600 mb-1">State</label>
                  <input
                    type="text"
                    v-model="criteriaValues.location.state"
                    class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Enter state"
                  />
                </div>
                <div class="md:col-span-2">
                  <label class="block text-sm text-gray-600 mb-1">City</label>
                  <input
                    type="text"
                    v-model="criteriaValues.location.city"
                    class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Enter city"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Income -->
          <div class="criteria-item" :class="{ 'opacity-50 cursor-not-allowed': !canSelectMore && !isSelected('income') }">
            <div class="flex items-center mb-3">
              <input
                type="checkbox"
                id="income"
                v-model="selectedCriteriaMap.income"
                :disabled="!canSelectMore && !isSelected('income')"
                class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                @change="handleCriteriaChange('income')"
              />
              <label for="income" class="ml-3 text-lg font-medium text-gray-700">Income Range</label>
            </div>
            <div v-if="isSelected('income')" class="ml-8 mt-2">
              <div class="flex flex-col md:flex-row md:space-x-4">
                <div class="flex-1">
                  <label class="block text-sm text-gray-600 mb-1">Min Income ($)</label>
                  <input
                    type="number"
                    v-model="criteriaValues.income.min"
                    class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    min="10000"
                    max="250000"
                    step="10000"
                  />
                </div>
                <div class="flex-1 mt-4 md:mt-0">
                  <label class="block text-sm text-gray-600 mb-1">Max Income ($)</label>
                  <input
                    type="number"
                    v-model="criteriaValues.income.max"
                    class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    min="10000"
                    max="250000"
                    step="10000"
                  />
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Save Button -->
        <div class="mt-8">
          <button 
            type="submit"
            :disabled="!isValid || saving"
            class="w-full py-3 px-6 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200 disabled:bg-blue-300"
          >
            {{ saving ? 'Saving...' : 'Save Criteria' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import axios from 'axios';

export default {
  name: 'CriteriaManagement',
  data() {
    return {
      saving: false,
      selectedCriteriaMap: {
        age: false,
        ethnicity: false,
        children: false,
        wantsKids: false,
        bodyType: false,
        height: false,
        religion: false,
        relationshipStatus: false,
        location: false,
        income: false
      },
      criteriaValues: {
        age: { min: 18, max: 65 },
        ethnicity: [],
        children: '',
        wantsKids: '',
        bodyType: '',
        height: { min: '60', max: '78' }, // 5'0" to 6'6"
        religion: [],
        relationshipStatus: '',
        location: { country: '', state: '', city: '' },
        income: { min: 10000, max: 250000 }
      },
      ethnicityOptions: [
        'Asian', 'Black/African', 'Caucasian', 'Hispanic/Latino',
        'Indian', 'Middle Eastern', 'Native American', 'Pacific Islander',
        'Mixed', 'Other'
      ],
      bodyTypeOptions: [
        'Slim', 'Athletic', 'Average', 'Curvy', 'Muscular',
        'A few extra pounds', 'Big and Beautiful'
      ],
      heightOptions: Array.from({ length: 37 }, (_, i) => (60 + i).toString()), // 5'0" (60") to 8'0" (96")
      religionOptions: [
        'Christianity', 'Islam', 'Hinduism', 'Buddhism', 'Judaism',
        'Sikhism', 'Atheism', 'Agnosticism', 'Other'
      ],
      countryOptions: [
        'United States', 'Canada', 'United Kingdom', 'Australia', 'Germany',
        'France', 'India', 'China', 'Brazil', 'Other'
      ]
    };
  },
  computed: {
    ...mapState({
      user: state => state.auth.user,
      authorization: state => state.auth.authorization,
    }),
    selectedCriteria() {
      return Object.entries(this.selectedCriteriaMap)
        .filter(([_, selected]) => selected)
        .map(([key]) => key);
    },
    canSelectMore() {
      return this.selectedCriteria.length < (this.user?.criteria_limit || 0);
    },
    isValid() {
      return this.selectedCriteria.length > 0 && 
             this.selectedCriteria.length <= (this.user?.criteria_limit || 0);
    }
  },
  methods: {
    formatPackageName(package_name) {
      return package_name.charAt(0).toUpperCase() + package_name.slice(1);
    },
    isSelected(criteria) {
      return this.selectedCriteriaMap[criteria];
    },
    handleCriteriaChange(criteria) {
      if (this.selectedCriteriaMap[criteria] && !this.canSelectMore) {
        this.selectedCriteriaMap[criteria] = false;
        alert(`You can only select up to ${this.user.criteria_limit} criteria with your current package.`);
      }
    },
    formatHeight(inches) {
      const feet = Math.floor(inches / 12);
      const remainingInches = inches % 12;
      return `${feet}'${remainingInches}"`;
    },
    async saveCriteria() {
      if (!this.isValid) return;

      this.saving = true;
      const criteriaData = {};

      this.selectedCriteria.forEach(criteria => {
        criteriaData[criteria] = this.criteriaValues[criteria];
      });

      try {
        const response = await axios.post('/api/user/criteria', {
          criteria: criteriaData
        }, {
          headers: {
            Authorization: `Bearer ${this.authorization.token}`
          }
        });

        if (response.data.success) {
          alert('Criteria saved successfully!');
          // Optionally redirect to another page or update UI
        }
      } catch (error) {
        console.error('Error saving criteria:', error);
        alert('Failed to save criteria. Please try again.');
      } finally {
        this.saving = false;
      }
    }
  },
  watch: {
    user: {
      immediate: true,
      handler(newUser) {
        if (!newUser?.purchased_package) {
          this.$router.push('/client/billing');
        }
      }
    }
  },
  async created() {
    // Load existing criteria if any
    if (this.user?.criteria) {
      try {
        const existingCriteria = JSON.parse(this.user.criteria);
        Object.keys(existingCriteria).forEach(key => {
          if (this.selectedCriteriaMap.hasOwnProperty(key)) {
            this.selectedCriteriaMap[key] = true;
            this.criteriaValues[key] = existingCriteria[key];
          }
        });
      } catch (e) {
        console.error('Error parsing existing criteria:', e);
      }
    }
  }
};
</script>

<style scoped>
.criteria-item {
  transition: opacity 0.3s ease-in-out;
}
</style>
