<script setup>
import {inject} from "vue";
import Alert from "../../Util/Alert.vue";
import VideoPlayer from "../../VideoPlayer/VideoPlayer.vue";
import Comments from "./Icons/Comments.vue";
import Share from "./Icons/Share.vue";
import Ellipsis from "./Icons/Ellipsis.vue";
import Likes from "./Icons/Likes.vue";

const data = inject('facebookCtx');
</script>
<template>
    <div>
        <template v-if="data.content[0].media.length && data.content[0].media[0].is_video">
            <div class="bg-black rounded-xl mx-auto max-w-sm">
                <div
                    class="mx-auto rounded-xl rounded-b-none border-none overflow-hidden max-w-full block outline-none relative">
                    <div class="bg-black aspect-w-9 aspect-h-16">
                        <div class="absolute inset-0">
                            <div class="relative w-full h-full flex items-center">
                                <div class="w-full h-full">
                                    <VideoPlayer :src="data.content[0].media[0].url"/>

                                    <div class="absolute right-0 bottom-2xl w-14">
                                        <div class="flex flex-col space-y-md">
                                            <div class="flex flex-col text-white">
                                                <Likes class="mx-auto"/>
                                                <span class="mt-xs text-xs text-center">1.1K</span>
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
                                                <Ellipsis class="mx-auto"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="absolute bottom-md px-md w-full">
                                        <div class="pr-[5.5rem]">
                                            <div class="flex items-center">
                                                <div class="mr-sm">
                                                    <span
                                                        class="flex justify-center items-center flex-shrink-0 w-10 h-10 rounded-full">
                                                        <img :src="data.image"
                                                             class="object-cover w-full h-full rounded-full" alt=""/>
                                                    </span>
                                                </div>
                                                <div class="text-white mr-sm max-w-full truncate">{{ data.name }}</div>
                                                <div
                                                    class="border border-gray-300 px-1 text-sm font-medium bg-black text-white rounded-md bg-opacity-30">
                                                    Follow
                                                </div>
                                            </div>
                                            <div class="text-white text-sm mt-sm truncate"
                                                 v-html="data.content[0].body"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex w-full px-sm bg-black rounded-b-xl">
                    <div class="flex w-full justify-center my-xs">
                        <div class="rounded-full px-sm py-1 w-full border border-white">
                            <span class="text-gray-200 text-sm">Add a comment...</span>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template v-else>
            <Alert variant="warning" :closeable="false">
                {{ $t('service.meta.reel_supports_one_video') }}
            </Alert>
        </template>
    </div>
</template>
