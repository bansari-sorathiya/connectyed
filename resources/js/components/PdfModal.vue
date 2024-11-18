<template>
    <div v-if="isOpen" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <iframe :src="pdfUrl" width="300px" height="500px"></iframe>
        <div class="flex justify-center">
            <button class="bg-connectyed-button-light text-connectyed-button-dark py-4 px-1 w-24 justify-self-center" @click="closeModal">Close</button>
        </div>
      </div>
        
    </div>
  </template>
  
  <script>
  export default {
    props: ['isOpen', 'pdfUrl'],
    methods: {
      closeModal() {
        this.$emit('close');
      }
    }
  };
  </script>
  
  <style>
  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
  }
  
  .modal-content {
    background-color: white;
    padding: 20px;
    border-radius: 5px;
    max-width: 70%;
    max-height: 70%;
  }
  </style>