<template>
    <!-- ================> header section start here <================== -->
    <header v-if="!isHomepage" class="header" id="navbar">
        <div class="header__top d-none d-lg-block bg-connectyed-header-light">
            <div class="container">
                <div class="header__top--area">
                    <!--<div class="header__top--left p-4">-->
                    <!--     You can add content here if needed -->
                    <!--</div>-->
                    <!--<div class="header__top--right">-->
                    <!--     You can add content here if needed -->
                    <!--</div>-->
                </div>
            </div>
        </div>
        <div class="header__bottom">
            <div class="container">
                <nav class="navbar navbar-expand-lg">
                    <a class="navbar-brand" href="#">
                        <h2>
                            <router-link :to="{name:'home'}" class="nav-link">
                                <img src="assets/images/logo/connectyedlogo.png" alt="Connectyed Logo" class="w-[70px]">
                            </router-link>
                        </h2>
                    </a>
                    <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler--icon"></span>
                    </button>
                    <div class="navbar-collapse justify-content-end" id="navbarNavAltMarkup">
                        <div class="navbar-nav mainmenu">
                            <ul>
                                <li>
                                    <router-link :to="{name:'home'}" class="nav-link">Home</router-link>
                                </li>
                                 <!--Add more navigation links here if needed -->
                            </ul>
                        </div>
                        <div class="header__more">
                            <button class="btn" type="button" v-if="currentRouteName !== 'register'">
                                <router-link :to="{name:'register'}" class="bg-connectyed-button-light text-connectyed-button-dark hover:bg-connectyed-button-hover-light hover:text-connectyed-button-light py-4 px-5">
                                    Register for free
                                </router-link>
                            </button>
                            <button class="btn" type="button" v-else-if="currentRouteName === 'register' || !user">
                                <router-link :to="{name:'login'}" class="bg-connectyed-button-light text-connectyed-button-dark hover:bg-connectyed-button-hover-light hover:text-connectyed-button-light py-4 px-5">
                                    Login
                                </router-link>
                            </button>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </header>
    <!-- ================> header section end here <================== -->

    <main>
        <router-view></router-view>
    </main>
<!--Footer section begins -->
<footer class="footer footer--style2">
    <div class="footer__top bg_img">
        <div class="footer__newsletter wow fadeInUp" data-wow-duration="1.5s">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-12">
                        <div class="footer__newsletter--area text-center">
                            <div class="footer__newsletter--title">
                                <h4>Newsletter Sign up</h4>
                            </div>
                            <div class="footer__newsletter--form mx-auto" style="max-width: 500px;">
                                <form @submit.prevent="subscribeNewsletter">
                                    <input
                                        type="email"
                                        v-model="newsletterEmail"
                                        placeholder="Your email address"
                                        :class="{'border-red-500': newsletterError}"
                                        required
                                    />
                                    <button type="submit" class="bg-connectyed-button-light text-connectyed-button-dark hover:bg-connectyed-button-hover-light hover:text-connectyed-button-light py-3 px-4 right-1 top-1 absolute">
                                        <span>Subscribe</span>
                                    </button>
                                </form>
                                <!-- Success and Error Messages -->
                                <div v-if="newsletterSuccess" class="text-green-500 mt-2">{{ newsletterSuccess }}</div>
                                <div v-if="newsletterError" class="text-red-500 mt-2">{{ newsletterError }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer__toparea padding-top wow fadeInUp" data-wow-duration="1.5s">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-12">
                        <div class="footer__item footer--about text-center">
                            <div class="footer__inner">
                                <div class="footer__content">
                                    <div class="footer__content--title">
                                        <h4>About Connectyed</h4>
                                    </div>
                                    <div class="footer__content--desc">
                                        <p>
                                          At Connectyed, we understand the challenges of finding meaningful connections in a busy world. Our mission is to make your search for the perfect match as effortless and rewarding as possible.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer__bottom wow fadeInUp" data-wow-duration="1.5s">
        <div class="container">
            <div class="footer__content text-center">
                <p class="mb-0">
                    All Rights Reserved &copy; <a href="index.html">Connectyed</a> || Design By: Connectyed
                </p>
            </div>
        </div>
    </div>
</footer>
</template>

<script setup>
import { reactive, computed, ref } from 'vue'
import { Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { Bars3Icon, BellIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useStore } from 'vuex'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios' // Import axios for HTTP requests

const store = useStore();
const router = useRouter();
const route = useRoute();

const currentRouteName = computed(() => route.name)
const isHomepage = computed(() => route.path === '/'); 
const auth = reactive(store.state.auth)
const user = auth.user

// Newsletter Subscription States
const newsletterEmail = ref('');
const newsletterSuccess = ref('');
const newsletterError = ref('');

const subscribeNewsletter = async () => {
    newsletterSuccess.value = '';
    newsletterError.value = '';

    // Basic email format validation
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(newsletterEmail.value)) {
        newsletterError.value = 'Please enter a valid email address.';
        return;
    }

    try {
        const response = await axios.post('/api/newsletter/subscribe', { email: newsletterEmail.value });
        if (response.data.success) {
            newsletterSuccess.value = 'Subscribed successfully!';
            newsletterEmail.value = '';
        } else {
            newsletterError.value = response.data.message || 'Subscription failed. Please try again.';
        }
    } catch (error) {
        if (error.response && error.response.data && error.response.data.errors) {
            // Display the first error message
            const firstErrorField = Object.keys(error.response.data.errors)[0];
            newsletterError.value = error.response.data.errors[firstErrorField][0];
        } else {
            newsletterError.value = 'An error occurred. Please try again.';
        }
    }
}
</script>

<style scoped>
@import 'swiper/swiper-bundle.css';

/* CSS Variables for Consistent Styling */
:root {
  --connectyed-button-light: #f0f0f0;
  --connectyed-button-dark: #333333;
  --connectyed-button-hover-light: #e0e0e0;
  --connectyed-button-hover-dark: #333333; /* Changed from #ffffff for better contrast */
}

/* Button Styles */
.bg-connectyed-button-light {
  background-color: var(--connectyed-button-light);
}

.text-connectyed-button-dark {
  color: var(--connectyed-button-dark);
}

.hover\:bg-connectyed-button-hover-light:hover {
  background-color: var(--connectyed-button-hover-light);
}

.hover\:text-connectyed-button-light:hover {
  color: var(--connectyed-button-hover-dark); /* Ensures text is readable */
}

/* Specific Button Hover Fixes */
.bg-connectyed-button-light.text-connectyed-button-dark.hover\:bg-connectyed-button-hover-light.hover\:text-connectyed-button-light {
  transition: background-color 0.3s, color 0.3s;
}

button, .bg-connectyed-button-light, .bg-connectyed-button-pagination-light {
  /* Ensure all buttons have smooth transitions */
  transition: background-color 0.3s, color 0.3s;
}

/* Additional Button Hover Styles */
button:hover, .bg-connectyed-button-light:hover, .bg-connectyed-button-pagination-light:hover {
  /* Maintain text readability on hover */
  color: var(--connectyed-button-hover-dark);
}

/* Specific styles for Subscribe button in footer */
.footer__newsletter--form button {
  color: #213366 !important;
}

/* Specific styles for Subscribe button in footer */
.footer__newsletter--form button:hover {
  color: var(--connectyed-button-hover-dark) !important;
}

/* Ensure all header buttons have readable text on hover */
button.bg-connectyed-button-light:hover,
a.bg-connectyed-button-light:hover {
  color: var(--connectyed-button-hover-dark) !important;
}

/* Newsletter Subscription Form Styles */
.footer__newsletter--form {
  position: relative;
}

.footer__newsletter--form input {
  width: 100%;
  padding: 10px 15px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.footer__newsletter--form input.border-red-500 {
  border-color: #f56565; /* Tailwind's red-500 */
}

.footer__newsletter--form button {
  position: absolute;
  right: 0px;
  top: 50%;
  transform: translateY(-50%);
  border: none;
  border-radius: 4px;
  cursor: pointer;
  color: #213366;
}

.text-green-500 {
  color: #48bb78; /* Tailwind's green-500 */
}

.text-red-500 {
  color: #f56565; /* Tailwind's red-500 */
}

.mt-2 {
  margin-top: 0.5rem; /* Tailwind's mt-2 */
}
</style>
