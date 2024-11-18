<template>  
  <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8 items-center justify-center">
      <div class="mx-full shadow-connectyed rounded-xl bg-connectyed-card-light flex flex-col py-5 px-5 mb-5"> 
        <div class="card-body text-center">    
          <div v-if="loading" class="text-lg">
            <p>Verifying your email...</p>
          </div>
          <div v-else>
            <p :class="{'text-red-600': error, 'text-green-600': !error}">
              {{ message }}
            </p>
          </div>
        </div>
      </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  props: ['verificationUrl'],
  data() {
    return {
      loading: true,
      message: '',
      error: false
    };
  },
  mounted() {
    if (this.verificationUrl) {
      this.verifyEmail();
    } else {
      this.error = true;
      this.message = 'Invalid verification link.';
      this.loading = false;
    }
  },
  methods: {
    async verifyEmail() {
      try {
        const response = await axios.get(this.verificationUrl);
        
        if (response.data.status === 'success') {
          this.message = response.data.message;
          this.error = false;
          
          // Redirect to login after successful verification
          setTimeout(() => {
            this.$router.push({ name: 'login' });
          }, 2000);
        } else {
          throw new Error(response.data.message || 'Verification failed');
        }
      } catch (error) {
        this.error = true;
        this.message = error.response?.data?.message || 'Failed to verify email. Please try again.';
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>