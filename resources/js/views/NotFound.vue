<template>
    <div class="min-h-screen flex flex-col justify-center items-center bg-gray-100">
      <h1 class="text-6xl font-bold text-gray-800 mb-4">404</h1>
      <p class="text-xl text-gray-600 mb-6">Oops! The page you're looking for doesn't exist.</p>
      <router-link to="/" class="bg-indigo-600 text-white px-6 py-3 rounded-lg text-lg font-semibold hover:bg-indigo-500 transition duration-300">
        Go Back Home
      </router-link>
    </div>
  </template>
  
  <script>
  export default {
    name: "NotFound",
    mounted() {      
        setTimeout(() => {
            this.$router.push({ name: "home" });
        }, 3000);
    },
  };
  </script>
  
  <style scoped>
  /* Add custom styles here if needed */
  </style>
  