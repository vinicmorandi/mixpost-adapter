<script setup>
import Textarea from "../Form/Textarea.vue";
import VerticalGroup from "../Layout/VerticalGroup.vue";

defineProps({
    content: {
        required: true,
        type: Object,
    }
});
</script>
<template>
    <VerticalGroup>
        <template #title>
            <label for="m_html">HTML</label>
        </template>

        <Textarea id="m_html" v-model="content.body" class="min-h-[350px]"/>
    </VerticalGroup>
</template>
