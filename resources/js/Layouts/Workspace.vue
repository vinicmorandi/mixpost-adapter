<script setup>
import {provide, reactive} from "vue";
import {usePage} from "@inertiajs/vue3";
import DefaultLayout from "@/Layouts/Default.vue";
import SidebarWorkspace from "@/Components/Sidebar/SidebarWorkspace.vue";
import Navigation from "@/Components/Navigation/NavBar.vue";

const context = reactive({
    id: null,
    dashboard_filter: {
        account_id: null,
        period: '30_days'
    }
});

provide('workspaceCtx', context);

context.id = usePage().props.ziggy.workspace_id
</script>
<template>
    <DefaultLayout>
        <template #sidebar>
            <SidebarWorkspace/>
        </template>

        <template #navigation>
            <Navigation/>
        </template>

        <slot/>
    </DefaultLayout>
</template>

