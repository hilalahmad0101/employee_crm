<!-- resources/js/Components/Toast.vue -->
<template>
  <transition
    enter-active-class="transform ease-out duration-300 transition"
    enter-from-class="translate-y-2 opacity-0"
    enter-to-class="translate-y-0 opacity-100"
    leave-active-class="transform ease-in duration-200 transition"
    leave-from-class="translate-y-0 opacity-100"
    leave-to-class="translate-y-2 opacity-0"
  >
    <div
      v-if="visible"
      class="fixed top-5 right-5 z-50 bg-white border border-gray-200 rounded-lg shadow-lg w-[300px] p-4 flex items-start justify-between space-x-2"
    >
      <div class="text-sm text-gray-800 font-medium">
        {{ message }}
      </div>
      <button
        @click="close"
        class="text-gray-500 hover:text-gray-800 transition"
        aria-label="Close"
      >
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
          stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>
    </div>
  </transition>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  show: Boolean,
  message: String,
  duration: {
    type: Number,
    default: 4000,
  },
})

const visible = ref(props.show)
let timeout = null

watch(() => props.show, (val) => {
  visible.value = val
  if (val) {
    clearTimeout(timeout)
    timeout = setTimeout(() => {
      visible.value = false
    }, props.duration)
  }
})

function close() {
  visible.value = false
}
</script>
