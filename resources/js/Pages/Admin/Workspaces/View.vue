<script setup>
import {inject} from "vue";
import {Head, Link, router} from '@inertiajs/vue3';
import { useI18n } from "vue-i18n";
import useNotifications from "../../../Composables/useNotifications";
import AdminLayout from "@/Layouts/Admin.vue";
import PageHeader from "@/Components/DataDisplay/PageHeader.vue";
import Panel from "@/Components/Surface/Panel.vue";
import HorizontalGroup from "@/Components/Layout/HorizontalGroup.vue";
import PrimaryButton from "../../../Components/Button/PrimaryButton.vue";
import DangerButton from "../../../Components/Button/DangerButton.vue";
import WorkspaceUsers from "../../../Components/Workspace/WorkspaceUsers.vue";
import PencilSquare from "../../../Icons/PencilSquare.vue";
import Trash from "../../../Icons/Trash.vue";
import ArrowTopRightOnSquare from "../../../Icons/ArrowTopRightOnSquare.vue";
import SecondaryButton from "../../../Components/Button/SecondaryButton.vue";

defineOptions({layout: AdminLayout});

const { t: $t } = useI18n()

const routePrefix = inject('routePrefix');
const confirmation = inject('confirmation');

const props = defineProps({
    workspace: {
        type: Object
    }
})

const pageTitle = $t("workspace.view_workspace");

const {notify} = useNotifications();
const deleteWorkspace = () => {
    confirmation()
        .title($t("workspace.delete_workspace"))
        .description($t("workspace.confirm_delete_workspace"))
        .destructive()
        .onConfirm((dialog) => {
            dialog.isLoading(true);

            router.delete(route(`${routePrefix}.workspaces.delete`, {workspace: props.workspace.uuid}), {
                onSuccess() {
                    notify('success', $t('workspace.workspace_deleted'));
                }
            })
        })
        .show();

}
</script>
<template>
    <Head :title="pageTitle"/>

    <div class="w-full mx-auto max-w-6xl row-py">
        <PageHeader :title="pageTitle">
            <template #description>
                {{ workspace.name }}
            </template>
            <div class="flex items-center">
                <Link :href="route('mixpost.switchWorkspace', {workspace: workspace.uuid, redirect: true})"
                      method="post"
                      as="button"
                      type="button"
                      class="mr-xs">
                    <SecondaryButton size="sm">
                        <template #icon>
                            <ArrowTopRightOnSquare/>
                        </template>
                        {{ $t("general.open") }}
                    </SecondaryButton>
                </Link>

                <Link :href="route(`${routePrefix}.workspaces.edit`, {workspace: workspace.uuid})" class="mr-xs">
                    <PrimaryButton size="sm"><template #icon><PencilSquare/></template> {{ $t("general.edit") }}</PrimaryButton>
                </Link>

                <DangerButton @click="deleteWorkspace" size="sm"><template #icon><Trash/></template></DangerButton>
            </div>
        </PageHeader>

        <div class="row-px">
            <Panel>
                <template #title>{{ $t("general.details") }}</template>

                <div class="md:max-w-2xl">
                    <HorizontalGroup>
                        <template #title>
                            {{ $t("general.name") }}
                        </template>

                        <div>{{ workspace.name }}</div>
                    </HorizontalGroup>

                    <HorizontalGroup class="mt-lg">
                        <template #title>
                            {{ $t("general.color") }}
                        </template>

                        <div :style="{'background': workspace.hex_color}"
                             class="w-xl h-xl rounded-md"/>
                    </HorizontalGroup>

                </div>
            </Panel>

            <div class="mt-lg">
                <WorkspaceUsers :workspace="workspace"/>
            </div>
        </div>
    </div>
</template>
