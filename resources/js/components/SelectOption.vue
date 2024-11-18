<template>
    <div class="mb-2">
        <label class="block text-gray-700 text-sm font-bold mb-2">{{ label }}</label>
        
        <!-- Search input for country selection -->
        <div v-if="enableSearch" class="relative mb-2">
            <input
                type="text"
                v-model="searchQuery"
                :placeholder="'Search ' + label"
                class="block w-full bg-white border border-gray-300 rounded-md py-2 px-3 shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            />
        </div>

        <select
            class="block w-full bg-white border border-gray-300 rounded-md py-2 px-3 shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            :value="modelValue"
            @change="$emit('update:modelValue', $event.target.value)"
            :required="required"
            :class="{'border-red-500': error}"
        >
            <option value="" disabled>Select {{ label }}</option>
            <option v-for="option in filteredOptions" :key="option" :value="option">
                {{ option }}
            </option>
        </select>

        <!-- Error message -->
        <p v-if="error && errorMessage" class="mt-1 text-red-500 text-sm">
            {{ errorMessage }}
        </p>
    </div>
</template>

<script>
export default {
    name: 'SelectOption',
    props: {
        label: {
            type: String,
            required: true
        },
        options: {
            type: Array,
            required: true
        },
        modelValue: {
            type: String,
            required: false
        },
        required: {
            type: Boolean,
            default: false
        },
        error: {
            type: Boolean,
            default: false
        },
        errorMessage: {
            type: String,
            default: ''
        },
        enableSearch: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            searchQuery: ''
        };
    },
    computed: {
        filteredOptions() {
            if (!this.searchQuery || !this.enableSearch) {
                return this.options;
            }
            
            const query = this.searchQuery.toLowerCase();
            return this.options.filter(option => 
                option.toLowerCase().includes(query)
            );
        }
    },
    watch: {
        // Reset search when options change
        options() {
            this.searchQuery = '';
        }
    },
    methods: {
        clearSearch() {
            this.searchQuery = '';
        }
    }
};
</script>

<style scoped>
select {
    /* Prevent zoom on mobile devices */
    font-size: 16px;
}

/* Optional: Style for better mobile experience */
@media (max-width: 640px) {
    select {
        padding-right: 2rem;
    }
}
</style>