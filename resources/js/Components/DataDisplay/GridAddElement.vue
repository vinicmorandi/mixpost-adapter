<script setup>
import PlusIcon from "@/Icons/Plus.vue";
</script>
<template>
    <span
        class="flex items-center justify-center h-full border border-primary-800 rounded-lg hover:border-primary-500 hover:text-primary-500 transition-colors ease-in-out duration-200">
         <span class="p-lg">
            <span class="flex flex-col justify-center items-center">
                <PlusIcon class="w-7 h-7"/>
                <span class="mt-xs text-lg">
                    <slot/>
                </span>
            </span>
        </span>
    </span>
</template>
