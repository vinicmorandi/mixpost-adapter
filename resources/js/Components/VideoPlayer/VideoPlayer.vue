<script setup>
import {onBeforeUnmount, onMounted, ref} from "vue";
import Play from "@/Icons/Play.vue";

const props = defineProps({
    src: {
        required: true,
        type: String,
    }
});

const emit = defineEmits(['play', 'pause'])

const player = ref(null);
const isPlaying = ref(false);
const currentTime = ref(0);
const duration = ref(0);

const playPause = () => {
    if (isPlaying.value) {
        player.value.pause()

        emit('pause')
    } else {
        player.value.play()

        emit('play')
    }

    isPlaying.value = !isPlaying.value
};

const seek = () => {
    currentTime.value = player.value.currentTime
}

const handleLoadedMetadata = () => {
    if (player.value.duration) {
        duration.value = player.value.duration
    }
};

const handleTimeUpdate = () => {
    seek();
};

onMounted(() => {
    player.value.addEventListener('loadedmetadata', handleLoadedMetadata)
    player.value.addEventListener('timeupdate', handleTimeUpdate)
});

onBeforeUnmount(() => {
    player.value.removeEventListener('loadedmetadata', handleLoadedMetadata)
    player.value.removeEventListener('timeupdate', handleTimeUpdate)
})
</script>
<template>
    <div @click="playPause" class="w-full h-full">
        <div class="z-10 w-full h-full absolute flex items-center justify-center">
            <button v-if="!isPlaying"
                    class="w-16 h-16 border-2 border-white rounded-full flex items-center justify-center text-white bg-black bg-opacity-50">
                <Play class="!w-10 !h-10"/>
            </button>
        </div>

        <video ref="player" aria-label="video-player" class="w-full h-full object-cover">
            <source :src="src"/>
        </video>

        <!--        <input-->
        <!--            type="range"-->
        <!--            min="0"-->
        <!--            :max="duration"-->
        <!--            step="0.1"-->
        <!--            v-model="currentTime"-->
        <!--            class="w-full"-->
        <!--            @input="seek"-->
        <!--        />-->
    </div>
</template>
