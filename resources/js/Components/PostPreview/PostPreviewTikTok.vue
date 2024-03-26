<script setup>
import {computed, ref} from "vue";
import useEditor from "../../Composables/useEditor";
import {get} from "lodash";
import {usePage} from "@inertiajs/vue3";
import Panel from "@/Components/Surface/Panel.vue";
import VideoPlayer from "@/Components/VideoPlayer/VideoPlayer.vue";
import NoResult from "@/Components/Util/NoResult.vue";
import EditorReadOnly from "../Package/EditorReadOnly.vue";
import Alert from "../Util/Alert.vue";

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

const {isDocEmpty} = useEditor();

const videoIsPlaying = ref(false);

const isDirectShareType = computed(() => {
    return get(usePage().props.service_configs, 'tiktok.share_type', 'inbox') === 'direct';
})
</script>
<template>
    <Panel class="relative">
        <template v-if="content[0].media.length">
            <Alert v-if="!isDirectShareType"
                   class="mb-sm"
                   :closeable="false">
                {{ $t('service.tiktok.direct_share_type') }}
            </Alert>

            <div v-if="content[0].media[0].is_video" class="relative overflow-hidden rounded-xl mx-auto max-w-md">
                <div class="bg-black aspect-w-9 aspect-h-16">
                    <div class="absolute inset-0">
                        <div class="relative w-full h-full flex items-center">
                            <div class="w-full h-full">
                                <VideoPlayer :src="content[0].media[0].url"
                                             @play="videoIsPlaying = true"
                                             @pause="videoIsPlaying = false"/>

                                <div class="absolute top-md w-full">
                                    <div class="flex justify-center">
                                        <div class="relative mr-md">
                                            <div class="font-medium text-gray-400 tracking-wide">
                                                Following
                                                <div class="absolute top-0 right-0 -mr-1.5 -mt-0.5">
                                                    <div class="w-2 h-2 rounded-full bg-red-600"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-medium text-white tracking-wide">For You</div>
                                            <div class="bg-white w-5 h-[3px] mx-auto mt-1"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="absolute right-xs bottom-2xl w-16">
                                    <div class="flex flex-col space-y-lg">
                                        <div
                                            class="relative flex justify-center items-center flex-shrink-0 w-10 h-10 mx-auto rounded-full bg-white p-0.5">
                                            <img :src="image"
                                                 class="object-cover w-full h-full rounded-full" alt=""/>

                                            <span class="absolute -bottom-xs flex justify-center">
                                <span
                                    class="w-4 h-4 bg-red-600 text-white flex items-center justify-center rounded-full font-medium">+</span>
                            </span>
                                        </div>

                                        <div class="flex flex-col text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                                                 class="h-8 w-8 mx-auto">
                                                <path
                                                    d="M7.5 2.25c3 0 4.5 2 4.5 2s1.5-2 4.5-2c3.5 0 6 2.75 6 6.25 0 4-3.269 7.566-6.25 10.25C14.41 20.407 13 21.5 12 21.5s-2.45-1.101-4.25-2.75C4.82 16.066 1.5 12.5 1.5 8.5c0-3.5 2.5-6.25 6-6.25Z"></path>
                                            </svg>
                                            <span class="mt-xs text-xs text-center">2.4K</span>
                                        </div>

                                        <div class="flex flex-col text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 48 48"
                                                 class="h-8 w-8 mx-auto">
                                                <path
                                                    d="M3.5 21.44C3.5 11.47 13.04 4 24 4s20.5 7.47 20.5 17.44c0 5.85-2.93 10.69-6.47 14.37a41.67 41.67 0 0 1-11.06 7.97A2.05 2.05 0 0 1 24 41.95v-3.08c-10.96 0-20.5-7.47-20.5-17.43Zm11.79 3.07a2.56 2.56 0 1 0 0-5.12 2.56 2.56 0 0 0 0 5.12Zm8.71 0a2.56 2.56 0 1 0 0-5.12 2.56 2.56 0 0 0 0 5.12Zm11.27-2.56a2.56 2.56 0 1 0-5.12 0 2.56 2.56 0 0 0 5.12 0Z"></path>
                                            </svg>
                                            <span class="mt-xs text-xs text-center">158</span>
                                        </div>

                                        <div class="flex flex-col text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"
                                                 class="h-8 w-8 mx-auto">
                                                <path fill="currentColor" fill-rule="evenodd"
                                                      d="M10.938 3.175a.674.674 0 0 1 1.138-.488l6.526 6.215c.574.547.554 1.47-.043 1.991l-6.505 5.676a.674.674 0 0 1-1.116-.508V13.49s-6.985-1.258-9.225 2.854c-.209.384-1.023.518-.857-1.395.692-3.52 2.106-9.017 10.082-9.017V3.175Z"
                                                      clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="mt-xs text-xs text-center">1081</span>
                                        </div>
                                        <div class="flex flex-col text-white">
                                            <div
                                                :class="{'animate-spin-slow': videoIsPlaying}"
                                                class="relative flex justify-center items-center flex-shrink-0 w-10 h-10 mx-auto rounded-full bg-gray-800 p-xs">
                                                <img :src="image"
                                                     class="object-cover w-full h-full rounded-full" alt=""/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="absolute bottom-md px-md pr-2xl w-full">
                                    <div class="text-white font-medium">{{ name }}</div>

                                    <template v-if="isDirectShareType">
                                        <EditorReadOnly :value="content[0].body"
                                                        :class="{'mt-xs': !isDocEmpty(content[0].body), 'hidden': isDocEmpty(content[0].body)}"
                                                        class="text-white text-sm pr-2xl"/>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <template v-else>
                <Alert variant="warning" :closeable="false">
                    {{ $t('service.tiktok.video_limit') }}
                </Alert>
            </template>
        </template>

        <template v-else>
            <NoResult>
                {{ $t('service.tiktok.select_video') }}
            </NoResult>
        </template>
    </Panel>
</template>
