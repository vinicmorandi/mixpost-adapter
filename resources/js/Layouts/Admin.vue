<script setup>
import DefaultLayout from "@/Layouts/Default.vue";
import SidebarAdmin from "@/Components/Sidebar/SidebarAdmin.vue";
import Navigation from "@/Components/Navigation/NavBar.vue";
</script>
<template>
    <DefaultLayout>
        <template #sidebar>
            <SidebarAdmin/>
        </template>

        <template #navigation>
            <Navigation/>
        </template>

        <slot/>
    </DefaultLayout>
</template>

