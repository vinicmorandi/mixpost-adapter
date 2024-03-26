<script setup>
import ProviderIcon from "@/Components/Account/ProviderIcon.vue";

defineProps({
    provider: {
        type: String,
        required: true,
    },
    title: {
        type: String,
        required: true
    }
})
</script>
<template>
    <div class="border border-gray-200 p-md rounded-lg">
        <div class="flex items-center mb-md">
            <ProviderIcon :provider="provider" class="mr-xs"/>
            <span>{{ title }}</span>
        </div>

        <slot/>
    </div>
</template>
