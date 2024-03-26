<script setup>
import {useForm, router} from "@inertiajs/vue3";
import {inject, ref} from "vue";
import Badge from "../DataDisplay/Badge.vue";
import Dropdown from "../Dropdown/Dropdown.vue";
import PencilSquare from "../../Icons/PencilSquare.vue";
import Trash from "../../Icons/Trash.vue";
import DropdownItem from "../Dropdown/DropdownItem.vue";
import NoResult from "../Util/NoResult.vue";
import DropdownButton from "../Dropdown/DropdownButton.vue";
import AttachWorkspace from "./AttachWorkspace.vue";
import SecondaryButton from "../Button/SecondaryButton.vue";
import Panel from "../Surface/Panel.vue";
import Table from "@/Components/DataDisplay/Table.vue";
import TableRow from "@/Components/DataDisplay/TableRow.vue";
import TableCell from "@/Components/DataDisplay/TableCell.vue";
import Avatar from "../DataDisplay/Avatar.vue";
import UserRole from "../Workspace/UserRole.vue";
import DialogModal from "../Modal/DialogModal.vue";
import PrimaryButton from "../Button/PrimaryButton.vue";
import Eye from "../../Icons/Eye.vue";
import PureButtonLink from "../Button/PureButtonLink.vue";

const routePrefix = inject('routePrefix');
const confirmation = inject('confirmation');

const props = defineProps({
    user: {
        type: Object
    }
})

const modalRole = ref(null);

const formRole = useForm({
    user_id: props.user.id,
    role: 'admin'
});

const openModalRole = (workspace) => {
    formRole.role = workspace.pivot.role;
    modalRole.value = workspace;
}

const closeModalRole = () => {
    modalRole.value = null;
}

const detachWorkspace = (workspace) => {
    confirmation()
        .title($t("team.detach_workspace"))
        .description($t("team.detach_confirm", {'workspace': workspace.name, 'user': props.user.name}))
        .destructive()
        .btnConfirmName($t("admin.detach"))
        .onConfirm((dialog) => {
            dialog.isLoading(true);

            router.delete(route(`${routePrefix}.workspaces.users.delete`, {workspace: workspace.uuid}), {
                    data: {
                        user_id: props.user.id
                    },
                    preserveScroll: true,
                    onSuccess() {
                        dialog.reset();
                    },
                    onFinish() {
                        dialog.isLoading(false);
                    }
                }
            );
        })
        .show();
}

const updateRole = () => {
    formRole.put(route(`${routePrefix}.workspaces.users.update`, {workspace: modalRole.value.uuid}), {
            data: {
                user_id: props.user.id
            },
            preserveScroll: true,
            onSuccess() {
                formRole.reset();
                closeModalRole();
            }
        }
    );
}
</script>
<template>
    <Panel>
        <template #title>{{ $t("workspace.workspaces") }}</template>
        <template #action>
            <AttachWorkspace :user="user" :attachedWorkspaces="user.workspaces.map((item) => item.uuid)"/>
        </template>

        <template v-if="user.workspaces.length">
            <Table>
                <template #head>
                    <TableRow>
                        <TableCell component="th" scope="col"></TableCell>
                        <TableCell component="th" scope="col">{{ $t("general.name") }}</TableCell>
                        <TableCell component="th" scope="col">{{ $t("team.role") }}</TableCell>
                        <TableCell component="th" scope="col">{{ $t("team.attached_at") }}</TableCell>
                        <TableCell component="th" scope="col"/>
                    </TableRow>
                </template>
                <template #body>
                    <template v-for="workspace in user.workspaces" :key="workspace.id">
                        <TableRow :hoverable="true">
                            <TableCell align="left">
                                <div class="flex justify-start">
                                    <Avatar :backgroundColor="workspace.hex_color"
                                            :name="workspace.name"
                                            roundedClass="rounded-lg"
                                    />
                                </div>
                            </TableCell>

                            <TableCell>
                                <div>{{ workspace.name }}</div>
                            </TableCell>
                            <TableCell>
                                <Badge><span class="capitalize">{{ workspace.pivot.role }}</span></Badge>
                            </TableCell>
                            <TableCell>
                                {{ workspace.pivot.joined_at }}
                            </TableCell>
                            <TableCell align="right">
                                <div class="flex justify-end">
                                    <PureButtonLink
                                        :href="route(`${routePrefix}.workspaces.view`, {workspace: workspace.uuid})"
                                        v-tooltip="$t('general.view')" class="mr-xs">
                                        <Eye/>
                                    </PureButtonLink>

                                    <Dropdown width-classes="w-32" placement="bottom-end">
                                        <template #trigger>
                                            <DropdownButton/>
                                        </template>

                                        <template #content>
                                            <DropdownItem @click="openModalRole(workspace)" as="button">
                                                <PencilSquare class="mr-xs"/>
                                                {{ $t('team.edit_role') }}
                                            </DropdownItem>

                                            <DropdownItem @click="detachWorkspace(workspace)" as="button">
                                                <Trash class="text-red-500 mr-xs"/>
                                                {{ $t('general.detach') }}
                                            </DropdownItem>
                                        </template>
                                    </Dropdown>
                                </div>
                            </TableCell>
                        </TableRow>
                    </template>
                </template>
            </Table>
        </template>

        <template v-else>
            <NoResult/>
        </template>
    </Panel>

    <DialogModal :show="modalRole !== null"
                 max-width="md"
                 :scrollable-body="true"
                 :closeable="true"
                 @close="closeModalRole">
        <template #header>
            {{ $t('team.edit_role_on') }} <strong>{{ modalRole.name }}</strong> {{ $t('workspace.workspaces') }}
        </template>

        <template #body>
            <UserRole v-model="formRole.role" class="mt-lg"/>
        </template>

        <template #footer>
            <SecondaryButton @click="closeModalRole" class="mr-xs"> {{ $t('general.cancel') }}</SecondaryButton>
            <PrimaryButton @click="updateRole" :disabled="formRole.processing" :isLoading="formRole.processing">
                {{ $t('general.update') }}
            </PrimaryButton>
        </template>
    </DialogModal>
</template>
