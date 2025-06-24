<!-- resources/js/Components/ConfirmationDialog.vue -->
<template>
  <Dialog :open="open" @update:open="value => $emit('update:open', value)">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle>{{ title }}</DialogTitle>
        <DialogDescription>{{ message }}</DialogDescription>
      </DialogHeader>
      <div class="flex justify-end space-x-2 mt-4">
        <Button variant="secondary" @click="$emit('cancel')">Cancel</Button>
        <Button variant="destructive" @click="$emit('confirm')">Confirm</Button>
      </div>
    </DialogContent>
  </Dialog>
</template>

<script setup>
import { ref } from 'vue'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'

defineProps({
  open: Boolean,
  title: {
    type: String,
    default: 'Are you sure?',
  },
  message: {
    type: String,
    default: 'This action cannot be undone.',
  },
})
defineEmits(['update:open', 'confirm', 'cancel'])
</script>
