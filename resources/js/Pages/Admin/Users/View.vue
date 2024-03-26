<script setup>
import {inject} from "vue";
import { useI18n } from "vue-i18n";
import {Head, Link, router} from '@inertiajs/vue3';
import useNotifications from "../../../Composables/useNotifications";
import useAuth from "../../../Composables/useAuth";
import AdminLayout from "@/Layouts/Admin.vue";
import PageHeader from "@/Components/DataDisplay/PageHeader.vue";
import Panel from "@/Components/Surface/Panel.vue";
import HorizontalGroup from "@/Components/Layout/HorizontalGroup.vue";
import PrimaryButton from "../../../Components/Button/PrimaryButton.vue";
import DangerButton from "../../../Components/Button/DangerButton.vue";
import UserWorkspaces from "../../../Components/User/UserWorkspaces.vue";
import PencilSquare from "../../../Icons/PencilSquare.vue";
import Trash from "../../../Icons/Trash.vue";

defineOptions({layout: AdminLayout});

const { t: $t } = useI18n()

const routePrefix = inject('routePrefix');
const confirmation = inject('confirmation');

const props = defineProps({
    user: {
        type: Object
    }
})

const pageTitle = $t("user.view_user");

const {notify} = useNotifications();
const {user: authUser} = useAuth();

const deleteUser = () => {
    confirmation()
        .title($t("user.delete_users"))
        .description($t("user.confirm_delete_user"))
        .destructive()
        .onConfirm((dialog) => {
            dialog.isLoading(true);

            router.delete(route(`${routePrefix}.users.delete`, {user: props.user.id}), {
                onSuccess(response) {
                    if (!response.props.flash.error) {
                        notify('success', $t('user.user_deleted'));
                    }
                },
                onFinish() {
                    dialog.isLoading(false);
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
                {{ user.name }}
            </template>

            <div class="flex items-center">
                <Link :href="route(`${routePrefix}.users.edit`, {user: user.id})" class="mr-xs">
                    <PrimaryButton size="sm">
                        <template #icon>
                            <PencilSquare/>
                        </template>
                        {{ $t("general.edit") }}
                    </PrimaryButton>
                </Link>

                <template v-if="authUser.id !== user.id">
                    <DangerButton @click="deleteUser" size="sm">
                        <template #icon>
                            <Trash/>
                        </template>
                    </DangerButton>
                </template>
            </div>
        </PageHeader>

        <div class="row-px">
            <Panel>
                <template #title>{{ $t("user.user_details") }}</template>

                <div class="md:max-w-2xl">
                    <HorizontalGroup>
                        <template #title>
                            {{ $t("general.name") }}
                        </template>

                        {{ user.name }}
                    </HorizontalGroup>

                    <HorizontalGroup class="mt-md">
                        <template #title>
                            {{ $t("general.email") }}
                        </template>

                        {{ user.email }}
                    </HorizontalGroup>

                    <HorizontalGroup class="mt-md">
                        <template #title>
                            {{ $t("general.created_at") }}
                        </template>

                        {{ user.created_at }}
                    </HorizontalGroup>

                    <HorizontalGroup class="mt-md">
                        <template #title>
                            {{ $t("user.system_admin") }}
                        </template>

                        {{ user.is_admin ? $t("general.yes") : $t("general.no") }}
                    </HorizontalGroup>
                </div>
            </Panel>

            <div class="mt-lg">
                <UserWorkspaces :user="user"/>
            </div>
        </div>
    </div>
</template>
