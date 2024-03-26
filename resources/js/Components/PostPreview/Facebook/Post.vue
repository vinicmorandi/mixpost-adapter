<script setup>
import {inject} from "vue";
import useEditor from "@/Composables/useEditor";
import Gallery from "@/Components/ProviderGallery/Facebook/FacebookGallery.vue"
import EditorReadOnly from "@/Components/Package/EditorReadOnly.vue";

import fbIconsImgUrl from "@img/fb-icons.png"

const {isDocEmpty} = useEditor();

const data = inject('facebookCtx');
</script>
<template>
    <div class="flex items-center">
            <span class="inline-flex justify-center items-center flex-shrink-0 w-10 h-10 rounded-full mr-sm">
                <img :src="data.image"
                     class="object-cover w-full h-full rounded-full" alt=""/>
            </span>
        <div class="flex flex-col">
            <div class="font-medium mr-xs">{{ data.name }}</div>
            <div class="text-gray-400 text-sm">19h</div>
        </div>
    </div>

    <div class="w-full">
        <EditorReadOnly :value="data.content[0].body"
                        :class="{'mt-xs': !isDocEmpty(data.content[0].body), 'mb-xs': data.content[0].media.length}"/>

        <Gallery :media="data.content[0].media"/>
    </div>

    <div class="mt-5 flex items-center justify-between">
        <div v-tooltip="$t('service.representative_data')" class="flex items-center">
            <div class="flex mr-xs">
                <img src="@img/fb-like.svg" class="w-5 h-5 z-10" alt=""/>
                <img src="@img/fb-wow.svg" class="w-5 h-5 -ml-1" alt=""/>
            </div>
            <div class="text-gray-500">116</div>
        </div>
        <div v-tooltip="$t('service.representative_data')" class="text-gray-500">0 comments</div>
    </div>

    <div class="mt-5 flex items-center justify-around border-t border-b border-gray-200 text-gray-500 py-2">
        <div class="flex items-center">
            <i :style="{backgroundImage: `url(${fbIconsImgUrl})`, backgroundPosition: '0px -342px', backgroundSize: '25px 867px'}"
               class="facebook-toolbar-icon"></i>
            <span class="ml-1 font-semibold">Like</span>
        </div>
        <div class="flex items-center">
            <i :style="{backgroundImage: `url(${fbIconsImgUrl})`, backgroundPosition: '0px -304px', backgroundSize: '25px 867px'}"
               class="facebook-toolbar-icon"></i>
            <span class="ml-1 font-semibold">Comment</span>
        </div>
        <div class="flex items-center">
            <i :style="{backgroundImage: `url(${fbIconsImgUrl})`, backgroundPosition: '0px -361px', backgroundSize: '25px 867px'}"
               class="facebook-toolbar-icon"></i>
            <span class="ml-1 font-semibold">Share</span>
        </div>
    </div>
</template>
<style>
.facebook-toolbar-icon {
    @apply inline-block w-5 h-5 bg-no-repeat;
}
</style>
