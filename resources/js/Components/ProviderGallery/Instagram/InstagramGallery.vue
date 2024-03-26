<script setup>
import Video from "@/Components/ProviderGallery/Instagram/Video.vue"
import OneImage from "@/Components/ProviderGallery/Instagram/OneImage.vue"

defineProps({
    media: {
        type: Array,
        required: true
    }
})
</script>
<template>
    <div v-if="media[0].is_video" class="overflow-hidden">
        <Video :media="media[0]"/>
    </div>

    <div v-else class="overflow-hidden">
        <OneImage :media="media[0]"/>
    </div>
</template>
