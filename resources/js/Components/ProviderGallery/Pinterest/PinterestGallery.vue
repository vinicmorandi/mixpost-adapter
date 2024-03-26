<script setup>
import Image from "@/Components/ProviderGallery/Pinterest/Image.vue"
import Alert from "../../Util/Alert.vue";

defineProps({
    media: {
        type: Array,
        required: true
    }
})
</script>
<template>
    <div v-if="media.length">
        <div v-if="media[0].is_video" class="overflow-hidden rounded-xl">
            <Alert variant="warning"> {{ $t('service.pinterest.not_support_video')   }}</Alert>
        </div>

        <div v-else class="overflow-hidden rounded-3xl">
            <Image :media="media[0]"/>
        </div>
    </div>
</template>
