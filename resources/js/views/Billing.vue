<template>
  <div class="container mx-auto p-4">
    <!-- Package Options Header -->
    <div class="mb-6">
      <h2 class="text-xl font-bold mb-2">Dating Packages</h2>
      <p class="text-gray-600">Choose a package to start your dating journey!</p>
    </div>

    <!-- PricingComparison Component -->
    <PricingComparison />
    
    <!-- Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6 mt-6">
      <div 
        v-for="product in products" 
        :key="product.id" 
        class="bg-white p-6 rounded-lg shadow relative"
      >
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
          @click="redirectToRegistration"
          :class="[
            'w-full py-2 px-4 rounded transition duration-200',
            'bg-blue-500 hover:bg-blue-600 text-white'
          ]"
        >
          Subscribe to Purchase
        </button>
      </div>
    </div>

    <!-- Discount Modal (optional) -->
    <!-- You can add a modal component here to handle the purchase flow -->
  </div>
</template>

<script>
import PricingComparison from './Client/PricingComparison.vue';
import axios from 'axios'; // Ensure axios is imported for HTTP requests

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
          stripePriceId: 'price_1Q6dwmFZef913bMWLjDn3QTZ', // Verify if this should be unique
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
        }
      ]
    };
  },
  methods: {
    /**
     * Handle the purchase action for a selected product.
     * @param {Object} product - The product being purchased.
     */
    openDiscountModal(product) {
      // Implement your purchase logic here.
      // For example, redirect to a Stripe Checkout session or open a modal.
      console.log("Purchase initiated for", product.name);
      
      // Example: Redirect to registration and pass the product ID
      this.$router.push({ name: 'Register', query: { productId: product.id } });
      
      // Alternatively, integrate with a payment gateway like Stripe
      /*
      axios.post('/api/create-checkout-session', {
        priceId: product.stripePriceId,
      })
      .then(response => {
        const sessionId = response.data.sessionId;
        const stripe = Stripe('your-publishable-key');
        return stripe.redirectToCheckout({ sessionId });
      })
      .then(result => {
        if (result.error) {
          // Handle error
          console.error(result.error.message);
        }
      })
      .catch(error => {
        console.error("Error initiating checkout:", error);
      });
      */
    },
    /**
     * Redirect to the registration page.
     */
    redirectToRegistration() {
      window.scrollTo(0, 0);
      this.$router.push('/register');
    },
  },
};
</script>

<style scoped>
.line-through {
  text-decoration: line-through;
}
</style>
