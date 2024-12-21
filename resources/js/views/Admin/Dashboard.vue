<template>
  <div class="flex min-h-screen bg-gray-100">

    <!-- Left Sidebar Navigation Menu -->
    <aside class="w-64 bg-[#213366] text-[#e7dccf] flex flex-col fixed md:relative">
      <!-- Platform Title Section -->
      <div class="p-6 bg-[#333333] text-white text-xl font-bold">
        Connectyed.com
      </div>

      <!-- Navigation Links -->
      <nav class="mt-6 flex-1">
        <ul>
          <!-- Dashboard -->
          <li class="mb-4">
            <router-link to="/admin/dashboard" class="block px-4 py-3 text-white hover:bg-[#e7dccf] hover:text-[#213366] transition-colors duration-200">
              <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
            </router-link>
          </li>

          <!-- Manage Matchmakers -->
          <li class="mb-4">
            <router-link to="/admin/matchmakers" class="block px-4 py-3 text-white  hover:bg-[#e7dccf] hover:text-[#213366] transition-colors duration-200">
              <i class="fas fa-user-friends mr-2"></i> Matchmakers
            </router-link>
          </li>

          <!-- Manage Clients -->
          <li class="mb-4">
            <router-link to="/admin/clients" class="block px-4 py-3 text-white hover:text-[#213366] hover:bg-[#e7dccf] transition-colors duration-200">
              <i class="fas fa-users mr-2"></i> Clients
            </router-link>
          </li>     

          <!-- Blind Requests -->
          <li class="mb-4">
            <router-link to="/admin/blind_requests" class="block px-4 py-3 text-white hover:text-[#213366] hover:bg-[#e7dccf] transition-colors duration-200">
              <i class="fas fa-users mr-2"></i> Free Bliand Date Request
            </router-link>
          </li>          
        </ul>
      </nav>

    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 p-8 bg-[#e7dccf]">      
      <!-- Render specific content here based on the selected route -->
      <router-view />
    </main>

  </div>
</template>

<script>
export default {
  data() {
    return {
      profile: {
        name: 'Admin',
        avatar: 'https://via.placeholder.com/150', // Admin avatar or user profile
      },
    };
  }
};
</script>

<style scoped>
/* Optional additional custom styles */
</style>
