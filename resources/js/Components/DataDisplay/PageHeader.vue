<script setup>
import Flex from "../Layout/Flex.vue";

defineProps({
    title: {
        required: true,
        type: String
    },
    col: {
        type: Boolean,
        default: false,
    },
    responsive: {
        type: Boolean,
        default: true,
    }
})
</script>
<template>
    <div class="row-px mb-2xl">
        <Flex class="justify-between items-start">
            <div>
                <div class="flex items-center">
                    <slot name="beforeTitle"/>
                    <h1 class="font-semibold text-xl">
                        {{ $props.title }}
                    </h1>
                </div>
                <div v-if="$slots.description" class="mt-xs text-lg">
                    <slot name="description"/>
                </div>
            </div>
            <slot/>
        </Flex>
    </div>
</template>
