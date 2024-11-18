<template>
    <div class="mb-2">
      <label class="block text-gray-700 text-sm font-bold mb-2">{{ label }}
        <span v-if="required" class="text-red-500">*</span>
      </label>
      <input
        :type="type"
        class="block w-full bg-white border border-gray-300 rounded-md py-2 px-3 shadow-sm focus:outline-none focus:ring-connectyed-button-dark focus:border-connectyed-border-light"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :required="required"
        :class="{'border-red-500': error}"
      />
      <p v-if="errorMessage" class="text-red-500 text-sm">{{ errorMessage }}</p>
    </div>
  </template>
  
  <script>
  export default {
    name: 'InputText',
    props: {
        label: {
            type: String,
            required: true
        },
        type: {
            type: String,
            default: 'text'
        },
        modelValue: {
            type: [String, Number],
            default: ''
        },
        required: {
            type: Boolean,
            default: false // Required prop, defaults to false
        },
        error: {
            type: Boolean, // New prop to indicate if there is an error
            default: false
        },
        errorMessage: {
            type: String, // Prop for displaying the error message
            default: ''
        }
    },
  };
  </script>
  
  <style scoped>
  /* Additional custom styles can be added here if needed */
  </style>
