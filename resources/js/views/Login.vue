<!-- resources/js/views/Login.vue -->

<template>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8 flex items-center justify-center">
        <div class="w-full max-w-sm mt-20">
            <div class="font-bold text-xl mb-2">Login</div>
            <form class="bg-connectyed-card-light shadow-md rounded px-8 pt-6 pb-8 mb-4 border-solid border-2 border-gray-200" @submit.prevent="login">
                <!-- Display Validation Errors -->
                <div v-if="Object.keys(validationErrors).length > 0" class="mb-4">
                    <div class="bg-red-200 text-red-800 p-4 rounded">
                        <ul>
                            <li v-for="(value, key) in validationErrors" :key="key">
                                <!-- Handle both array and single error messages -->
                                {{ Array.isArray(value) ? value[0] : value }}
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                        Username
                    </label>
                    <input 
                        class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                        v-model="auth.username" 
                        name="username" 
                        id="username" 
                        type="text" 
                        placeholder="Username" 
                        autocomplete="off"
                    >
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                        Password
                    </label>
                    <input 
                        class="appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" 
                        v-model="auth.password" 
                        name="password" 
                        id="password" 
                        type="password" 
                        placeholder="******"
                    >
                </div>
                <div class="flex items-center justify-between">
                    <button 
                        class="bg-connectyed-button-light text-connectyed-button-dark hover:bg-connectyed-button-hover-light hover:text-connectyed-button-light w-full h-12 duration-150" 
                        type="submit" 
                        :disabled="processing"
                    >
                        {{ processing ? "Please wait" : "Login" }}
                    </button>
                </div>
                <div class="my-5 text-black">
                    <!-- You can add additional content here if needed -->
                </div>

                <label>
                    Don't have an account? 
                    <router-link :to="{ name: 'register' }" class="text-blue-500 hover:underline">
                        Register Now!
                    </router-link>
                </label>
                <div class="mt-4">
                    <router-link to="/password/forgot" class="text-blue-500 hover:underline">
                        Forgot your password?
                    </router-link>
                </div>
            </form>

            <p class="text-center text-gray-500 text-xs">
                &copy;2024 Connectyed.
            </p>
        </div>
    </div>
</template>

<script>
import { mapActions } from 'vuex'

export default {
    name: "Login",
    data() {
        return {
            auth: {
                username: "",
                password: ""
            },
            validationErrors: {},
            processing: false
        }
    },
    methods: {
        ...mapActions({
            signIn: 'auth/login'
        }),
        async login() {
            this.processing = true;
            this.validationErrors = {};

            try {
                await this.signIn(this.auth);
                // Successful login is handled by Vuex action (redirecting the user)
            } catch (error) {
                const response = error.response;
                if (response && response.status === 422) {
                    // Handle validation errors
                    this.validationErrors = response.data.errors || response.data.message;
                } else if (response && response.status === 401) {
                    // Handle invalid credentials
                    this.validationErrors = { general: 'Invalid username or password.' };
                } else if (response && response.status === 403) {
                    // Handle unverified account
                    this.validationErrors = { general: response.data.message || 'Account not verified.' };
                } else {
                    // Handle other unexpected errors
                    this.validationErrors = { general: 'An unexpected error occurred.' };
                }
            } finally {
                this.processing = false;
            }
        }
    }
}
</script>

<style scoped>
/* Component-specific styles */

/* Example: Adjust error message styling */
.bg-red-200 {
    background-color: #fee2e2;
}
.text-red-800 {
    color: #991b1b;
}
.p-4 {
    padding: 1rem;
}
.rounded {
    border-radius: 0.25rem;
}
</style>
