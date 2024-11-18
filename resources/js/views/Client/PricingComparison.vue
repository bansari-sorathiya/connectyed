<template>
  <div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Compare Packages</h2>
    <table class="min-w-full bg-white">
      <thead>
        <tr>
          <th class="py-2">Feature</th>
          <th class="py-2">Silver</th>
          <th class="py-2">Gold</th>
          <th class="py-2">Platinum</th>
        </tr>
      </thead>
      <tbody>
        <!-- Feature Row with Dropdown Explanation -->
        <tr v-for="feature in features" :key="feature.name">
          <td class="border px-4 py-2">
            <div class="flex items-center">
              <span>{{ feature.displayName }}</span>
              <button @click="toggleExplanation(feature.name)" class="ml-2 focus:outline-none">
                <svg
                  v-if="!feature.showExplanation"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
                <svg
                  v-else
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                </svg>
              </button>
            </div>
            <!-- Explanation Dropdown -->
            <div v-if="feature.showExplanation" class="mt-2 text-sm text-gray-600">
              {{ feature.explanation }}
            </div>
          </td>
          <td class="border px-4 py-2 text-center">
            <!-- Display feature value or checkmark -->
            <span v-if="typeof feature.silver === 'boolean'">
              {{ feature.silver ? '✔️' : '❌' }}
            </span>
            <span v-else>
              {{ feature.silver }}
            </span>
          </td>
          <td class="border px-4 py-2 text-center">
            <span v-if="typeof feature.gold === 'boolean'">
              {{ feature.gold ? '✔️' : '❌' }}
            </span>
            <span v-else>
              {{ feature.gold }}
            </span>
          </td>
          <td class="border px-4 py-2 text-center">
            <span v-if="typeof feature.platinum === 'boolean'">
              {{ feature.platinum ? '✔️' : '❌' }}
            </span>
            <span v-else>
              {{ feature.platinum }}
            </span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: "PricingComparison",
  data() {
    return {
      features: [
        {
          name: 'one_on_one_blind_date',
          displayName: '1 on 1 Blind Date',
          explanation: 'A personalized blind date setup based on your selected criteria.',
          silver: true,
          gold: true,
          platinum: true,
        },
        {
          name: 'matchmaker_call_included',
          displayName: 'Matchmaker Call Included',
          explanation: 'Includes a 1 on 1 call with your matchmaker to discuss preferences.',
          silver: false,
          gold: true,
          platinum: true,
        },
        {
          name: 'choose_criteria',
          displayName: 'Choose Criteria',
          explanation: 'Select the criteria you desire in a match.',
          silver: 'Up to 4',
          gold: 'Up to 6',
          platinum: 'Up to 9',
        },
        {
          name: 'pick_matches',
          displayName: 'Pick 1 of 2 Matches',
          explanation: 'Choose 1 out of 2 curated matches provided to you.',
          silver: false,
          gold: false,
          platinum: true,
        },
        {
          name: 'match_screening',
          displayName: 'Match Screening',
          explanation: 'Matchmaker conducts a screening call with your match.',
          silver: false,
          gold: true,
          platinum: true,
        },
        {
          name: 'match_verification',
          displayName: 'Match Verification',
          explanation: 'Matchmaker verifies your match details for compatibility.',
          silver: false,
          gold: false,
          platinum: true,
        },
        {
          name: 'delivery_time',
          displayName: 'Delivery Time',
          explanation: 'The time it takes to provide you with a match.',
          silver: '10 days',
          gold: '8 days',
          platinum: '6 days',
        },
        // Add more features as needed
      ],
    };
  },
  methods: {
    toggleExplanation(featureName) {
      const feature = this.features.find(f => f.name === featureName);
      if (feature) {
        feature.showExplanation = !feature.showExplanation;
      }
    },
  },
};
</script>

<style scoped>
table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  border: 1px solid #dddddd;
  text-align: center;
  padding: 8px;
}
th {
  background-color: #f2f2f2;
}
button {
  background: none;
}
</style>
