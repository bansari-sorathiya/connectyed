// resources/js/store/index.js

import { createStore } from 'vuex'
import createPersistedState from 'vuex-persistedstate'
import auth from './auth'

const store = createStore({
    modules: {
        auth
    },
    plugins: [
        createPersistedState({
            key: 'your_app_store', // Optional: Customize the key
            paths: ['auth'], // Persist only the auth module
            storage: window.localStorage,
            // Consider adding a filter to exclude sensitive information
            // For example:
            // filter: (mutation, state) => {
            //     // Only persist certain mutations or state parts
            // }
        }),
    ],
})

export default store
