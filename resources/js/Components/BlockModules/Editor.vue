<script setup>
import EditorClassic from "../Package/EditorClassic.vue";

defineProps({
    content: {
        required: true,
        type: Object,
    }
});
</script>
<template>
    <EditorClassic :value="content.body" @update="content.body = $event"/>
</template>
