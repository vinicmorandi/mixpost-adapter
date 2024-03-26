<script setup>
import {inject} from "vue";
import Comments from "./Icons/Comments.vue";
import Share from "./Icons/Share.vue";
import Ellipsis from "./Icons/Ellipsis.vue";
import Camera from "../../../Icons/Camera.vue";
import Likes from "./Icons/Likes.vue";
import Alert from "../../Util/Alert.vue";
import VideoPlayer from "../../VideoPlayer/VideoPlayer.vue";

const data = inject('instagramCtx');
</script>
<template>
    <div>
        <template v-if="data.content[0].media[0].is_video">
            <div class="bg-black rounded-xl mx-auto max-w-sm">
                <div class="mx-auto rounded-xl rounded-b-lg border-none overflow-hidden max-w-full block outline-none relative">
                    <div class="bg-black aspect-w-9 aspect-h-16">
                        <div class="absolute inset-0">
                            <div class="relative w-full h-full flex items-center">
                                <div class="w-full h-full">
                                    <VideoPlayer :src="data.content[0].media[0].url"/>

                                    <div class="absolute top-md w-full flex justify-between items-center px-md">
                                        <div class="text-white font-medium text-md">Reels</div>
                                        <div class="flex items-center space-x-lg text-white">
                                            <Camera/>
                                        </div>
                                    </div>

                                    <div class="absolute right-xs bottom-2xl w-16">
                                        <div class="flex flex-col space-y-md">
                                            <div class="flex flex-col text-white">
                                                <Likes class="mx-auto"/>
                                                <span class="mt-xs text-xs text-center">Likes</span>
                                            </div>

                                            <div class="flex flex-col text-white">
                                                <Comments class="mx-auto"/>
                                                <span class="mt-xs text-xs text-center">428</span>
                                            </div>

                                            <div class="flex flex-col text-white">
                                                <Share class="mx-auto"/>
                                                <span class="mt-xs text-xs text-center">2.8K</span>
                                            </div>

                                            <div class="flex flex-col text-white">
                                                <Ellipsis class="mx-auto transform rotate-90"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="absolute bottom-lg px-md pr-[5.5rem] w-full">
                                        <div class="flex items-center">
                                            <div class="mr-sm">
                                <span class="flex justify-center items-center flex-shrink-0 w-10 h-10 rounded-full">
                                    <img :src="data.image"
                                         class="object-cover w-full h-full rounded-full" alt=""/>
                                </span>
                                            </div>

                                            <div class="text-white max-w-full truncate">{{ data.username }}</div>
                                        </div>
                                        <div class="text-white text-sm mt-sm truncate" v-html="data.content[0].body"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template v-else>
            <Alert variant="warning" :closeable="false">
                {{ $t('service.meta.reel_supports_one_video')}}
            </Alert>
        </template>
    </div>
</template>
