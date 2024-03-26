<script setup>
import {provide} from "vue";
import Panel from "@/Components/Surface/Panel.vue";
import Post from "./Post.vue";
import Reel from "./Reel.vue";
import Story from "./Story.vue";

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
    },
    options: {
        required: true,
        type: Object
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
        <template v-if="options.type === 'reel'">
            <Reel/>
        </template>

        <template v-else-if="options.type === 'story'">
            <Story/>
        </template>

        <div v-else>
            <Post/>
        </div>
    </Panel>
</template>
