<script setup>
import {computed} from "vue";
import useEditor from "@/Composables/useEditor";
import Panel from "@/Components/Surface/Panel.vue";
import Gallery from "@/Components/ProviderGallery/Linkedin/LinkedinGallery.vue"
import EditorReadOnly from "@/Components/Package/EditorReadOnly.vue";
import LikeImg from "@img/social-icons/linkedin/like.svg"
import LightImg from "@img/social-icons/linkedin/light.svg"
import HeartImg from "@img/social-icons/linkedin/heart.svg"
import Avatar from "../DataDisplay/Avatar.vue";

const props = defineProps({
    name: {
        required: true,
        type: String
    },
    username: {
        required: true,
        type: [String, null]
    },
    image: {
        required: true,
        type: [String, null]
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

const mainContent = computed(()=> {
    return props.content[0];
});
</script>
<template>
    <Panel class="relative">
        <div class="flex items-start justify-between">
           <div class="flex items-center">
               <div class="mr-sm">
                <span class="inline-flex justify-center items-center flex-shrink-0 w-10 h-10 rounded-full">
                    <img v-if="image" :src="image"
                         class="object-cover w-full h-full rounded-full" alt=""/>
                    <template v-else>
                        <Avatar :name="name"/>
                    </template>
                </span>
               </div>
               <div>
                   <div class="font-medium mr-xs">{{ name }}</div>
                   <div class="text-gray-400"> {{$t('service.linkedin.job_title')}}</div>
               </div>
           </div>
        </div>

        <div class="w-full">
            <EditorReadOnly :value="mainContent.body"
                            :class="{'mt-xs': !isDocEmpty(mainContent.body), 'mb-xs': mainContent.media.length}"/>

            <Gallery :media="mainContent.media"/>

            <div class="mt-md flex items-center justify-between">
               <div v-tooltip="$t('service.representative_data')" class="flex">
                   <div class="flex items-center">
                       <img :src="LikeImg" alt="Like" class="w-5 h-5" draggable="false"/>
                   </div>
                   <div class="flex items-center -ml-xs z-10">
                       <img :src="LightImg" alt="Light" class="w-5 h-5" draggable="false"/>
                   </div>
                   <div class="flex items-center -ml-xs z-20">
                       <img :src="HeartImg" alt="Light" class="w-5 h-5" draggable="false"/>
                   </div>
               </div>
                <div v-tooltip="$t('service.representative_data')" class="flex items-center text-gray-500 text-sm">
                    <div class="mr-1">3 comments</div>
                    <div class="mr-1">&#183;</div>
                    <div>1 repost</div>
                </div>
            </div>
            <div class="mt-md pt-md flex items-center justify-around border-t border-gray-200 text-gray-700">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" data-supported-dps="24x24" fill="currentColor">
                        <path d="M19.46 11l-3.91-3.91a7 7 0 01-1.69-2.74l-.49-1.47A2.76 2.76 0 0010.76 1 2.75 2.75 0 008 3.74v1.12a9.19 9.19 0 00.46 2.85L8.89 9H4.12A2.12 2.12 0 002 11.12a2.16 2.16 0 00.92 1.76A2.11 2.11 0 002 14.62a2.14 2.14 0 001.28 2 2 2 0 00-.28 1 2.12 2.12 0 002 2.12v.14A2.12 2.12 0 007.12 22h7.49a8.08 8.08 0 003.58-.84l.31-.16H21V11zM19 19h-1l-.73.37a6.14 6.14 0 01-2.69.63H7.72a1 1 0 01-1-.72l-.25-.87-.85-.41A1 1 0 015 17l.17-1-.76-.74A1 1 0 014.27 14l.66-1.09-.73-1.1a.49.49 0 01.08-.7.48.48 0 01.34-.11h7.05l-1.31-3.92A7 7 0 0110 4.86V3.75a.77.77 0 01.75-.75.75.75 0 01.71.51L12 5a9 9 0 002.13 3.5l4.5 4.5H19z"></path>
                    </svg>
                    <div class="font-medium">Like</div>
                </div>

                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" data-supported-dps="24x24" fill="currentColor">
                        <path d="M7 9h10v1H7zm0 4h7v-1H7zm16-2a6.78 6.78 0 01-2.84 5.61L12 22v-4H8A7 7 0 018 4h8a7 7 0 017 7zm-2 0a5 5 0 00-5-5H8a5 5 0 000 10h6v2.28L19 15a4.79 4.79 0 002-4z"></path>
                    </svg>
                    <div class="font-medium">Comment</div>
                </div>

                <template v-if="options.visibility === 'PUBLIC'">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" data-supported-dps="24x24" fill="currentColor">
                            <path d="M13.96 5H6c-.55 0-1 .45-1 1v10H3V6c0-1.66 1.34-3 3-3h7.96L12 0h2.37L17 4l-2.63 4H12l1.96-3zm5.54 3H19v10c0 .55-.45 1-1 1h-7.96L12 16H9.63L7 20l2.63 4H12l-1.96-3H18c1.66 0 3-1.34 3-3V8h-1.5z"></path>
                        </svg>
                        <div class="font-medium">Repost</div>
                    </div>

                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" data-supported-dps="24x24" fill="currentColor">
                            <path d="M21 3L0 10l7.66 4.26L16 8l-6.26 8.34L14 24l7-21z"></path>
                        </svg>
                        <div class="font-medium">{{$t('service.linkedin.send')}}</div>
                    </div>
                </template>
            </div>
        </div>
    </Panel>
</template>
