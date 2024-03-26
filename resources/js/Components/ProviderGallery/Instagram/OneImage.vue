<script setup>
defineProps({
    media: {
        type: Object,
        required: true
    }
})
</script>
<template>
    <div class="h-auto">
        <div class="w-full h-full inset-0 relative">
            <img :src="media.thumb_url" draggable="false" class="h-full w-full object-cover object-center" alt="Image"/>
        </div>
    </div>
</template>
