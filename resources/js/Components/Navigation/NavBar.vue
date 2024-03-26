<script setup>
import {inject} from "vue";
import MenuIcon from "@/Icons/Menu.vue"

const appCtx = inject('appCtx');

const toggleAside = () => {
    appCtx.showAside = !appCtx.showAside;
}
</script>
<template>
    <nav
        class="flex items-center justify-between xl:hidden row-px bg-white border-b border-gray-200 shadow-mix h-12 p-2">
        <button @click="toggleAside" class="outline-none">
            <MenuIcon/>
        </button>
        <div id="navRightButton" class="flex items-center"></div>
    </nav>
</template>
