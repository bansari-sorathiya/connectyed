<template>
  <div class="container mx-auto p-4">
    <!-- Debug Info - Remove in production -->
    <!-- <pre class="mb-4 p-2 bg-gray-100 rounded">{{ user }}</pre> -->
    
    <!-- Current Package Status -->
    <div v-if="hasPackage" class="mb-8 bg-white p-6 rounded-lg shadow">
      <h2 class="text-xl font-bold mb-4">Current Package</h2>
      <div class="flex justify-between items-center">
        <div>
          <p class="text-lg">
            {{ formatPackageName(user.purchased_package) }} Package
            <span class="text-sm text-gray-600 ml-2">
              (Purchased {{ formatDate(user.package_purchased_at) }})
            </span>
          </p>
          <p class="text-gray-600 mt-2">
            Criteria limit: {{ user.criteria_limit }}
            <span v-if="userCriteriaCount !== null" class="ml-2">
              ({{ userCriteriaCount }} used)
            </span>
          </p>
        </div>
        <router-link 
          to="/client/criteria" 
          class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition duration-200"
          aria-label="Manage Criteria"
        >
          Manage Criteria
        </router-link>
      </div>
    </div>

    <!-- New Match Criteria Section -->
    <div v-if="hasPackage" class="mb-8 bg-white p-6 rounded-lg shadow">
      <div class="flex justify-between items-center">
        <div>
          <h2 class="text-xl font-bold">Match Criteria</h2>
          <p class="text-gray-600 mt-2">
            Set your match preferences to find your perfect match!
            <span v-if="userCriteriaCount > 0" class="ml-2">
              ({{ userCriteriaCount }}/{{ user.criteria_limit }} criteria set)
            </span>
          </p>
        </div>
        <router-link 
          to="/client/criteria" 
          class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-200 flex items-center space-x-2"
          aria-label="Set or Update Match Criteria"
        >
          <span v-if="userCriteriaCount > 0">Update Criteria</span>
          <span v-else>Set Your Criteria</span>
        </router-link>
      </div>
    </div>

    <!-- Package Options Header -->
    <div class="mb-6">
      <h2 class="text-xl font-bold mb-2">
        {{ hasPackage ? 'Upgrade Your Package' : 'Dating Packages' }}fmatch
      </h2>
      <p v-if="hasPackage" class="text-gray-600">
        Upgrade to get access to more criteria and better matches!
      </p>
      <p v-else class="text-gray-600">
        Choose a package to start your dating journey!
      </p>
    </div>

    <!-- PricingComparison Component -->
    <PricingComparison />

    <!-- Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6 mt-6">
      <div 
        v-for="product in availableProducts" 
        :key="product.id" 
        class="bg-white p-6 rounded-lg shadow relative"
      >
        <div v-if="isCurrentPackage(product.id)" 
             class="absolute top-0 right-0 bg-blue-500 text-white px-3 py-1 rounded-bl">
          Current
        </div>
        
        <h3 class="text-lg font-semibold mb-2">{{ product.name }}</h3>
        <p class="text-gray-700 mb-4">{{ product.description }}</p>
        <p class="text-xl font-bold mb-2">
          <span class="line-through text-gray-500">
            ${{ (product.originalPrice / 100).toFixed(2) }}
          </span>
          <span class="text-green-600">
            ${{ (product.discountedPrice / 100).toFixed(2) }}
          </span>
        </p>
        <button
          @click="openDiscountModal(product)"
          :disabled="!canPurchasePackage(product.id) || processing"
          :class="[
            'w-full py-2 px-4 rounded transition duration-200',
            isCurrentPackage(product.id) 
              ? 'bg-gray-300 cursor-not-allowed'
              : 'bg-blue-500 hover:bg-blue-600 text-white'
          ]"
          :aria-label="buttonLabel(product)"
        >
          <span v-if="processing && selectedProduct?.id === product.id">
            Processing...
          </span>
          <span v-else>
            {{ getButtonText(product.id) }}
          </span>
        </button>
      </div>
    </div>

    <!-- Discount Code Modal -->
    <div 
      v-if="showDiscountModal" 
      class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
      aria-modal="true"
      role="dialog"
      aria-labelledby="discount-code-modal-title"
    >
      <div class="bg-white p-6 rounded-lg shadow-lg w-80">
        <h3 id="discount-code-modal-title" class="text-lg font-semibold mb-4">Enter Discount Code</h3>
        <input
          v-model="discountCode"
          type="text"
          placeholder="Discount Code"
          class="w-full px-3 py-2 border rounded mb-4"
          aria-label="Discount Code"
        />
        <div class="flex justify-end">
          <button
            @click="closeDiscountModal"
            class="mr-2 px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400"
            aria-label="Cancel Discount Code Entry"
          >
            Cancel
          </button>
          <button
            @click="confirmPurchase"
            :disabled="processing"
            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-200 disabled:bg-blue-300"
            aria-label="Confirm Discount Code"
          >
            <span v-if="processing">Processing...</span>
            <span v-else>Confirm</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { mapState } from 'vuex';
import PricingComparison from './PricingComparison.vue';

export default {
  name: "Billing",
  components: {
    PricingComparison,
  },
data() {
  return {
    products: [
      {
        id: 'matchmaker_intro',
        name: 'Matchmaker Intro',
        description: '1 on 1 Connectyed Team Call',
        originalPrice: 5000,
        discountedPrice: 2500,
        stripePriceId: 'price_1Q6dwmFZef913bMWLjDn3QTZ',
        rank: 0
      },
      {
        id: 'silver_package',
        name: 'Silver Package',
        description: '1 on 1 Blind Date - Choose up to 4 criteria',
        originalPrice: 9900,
        discountedPrice: 5000,
        stripePriceId: 'price_1Q6dwmFZef913bMWLjDn3QTZ',
        rank: 1
      },
      {
        id: 'gold_package',
        name: 'Gold Package',
        description: '1 on 1 Curated Date - Includes Matchmaker Call',
        originalPrice: 24900,
        discountedPrice: 14900,
        stripePriceId: 'price_1QEIiBFZef913bMWY56u5lcS',
        rank: 2
      },
      {
        id: 'platinum_package',
        name: 'Platinum Package',
        description: '1 on 1 Premium Date - Includes Matchmaker Call',
        originalPrice: 49900,
        discountedPrice: 24900,
        stripePriceId: 'price_1QEIjGFZef913bMWMV75AZ1n',
        rank: 3
      },
    ],
    processing: false,
    showDiscountModal: false,
    selectedProduct: null,
    discountCode: ''
  };
},
  computed: {
    ...mapState({
      user: state => state.auth.user,  // Updated to reflect nested user structure
      authorization: state => state.auth.authorization,
    }),
    hasPackage() {
      return !!this.user?.purchased_package;
    },
    userCriteriaCount() {
      if (!this.user?.criteria) return 0;
      try {
        return JSON.parse(this.user.criteria).length;
      } catch (e) {
        console.error('Error parsing criteria:', e);
        return 0;
      }
    },
    availableProducts() {
      if (!this.hasPackage) {
        return this.products;
      }
      
      const currentRank = this.getPackageRank(this.user.purchased_package);
      return this.products.filter(product => 
        this.getPackageRank(product.id) >= currentRank
      );
    }
  },
  methods: {
    formatPackageName(package_name) {
      if (!package_name) return '';
      return package_name.charAt(0).toUpperCase() + package_name.slice(1);
    },
    formatDate(date) {
      if (!date) return '';
      return new Date(date).toLocaleDateString();
    },
    getPackageRank(package_name) {
      if (!package_name) return 0;
      const packageProduct = this.products.find(p => 
        p.id === package_name || p.id === `${package_name}_package`
      );
      return packageProduct ? packageProduct.rank : 0;
    },
    canPurchasePackage(package_id) {
      if (!this.hasPackage) return true;
      if (this.isCurrentPackage(package_id)) return false;
      
      const currentRank = this.getPackageRank(this.user.purchased_package);
      const targetRank = this.getPackageRank(package_id);
      return targetRank > currentRank;
    },
    isCurrentPackage(package_id) {
      if (!this.hasPackage) return false;
      const packageName = package_id.replace('_package', '');
      return this.user.purchased_package === packageName;
    },
    getButtonText(package_id) {
      if (this.isCurrentPackage(package_id)) {
        return 'Current Package';
      }
      if (!this.canPurchasePackage(package_id)) {
        return 'Lower Tier';
      }
      return this.hasPackage ? 'Upgrade' : 'Purchase';
    },
    buttonLabel(product) {
      return `${this.hasPackage ? 'Upgrade to' : 'Purchase'} ${product.name}`;
    },
    openDiscountModal(product) {
      this.selectedProduct = product;
      this.showDiscountModal = true;
    },
    closeDiscountModal() {
      this.showDiscountModal = false;
      this.selectedProduct = null;
      this.discountCode = '';
    },
    /**
     * Updated confirmPurchase method as per user request.
     */
    async confirmPurchase() {
      if (!this.selectedProduct) return;

      this.processing = true;
      try {
        const payload = {
          product_type: this.selectedProduct.id,
          price_id: this.selectedProduct.stripePriceId,
          discount_code: this.discountCode || null,
        };

        const response = await axios.post(
          '/api/billing/create-checkout-session',
          payload,
          {
            headers: {
              Authorization: `Bearer ${this.authorization.token}`,
            },
          }
        );

        if (response.data.success && response.data.checkout_url) {
          // Store the first purchase status in localStorage
          if (response.data.is_first_purchase) {
            localStorage.setItem('redirect_after_payment', '/client/criteria');
          }
          // Redirect to Stripe Checkout
          window.location.href = response.data.checkout_url;
        } else {
          console.error('Failed to create checkout session:', response.data.message);
          alert(response.data.message || 'Failed to create checkout session. Please try again.');
        }
      } catch (error) {
        console.error('Error creating checkout session:', error);
        if (error.response?.data?.message) {
          alert(`Error: ${error.response.data.message}`);
        } else {
          alert('An unexpected error occurred. Please try again later.');
        }
      } finally {
        this.processing = false;
        this.closeDiscountModal();
      }
    },
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
  created() {
    // Check if we're returning from a successful payment
    const urlParams = new URLSearchParams(window.location.search);
    const sessionId = urlParams.get('session_id');
    
    if (sessionId) {
      // Get the stored redirect path
      const redirectPath = localStorage.getItem('redirect_after_payment');
      if (redirectPath) {
        localStorage.removeItem('redirect_after_payment');
        // Force refresh user data before redirect
        this.$store.dispatch('auth/getUser').then(() => {
          this.$router.push(redirectPath);
        }).catch((error) => {
          console.error('Error refreshing user data after payment:', error);
          // Optionally, redirect to billing or show an error message
          this.$router.push('/client/billing');
        });
      }
    }
  }
};
</script>

<style scoped>
.line-through {
  text-decoration: line-through;
}
</style>
