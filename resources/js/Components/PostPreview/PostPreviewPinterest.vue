<script setup>
import {computed} from "vue";
import Panel from "@/Components/Surface/Panel.vue";
import Gallery from "@/Components/ProviderGallery/Pinterest/PinterestGallery.vue"

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
        <div class="w-full">
            <Gallery :media="mainContent.media"/>

            <div class="px-sm">
                <div v-if="options.title" class="mt-md font-medium">{{ options.title }}</div>

                <div class="flex items-center mt-md">
                    <div class="mr-sm">
                    <span class="inline-flex justify-center items-center flex-shrink-0 w-10 h-10 rounded-full">
                        <img :src="image"
                             class="object-cover w-full h-full rounded-full" alt=""/>
                    </span>
                    </div>

                    <div>{{ username }}</div>
                </div>
            </div>
        </div>
    </Panel>
</template>
