<script setup>
import {useI18n} from "vue-i18n";
import {Head, Link} from '@inertiajs/vue3';
import AdminLayout from "@/Layouts/Admin.vue";
import PageHeader from "@/Components/DataDisplay/PageHeader.vue";
import Panel from "../../Components/Surface/Panel.vue";
import Plus from "../../Icons/Plus.vue";
import AlertUnconfiguredService from "../../Components/Service/AlertUnconfiguredService.vue";

defineOptions({layout: AdminLayout});

const {t: $t} = useI18n()

const pageTitle = $t("dashboard.dashboard");
</script>
<template>
    <Head :title="pageTitle"/>

    <div class="w-full mx-auto m-container row-py">
        <PageHeader :title="pageTitle"/>

        <div class="mt-lg row-px w-full">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-sm">
                <div>
                    <Link :href="route('mixpost.users.index')" class="group">
                        <Panel>
                            <template #title>{{ $t("user.users") }}</template>
                            <div class="font-bold text-primary-500 text-2xl">{{ $page.props.count.users }}</div>
                        </Panel>
                    </Link>

                    <Link :href="route('mixpost.users.create')" class="mt-lg block group">
                        <Panel class="link">
                            <div class="flex items-center">
                                <div class="mr-xs"><Plus/></div>
                                <div class="font-semibold text-lg">{{ $t("user.create_user") }}</div>
                            </div>
                        </Panel>
                    </Link>
                </div>

                <div>
                    <Link :href="route('mixpost.workspaces.index')" class="group">
                        <Panel>
                            <template #title>{{ $t("workspace.workspaces") }}</template>
                            <div class="font-bold text-primary-500 text-2xl">{{ $page.props.count.workspaces }}</div>
                        </Panel>
                    </Link>

                    <Link :href="route('mixpost.workspaces.create')" class="mt-lg block group">
                        <Panel class="link">
                            <div class="flex items-center">
                                <div class="mr-xs"><Plus/></div>
                                <div class="font-semibold text-lg">{{ $t("workspace.create_workspace") }}</div>
                            </div>
                        </Panel>
                    </Link>
                </div>
            </div>
        </div>

        <AlertUnconfiguredService :isConfigured="$page.props.is_configured_service" class="mt-lg row-px w-full"/>
    </div>
</template>
