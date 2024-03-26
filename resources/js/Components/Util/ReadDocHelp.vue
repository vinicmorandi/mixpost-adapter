<script setup>
import DocumentIcon from "@/Icons/Document.vue";

defineProps({
    href: {
        type: String,
        default: '#'
    }
})
</script>
<template>
    <div class="flex items-center">
        <span><DocumentIcon class="!w-4 !h-4 mr-xs"/></span>
        <span v-html="$t('util.read_doc', {href})"/>
    </div>
</template>
