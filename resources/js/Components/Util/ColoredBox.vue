<script setup>
defineProps({
    color: {
        required: true,
        type: String,
    }
})
</script>
<template>
    <div class="block">
        <div
            :style="{'background': color}"
            role="button"
            type="button"
            class="border border-stone-600 w-10 h-10 rounded-md"/>
        <template v-if="$slots.default">
            <div class="mt-1">
                <slot/>
            </div>
        </template>
    </div>
</template>
