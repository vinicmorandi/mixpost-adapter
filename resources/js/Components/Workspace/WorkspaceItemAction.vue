<script setup>
import {inject, ref} from "vue";
import {useI18n} from "vue-i18n";
import emitter from "@/Services/emitter";
import {router} from "@inertiajs/vue3";
import useAuth from "@/Composables/useAuth";
import useNotifications from "@/Composables/useNotifications";
import PureButtonLink from "@/Components/Button/PureButtonLink.vue";
import Eye from "../../Icons/Eye.vue";
import PencilSquare from "../../Icons/PencilSquare.vue";
import Dropdown from "../Dropdown/Dropdown.vue";
import Trash from "../../Icons/Trash.vue";
import DropdownItem from "../Dropdown/DropdownItem.vue";
import DropdownButton from "../Dropdown/DropdownButton.vue";
import ArrowTopRightOnSquare from "../../Icons/ArrowTopRightOnSquare.vue";

const {t: $t} = useI18n()

const routePrefix = inject('routePrefix');
const confirmation = inject('confirmation');

const props = defineProps({
    itemId: {
        type: String,
        required: true,
    }
})

const emit = defineEmits(['onDelete'])

const {notify} = useNotifications();
const {user} = useAuth();

const confirmationDeletion = ref(false);

const deleteWorkspace = () => {
    confirmation()
        .title($t('workspace.delete_workspace'))
        .description($t('workspace.confirm_delete_workspace'))
        .destructive()
        .onConfirm((dialog) => {
            dialog.isLoading(true);

            router.delete(route(`${routePrefix}.workspaces.delete`, {
                workspace: props.itemId,
            }), {
                onSuccess() {
                    confirmationDeletion.value = false;
                    notify('success', $t('workspace.workspace_deleted'))
                    emit('onDelete')
                    emitter.emit('workspaceDelete', props.itemId);
                    dialog.reset();
                },
                onFinish() {
                    dialog.isLoading(false)
                }
            })
        })
        .show();

}
</script>
<template>
    <div>
        <div class="flex flex-row items-center justify-end gap-xs">
            <PureButtonLink :href="route('mixpost.switchWorkspace', {workspace: itemId, redirect: true})"
                            method="post"
                            as="button"
                            type="button"
                            v-tooltip="$t('general.open')">
                <ArrowTopRightOnSquare/>
            </PureButtonLink>

            <PureButtonLink :href="route(`${routePrefix}.workspaces.view`, {workspace: itemId})"
                            v-tooltip="$t('general.view')">
                <Eye/>
            </PureButtonLink>

            <Dropdown width-classes="w-32" placement="bottom-end">
                <template #trigger>
                    <DropdownButton/>
                </template>

                <template #content>
                    <DropdownItem :href="route(`${routePrefix}.workspaces.edit`, {workspace: itemId})">
                        <PencilSquare class="mr-xs"/>
                        {{ $t('general.edit') }}
                    </DropdownItem>

                    <DropdownItem @click="deleteWorkspace" as="button">
                        <Trash class="text-red-500 mr-xs"/>
                        {{ $t('general.delete') }}
                    </DropdownItem>
                </template>
            </Dropdown>
        </div>
    </div>
</template>
