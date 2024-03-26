<script setup>
defineProps({
    media: {
        type: Object,
        required: true
    }
})
</script>
<template>
    <div class="w-full h-full inset-0 rounded-3xl relative">
        <img :src="media.thumb_url" draggable="false" class="rounded-3xl h-full w-full object-cover object-center" alt="Image"/>
    </div>
</template>
