<script setup>
import {inject} from "vue";
import VideoPlayer from "../../VideoPlayer/VideoPlayer.vue";
import Likes from "./Icons/Likes.vue";
import Share from "./Icons/Share.vue";

const data = inject('instagramCtx');
</script>
<template>
    <div class="bg-black rounded-xl mx-auto max-w-sm">
        <div class="mx-auto rounded-xl rounded-b-lg border-none overflow-hidden max-w-full block outline-none relative">
            <div class="bg-black aspect-w-9 aspect-h-16">
                <div class="absolute inset-0">
                    <div class="relative w-full h-full flex items-center">
                        <div class="w-full h-full">
                            <template v-if="data.content[0].media[0].is_video">
                                <VideoPlayer :src="data.content[0].media[0].url"/>
                            </template>

                            <template v-else>
                                <img
                                    :src="data.content[0].media[0].url"
                                    class="w-full h-full object-cover" alt="Story image">
                            </template>
                        </div>
                    </div>

                    <div class="absolute h-20 w-full opacity-50 left-0 top-0"
                         style="background: linear-gradient(rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.5) 50%, rgba(0, 0, 0, 0) 100%) 100% center;"></div>

                    <div class="absolute top-0 left-sm right-sm mt-xs h-[2px] flex space-x-1">
                        <div class="h-full w-full bg-white bg-opacity-100"></div>
                    </div>

                    <div class="absolute top-lg left-md">
                        <div class="flex items-center text-sm text-white gap-xs">
                                         <span
                                             class="inline-flex justify-center items-center flex-shrink-0 w-8 h-8 rounded-full mr-sm">
                                                 <img :src="data.image" class="object-cover w-full h-full rounded-full"
                                                      alt=""/>
                                             </span>

                            <div class="flex gap-xs">
                                <span>{{ data.username }}</span>
                                <span class="text-white opacity-80">1h</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex w-full p-md space-x-md bg-black text-white rounded-b-xl">
            <div class="border-gray-400 border rounded-full py-sm pl-md flex-grow text-sm">Send message</div>
            <div class="flex space-x-md items-center">
                <Likes/>
                <Share/>
            </div>
        </div>
    </div>
</template>
