<script setup>
import AspectRatioBox from "@/Components/Layout/AspectRatioBox.vue";
import Box from "@/Components/Layout/Box.vue";
import CoverStyle from "@/Components/DataDisplay/CoverStyle.vue";

defineProps({
    media: {
        type: Array,
        required: true
    }
})
</script>
<template>
    <div class="flex flex-col gap-1">
        <div class="w-full h-full flex grow-0 gap-1">
            <Box v-for="item in media.slice(0, 1)" :key="item.id">
                <AspectRatioBox :ratio="1.8">
                    <CoverStyle :src="item.thumb_url"/>
                </AspectRatioBox>
            </Box>
        </div>
        <div class="w-full flex grow-0 gap-1">
            <Box v-for="(item, index) in media.slice(1, 4)">
                <AspectRatioBox :ratio="1.5">
                    <CoverStyle :src="item.thumb_url"/>
                </AspectRatioBox>
                <div v-if="index === 2 && media.length > 3" class="absolute top-0 right-0 bottom-0 left-0 flex items-center justify-center bg-black bg-opacity-60">
                    <span class="font-medium text-3xl text-white">+{{ media.length - 4 }}</span>
                </div>
            </Box>
        </div>
    </div>
</template>
