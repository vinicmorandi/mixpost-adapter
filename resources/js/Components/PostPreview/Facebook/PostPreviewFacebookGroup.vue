<script setup>
import {provide} from "vue";
import Panel from "@/Components/Surface/Panel.vue";
import Post from "../Facebook/Post.vue";

const props = defineProps({
    name: {
        required: true,
        type: String
    },
    username: {
        required: true,
        type: String
    },
    image: {
        required: true,
        type: String
    },
    content: {
        required: true,
        type: Array,
    }
})

provide('facebookCtx', {
    name: props.name,
    username: props.username,
    image: props.image,
    content: props.content,
    options: props.options
});
</script>
<template>
    <Panel class="relative">
        <Post/>
    </Panel>
</template>
