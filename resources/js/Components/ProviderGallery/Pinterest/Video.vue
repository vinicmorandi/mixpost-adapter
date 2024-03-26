<script setup>
import {ref} from "vue";
import PlayIcon from "@/Icons/Play.vue"

const isOpen = ref(false);

defineProps({
    media: {
        type: Object,
        required: true
    }
})
</script>
<template>
    <div class="w-full h-full inset-0 rounded-3xl bg-pinterest relative">
        <div class="w-full h-full absolute flex items-center justify-center">
            <button @click="isOpen = true" v-if="!isOpen"
                    class="w-16 h-16 border-2 border-white rounded-full flex items-center justify-center text-white bg-pinterest bg-opacity-50">
                <PlayIcon class="!w-10 !h-10"/>
            </button>
        </div>

        <img v-if="!isOpen" :src="media.thumb_url" draggable="false" class="h-full mx-auto rounded-3xl"
             alt="Image"/>

        <video v-if="isOpen" class="w-auto h-full mx-auto rounded-3xl" controls autoplay media="">
            <source :src="media.url" :type="media.mime_type">
            {{ $t('error.browser_video_unsupported') }}
        </video>
    </div>
</template>
