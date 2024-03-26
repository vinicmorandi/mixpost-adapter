<script setup>
import {provide} from "vue";
import Panel from "@/Components/Surface/Panel.vue";
import Alert from "../../Util/Alert.vue";
import Story from "./Story.vue";
import Reel from "./Reel.vue";
import Post from "./Post.vue";

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

provide('instagramCtx', {
    name: props.name,
    username: props.username,
    image: props.image,
    content: props.content,
    options: props.options
});
</script>
<template>
    <Panel class="relative">
        <template v-if="content[0].media.length">
            <template v-if="options.type === 'reel'">
                <Reel/>
            </template>

            <template v-else-if="options.type === 'story'">
                <Story/>
            </template>

            <div v-else>
                <Post/>
            </div>
        </template>

        <template v-else>
            <Alert variant="warning" :closeable="false">
                {{ $t('service.instagram.select_video_image') }}
            </Alert>
        </template>
    </Panel>
</template>
