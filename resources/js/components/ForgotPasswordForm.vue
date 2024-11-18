<template>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8 flex items-center justify-center">        
        <div class="w-full max-w-sm mt-20 ">
            <div class="forgot-password">
            <div class="font-bold text-xl mb-2">Forgot Password</div>            
            <form @submit.prevent="submitEmail" class="bg-connectyed-card-light shadow-md rounded px-8 pt-6 pb-8 mb-4 border-solid border-2 border-gray-200">
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
                <button type="submit" class="bg-connectyed-button-light text-connectyed-button-dark hover:bg-connectyed-button-hover-light hover:text-connectyed-button-light w-full h-12 transition-colors duration-150 focus:shadow-outline font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline max-w-100">
                    {{ processing ? "Please wait" : "Send Reset Link" }}        
                    <span class="float-right" v-if="processing"><img class="h-5 ml-3 mt-1" src="assets/images/icons/process.gif"></span>            
                </button>
                
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
        successMessage: '',
        errorMessage: '',
        processing:false
      };
    },
    methods: {
        async submitEmail() {
            this.processing = true
            try {
            const response = await axios.post('/api/password/email', {
                email: this.email,
            });
                this.successMessage = 'Reset link has been sent to your email.';
                this.processing = false
                setTimeout(() => {
                    this.$router.push({ name: "login" });
                }, 500);
            } catch (error) {
                this.errorMessage = 'Failed to send reset link. Please check your email.';
                this.processing = false
            }
      },
    },
  };
  </script>
  
  <style scoped>
  /* Add custom styles if needed */
  </style>
  