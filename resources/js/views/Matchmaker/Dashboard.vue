<template>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8 items-center justify-center">
        <div class="mx-full shadow-connectyed rounded-xl bg-connectyed-card-mm-light flex flex-col mb-5">             
            <div class="flex rounded-xl">
                <!-- Sidebar -->
                <aside class="w-72 bg-connectyed-sidenav-light text-connectyed-icon rounded-l-xl">
                    <nav class="mt-10">
                    <ul>
                        <li v-for="item in menuItems" :key="item.title" class="mb-1">
                        <router-link :to="'/matchmaker' + item.link"                            
                            class="flex items-center p-4 text-gray-300 hover:hover:bg-connectyed-card-mm-light hover:text-connectyed-textnav-dark transition-colors duration-200"
                            active-class="bg-connectyed-card-mm-light text-gray-900"
                        >
                            <font-awesome-icon :icon="item.icon" class="w-5 h-5 mr-4" />
                            <span>{{ item.title }}</span>
                        </router-link>
                        </li>
                    </ul>
                    </nav>
                </aside>
            
                <!-- Main Content -->
                <div class="flex-1 p-6">
                    <router-view />
                </div>
            </div>            
        </div>
    </div>   
</template>
  
<script>
import { faTachometerAlt, faUser, faBriefcase, faUsers, faDollarSign, faEnvelope, faTasks, faCalendar } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

export default {
    name:"matchmaker",
    components: {
        FontAwesomeIcon
    },
    data() {
        return {
        menuItems: [
            { title: "Dashboard", icon: faTachometerAlt, link: "/dashboard" },        
            { title: "My Profile", icon: faUser, link: "/profile" },
            { title: "My Specialties", icon: faBriefcase, link: "/specialties" },
            { title: "My Availability", icon: faCalendar, link: "/availability" },
            { title: "Clients", icon: faUsers, link: "/clients" },
            { title: "Billing and Subscription", icon: faDollarSign, link: "/billing" },
            { title: "Communication Tools", icon: faEnvelope, link: "/communication" },
            { title: "Match Management", icon: faTasks, link: "/match-management" },
        ]
        };
    }
};
</script>