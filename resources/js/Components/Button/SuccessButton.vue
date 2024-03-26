<script setup>
import useButtonSize from "@/Composables/useButtonSize"
import CircleLoadingIcon from "@/Icons/CircleLoading.vue"

const props = defineProps({
    type: {
        type: String,
        default: 'button',
    },
    size: {
        type: String,
        default: 'lg'
    },
    isLoading: {
        type: Boolean,
        default: false,
    },
    hiddenTextOnSmallScreen: {
        type: Boolean,
        default: false,
    }
});

const {sizeClass} = useButtonSize(props.size);
</script>

<template>
    <button :type="type" :class="sizeClass"
            class="relative inline-flex items-center bg-green-500 border border-transparent rounded-md font-medium text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:border-green-700 focus:shadow-outline-green disabled:bg-green-400 transition ease-in-out duration-200">
        <span v-if="$slots.icon" class="inline-flex"
              :class="{'sm:mr-xs': $slots.default, 'mr-0 sm:mr-xs': hiddenTextOnSmallScreen, 'mr-xs': !hiddenTextOnSmallScreen && $slots.default}">
            <slot name="icon"/>
        </span>

        <span v-if="$slots.default" class="inline-flex items-center" :class="{'hidden sm:inline': hiddenTextOnSmallScreen}">
            <slot/>
        </span>

        <span v-if="isLoading" class="absolute left-0 top-0 flex justify-center items-center w-full h-full bg-green-500">
             <CircleLoadingIcon class="animate-spin text-white"/>
        </span>
    </button>
</template>
