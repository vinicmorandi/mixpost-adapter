<script setup>
import {computed, inject} from "vue";
import useEditor from "@/Composables/useEditor";
import Gallery from "@/Components/ProviderGallery/Instagram/InstagramGallery.vue"
import EditorReadOnly from "@/Components/Package/EditorReadOnly.vue";
import Comments from "./Icons/Comments.vue";
import Share from "./Icons/Share.vue";
import Likes from "./Icons/Likes.vue";
import Bookmark from "./Icons/Bookmark.vue";

const data = inject('instagramCtx');

const {isDocEmpty} = useEditor();

const formattedBody = computed(() => {
    const string = data.content[0].body;

    if (isDocEmpty(string)) {
        return '';
    }

    if (!string.includes('<div>')) {
        return `<div><instagram_username>${data.name}</instagram_username> ${string}</div>`;
    }

    return string.replace(/<div>/, `<div><instagram_username>${data.name}</instagram_username> `);
})

const slidersLength = computed(() => {
    return data.content[0].media.length;
});

const sliders = computed(() => {
    return Array.from({length: slidersLength.value}).splice(0, 6);
});

const lastSlideIndex = computed(() => {
    return sliders.value.length - 1;
});
</script>
<template>
    <div class="border border-gray-200 rounded py-sm">
        <div class="flex items-center justify-between px-sm">
            <div class="flex items-center">
                        <span
                            class="inline-flex justify-center items-center flex-shrink-0 w-10 h-10 rounded-full mr-sm">
                            <img :src="data.image"
                                 class="object-cover w-full h-full rounded-full" alt=""/>
                        </span>
                <div class="flex flex-col">
                    <div class="font-medium mr-xs">{{ data.name }}</div>
                </div>
            </div>

            <svg class="w-6 h-6 rotate-90" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="1.5"></circle>
                <circle cx="6" cy="12" r="1.5"></circle>
                <circle cx="18" cy="12" r="1.5"></circle>
            </svg>
        </div>

        <div class="w-full pb-xs">
            <Gallery :media="data.content[0].media" :options="data.options" :class="{'mt-sm': slidersLength}"/>

            <div class="px-sm relative">
                <div class="mt-xs flex items-center justify-between text-black py-xs">
                    <div class="flex items-center space-x-md">
                        <Likes/>
                        <Comments/>
                        <Share/>
                    </div>

                    <div class="absolute w-full">
                        <div class="flex w-full justify-center">
                            <div v-if="slidersLength > 1" class="flex items-center space-x-1">
                                <template
                                    v-for="(_, index) in sliders">
                                    <div class="w-1.5 h-1.5 flex rounded-full"
                                         :class="{'bg-gray-500': index > 0,
                                      'bg-blue-500': index === 0,
                                      '!w-1 !h-1': slidersLength > 6 && index === lastSlideIndex}">
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div>
                        <Bookmark/>
                    </div>
                </div>

                <EditorReadOnly :value="formattedBody"/>
            </div>
        </div>
    </div>
</template>
