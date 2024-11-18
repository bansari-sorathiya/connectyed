<template>
  <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8 flex items-center justify-center">        
      <div class="w-full max-w-sm mt-20 ">
          <div class="reset-password">            
            <div class="font-bold text-xl mb-2">Reset Password</div>
            <form @submit.prevent="resetPassword" class="bg-connectyed-card-light shadow-md rounded px-8 pt-6 pb-8 mb-4 border-solid border-2 border-gray-200">
              <div class="form-group">
                <label for="email">Email</label>
                <input
                  v-model="email"
                  type="email"
                  id="email"
                  required
                  class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-4"
                />
              </div>
              <div class="form-group">
                <label for="password">New Password</label>
                <input
                  v-model="password"
                  type="password"
                  id="password"
                  required
                  class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-4"
                />
              </div>
              <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input
                  v-model="password_confirmation"
                  type="password"
                  id="password_confirmation"
                  required
                  class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-4"
                />
              </div>
              <button type="submit" class="bg-connectyed-button-light text-connectyed-button-dark hover:bg-connectyed-button-hover-light hover:text-connectyed-button-light w-full h-12 transition-colors duration-150 focus:shadow-outline font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline max-w-100">Reset Password</button>
            </form>
            <p v-if="successMessage" class="text-green-500">{{ successMessage }}</p>
            <p v-if="errorMessage" class="text-red-500">{{ errorMessage }}</p>
          </div>
      </div>
    </div>
</template>
  
  <script>
  export default {
    data() {
      return {
        email: '',
        password: '',
        password_confirmation: '',
        token: this.$route.query.token, // Get token from URL query
        successMessage: '',
        errorMessage: ''
      };
    },
    methods: {
      async resetPassword() {
        try {
          const response = await axios.post('/api/password/reset', {
            email: this.email,
            password: this.password,
            password_confirmation: this.password_confirmation,
            token: this.token
          });
          this.successMessage = 'Password has been reset successfully.';
        } catch (error) {
          this.errorMessage = 'Failed to reset password. Please try again.';
        }
      },
    },
  };
  </script>
  
  <style scoped>
  /* Add custom styles if needed */
  </style>
  