<template>
  <div class="text-center py-10 text-gray-500">
    <svg class="mx-auto animate-spin h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24"
      xmlns="http://www.w3.org/2000/svg">
      <circle class="opacity-25" cx="12" cy="12" r="10"
        stroke="currentColor" stroke-width="4" />
      <path class="opacity-75" fill="currentColor"
        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
    </svg>
    <p class="mt-2">{{ message }}</p>
  </div>
</template>

<script setup>
defineProps({
  message: {
    type: String,
    default: 'Loading...',
  },
})
</script>
