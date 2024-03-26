<script setup>
import {computed} from "vue";
import Panel from "@/Components/Surface/Panel.vue";
import HandThumbUp from "@/Icons/HandThumbUp.vue";
import HandThumbDown from "@/Icons/HandThumbDown.vue";
import ChatBubbleBottomCenterText from "@/Icons/ChatBubbleBottomCenterText.vue";
import Share from "@/Icons/Share.vue";
import Refresh from "@/Icons/Refresh.vue";
import EllipsisHorizontal from "@/Icons/EllipsisHorizontal.vue";
import Camera from "@/Icons/Camera.vue";
import MagnifyingGlass from "@/Icons/MagnifyingGlass.vue";
import VideoPlayer from "@/Components/VideoPlayer/VideoPlayer.vue";
import NoResult from "@/Components/Util/NoResult.vue";

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

const mainContent = computed(() => {
    return props.content[0];
});
</script>
<template>
    <Panel class="relative">
        <div v-if="mainContent.media.length" class="relative overflow-hidden rounded-xl">
            <VideoPlayer :src="mainContent.media[0].url"/>

            <div class="absolute top-md right-md">
                <div class="flex items-center space-x-lg text-white">
                    <MagnifyingGlass/>
                    <Camera/>
                    <EllipsisHorizontal/>
                </div>
            </div>

            <div class="absolute right-xs bottom-2xl w-16">
                <div class="flex flex-col space-y-lg">
                    <div class="flex flex-col text-white">
                        <HandThumbUp class="mx-auto"/>
                        <span class="mt-xs text-sm font-medium text-center">159</span>
                    </div>

                    <div class="flex flex-col text-white">
                        <HandThumbDown class="mx-auto"/>
                        <span class="mt-xs text-sm font-medium text-center">Dislike</span>
                    </div>

                    <div class="flex flex-col text-white">
                        <ChatBubbleBottomCenterText class="mx-auto"/>
                        <span class="mt-xs text-sm font-medium text-center">47</span>
                    </div>

                    <div class="flex flex-col text-white">
                        <Share class="mx-auto"/>
                        <span class="mt-xs text-sm font-medium text-center">Share</span>
                    </div>

                    <div class="flex flex-col text-white">
                        <Refresh class="mx-auto"/>
                        <span class="mt-xs text-sm font-medium text-center">Remix</span>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-md px-md pr-2xl w-full">
                <div class="flex items-center">
                    <div class="mr-sm">
                        <span class="inline-flex justify-center items-center flex-shrink-0 w-10 h-10 rounded-full">
                            <img :src="image"
                                 class="object-cover w-full h-full rounded-full" alt=""/>
                        </span>
                    </div>
                    <div>
                        <div class="text-white">@{{ username }}</div>
                    </div>
                </div>

                <div v-if="options.title" class="mt-xs pr-2xl text-white">{{ options.title }}</div>
            </div>
        </div>
        <div v-else>
            <NoResult>
                {{ $t('service.youtube.select_video') }}
            </NoResult>
        </div>
    </Panel>
</template>
