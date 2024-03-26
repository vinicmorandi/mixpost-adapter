<script setup>
import {ref} from "vue";
import {router, useForm} from "@inertiajs/vue3";
import useAuth from "@/Composables/useAuth";
import Panel from "@/Components/Surface/Panel.vue";
import Table from "@/Components/DataDisplay/Table.vue";
import TableRow from "@/Components/DataDisplay/TableRow.vue";
import TableCell from "@/Components/DataDisplay/TableCell.vue";
import Avatar from "@/Components/DataDisplay/Avatar.vue";
import Badge from "@/Components/DataDisplay/Badge.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import PlusIcon from "@/Icons/Plus.vue";
import DialogModal from "@/Components/Modal/DialogModal.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import Select from "@/Components/Form/Select.vue";
import Dropdown from "@/Components/Dropdown/Dropdown.vue";
import PureButton from "@/Components/Button/PureButton.vue";
import DropdownItem from "@/Components/Dropdown/DropdownItem.vue";
import Trash from "@/Icons/Trash.vue";
import PencilSquare from "@/Icons/PencilSquare.vue";
import EllipsisVertical from "@/Icons/EllipsisVertical.vue";
import UserRole from "@/Components/Workspace/UserRole.vue";
import Error from "../Form/Error.vue";

const props = defineProps({
    workspace: {
        type: Object,
        required: true,
    }
})

const {user: authUser} = useAuth();

// Store User
const storeUserModel = ref(false);

const formStoreUser = useForm({
    user_id: 0,
    role: 'admin'
});

const storeUser = () => {
    formStoreUser.post(route('mixpost.workspaces.users.store', {workspace: props.workspace.uuid}), {
            preserveScroll: true,
            onSuccess() {
                storeUserModel.value = false;
                formStoreUser.reset();
            }
        }
    );
}

// Edit user role
const editUserModal = ref(false);

const formEditUser = useForm({
    user_id: 0,
    user_name: '',
    role: 'admin'
});

const openEditUserModal = (user) => {
    editUserModal.value = true;

    formEditUser.user_id = user.id;
    formEditUser.user_name = user.name;
    formEditUser.role = user.role;
}

const updateUserRole = () => {
    formEditUser.put(route('mixpost.workspaces.users.update', {workspace: props.workspace.uuid}), {
            preserveScroll: true,
            onSuccess() {
                editUserModal.value = false;
                formEditUser.reset();
            }
        }
    );
}

// Delete user
const deleteUser = (userId) => {
    router.delete(route('mixpost.workspaces.users.delete', {workspace: props.workspace.uuid}), {
            data: {
                user_id: userId
            },
            preserveScroll: true
        }
    );
}
</script>
<template>
    <Panel>
        <template #title> {{ $t('team.team') }}</template>
        <template #action>
            <SecondaryButton @click="storeUserModel = true" size="md">
                <PlusIcon class="!w-5 !h-5 mr-xs"/>
                {{ $t('team.add_user') }}
            </SecondaryButton>
        </template>
        <Table>
            <template #body>
                <template v-for="user in workspace.users" :key="user.id">
                    <TableRow :hoverable="true">
                        <TableCell>
                            <div class="flex items-center">
                                <Avatar :name="user.name" class="mr-xs"/>
                                <div>
                                    <div>{{ user.name }}</div>
                                    <div>{{ user.email }}</div>
                                </div>
                            </div>
                        </TableCell>
                        <TableCell>
                            <Badge><span class="capitalize">{{ user.role }}</span></Badge>
                        </TableCell>
                        <TableCell>
                            <template v-if="user.id !== authUser.id">
                                <Dropdown width-classes="w-32" placement="bottom-end">
                                    <template #trigger>
                                        <PureButton class="mt-1">
                                            <EllipsisVertical/>
                                        </PureButton>
                                    </template>

                                    <template #content>
                                        <DropdownItem @click="openEditUserModal(user)" as="button">
                                            <PencilSquare class="!w-5 !h-5 mr-1"/>
                                            {{ $t('team.edit_role') }}
                                        </DropdownItem>

                                        <DropdownItem @click="deleteUser(user.id)" as="button">
                                            <Trash class="!w-5 !h-5 mr-1 text-red-500"/>
                                            {{ $t('general.delete') }}
                                        </DropdownItem>
                                    </template>
                                </Dropdown>
                            </template>
                        </TableCell>
                    </TableRow>
                </template>
            </template>
        </Table>
    </Panel>

    <DialogModal :show="storeUserModel" max-width="md" @close="storeUserModel = false">
        <template #header>
            {{ $t('team.add_user_workspace') }}
        </template>
        <template #body>
            <div class="mt-sm">
                <Select v-model="formStoreUser.user_id">
                    <option :value="0">Select user</option>
                    <template v-for="(user, index) in $page.props.users" :key="index">
                        <option :value="user.id">{{ user.name }}</option>
                    </template>
                </Select>

                <Error v-if="formStoreUser.errors.user_id" :message="formStoreUser.errors.user_id" class="mt-xs"/>

                <UserRole v-model="formStoreUser.role" class="mt-lg"/>
            </div>
        </template>
        <template #footer>
            <SecondaryButton @click="storeUserModel = false" class="mr-xs">{{ $t('general.cancel') }}</SecondaryButton>
            <PrimaryButton @click="storeUser" :disabled="formStoreUser.processing || !$page.props.users.length">
                {{ $t('general.add') }}
            </PrimaryButton>
        </template>
    </DialogModal>

    <DialogModal :show="editUserModal" max-width="md" @close="editUserModal = false">
        <template #header>
            {{ $t('team.edit_role_for') }} <span class="font-medium">{{ formEditUser.user_name }}</span>
        </template>
        <template #body>
            <div class="mt-sm">
                <UserRole v-model="formEditUser.role"/>
            </div>
        </template>
        <template #footer>
            <SecondaryButton @click="editUserModal = false" class="mr-xs">{{ $t('general.cancel') }}</SecondaryButton>
            <PrimaryButton @click="updateUserRole" :disabled="formEditUser.processing">
                {{ $t('team.change_role') }}
            </PrimaryButton>
        </template>
    </DialogModal>
</template>
