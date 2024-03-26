<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
    hiddenTextOnSmallScreen: {
        type: Boolean,
        default: false,
    }
});
</script>
<template>
    <button :type="type"
            class="relative inline-flex items-center text-red-400 hover:text-red-500 transition-colors ease-in-out duration-200">
        <span v-if="$slots.icon" class="inline-flex"
              :class="{'sm:mr-xs': $slots.default, 'mr-0 sm:mr-xs': hiddenTextOnSmallScreen, 'mr-xs': !hiddenTextOnSmallScreen && $slots.default}">
            <slot name="icon"/>
        </span>

        <span v-if="$slots.default" class="inline-flex items-center" :class="{'hidden sm:inline': hiddenTextOnSmallScreen}">
            <slot/>
        </span>
    </button>
</template>
