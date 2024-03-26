<script setup>
import {computed} from "vue";

const props = defineProps({
    value: {
        type: String,
        required: true
    },
    showName: {
        type: Boolean,
        required: false,
        default: true
    }
})

const classNames = computed(() => {
    return {
        'draft': 'bg-gray-500',
        'published': 'bg-lime-500',
        'publishing': 'bg-violet-500',
        'scheduled': 'bg-cyan-500',
        'failed': 'bg-red-500',
    }[props.value]
})
</script>
<template>
    <div class="flex items-center">
        <div :class="[classNames]" v-tooltip="`${showName ? '' : $t(`post.${value}`)}`" class="w-4 h-4 rounded-full"></div>
        <div v-if="showName" class="ml-xs">{{ $t(`post.${value}`) }}</div>
    </div>
</template>
