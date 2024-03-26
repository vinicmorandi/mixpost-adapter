<script setup>
import ExclamationIcon from "@/Icons/Exclamation.vue"

defineProps({
    table: {
        type: Boolean,
        default: false,
    }
})
</script>
<template>
    <div class="flex items-center" :class="{'p-md': table}">
        <div class="w-8 h-8 bg-cyan-100 text-cyan-600 rounded-full flex items-center justify-center mr-xs">
            <ExclamationIcon/>
        </div>
        <div>
            <template v-if="$slots.default">
                <slot/>
            </template>
            <template v-if="!$slots.default">{{ $t('util.no_results') }}</template>
        </div>
    </div>
</template>
