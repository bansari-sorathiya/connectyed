<template>
  <div>
    <!-- Notification Message -->
    <div class="bg-yellow-300 text-yellow-900 p-4 rounded-lg shadow-lg mb-4 flex items-center">
      <font-awesome-icon icon="fa-solid fa-heart" class="w-6 h-6 mr-3" />
      <p class="flex-1 text-xl tracking-wide">
        Click <router-link to="/client/billing" class="font-bold text-lg text-blue-600 hover:underline">here</router-link> to
        request a FREE 1 on 1 Blind Date !
      </p>
    </div>
    <h2 class="text-xl font-bold mb-4">OVerview</h2>
    <!-- Insert Dashboard Overview content here -->
    <p>OVerview!</p>
  </div>
</template>
  
  <script>
  export default {
    name: "overview",
  };
  </script>
  