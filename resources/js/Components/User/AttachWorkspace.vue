<script setup>
import {inject, ref} from "vue";
import {useForm} from "@inertiajs/vue3";
import DialogModal from "../Modal/DialogModal.vue";
import PrimaryButton from "../Button/PrimaryButton.vue";
import SecondaryButton from "../Button/SecondaryButton.vue";
import UserRole from "../Workspace/UserRole.vue";
import Plus from "../../Icons/Plus.vue";
import SelectWorkspace from "../Workspace/SelectWorkspace.vue";

const routePrefix = inject('routePrefix');

const props = defineProps({
    user: {
        type: Object
    },
    attachedWorkspaces: {
        type: Array,
        default: []
    }
})

const selectedWorkspace = ref(null);

const formAttach = useForm({
    user_id: props.user.id,
    role: 'admin'
});

const attach = () => {
    formAttach.post(route('mixpost.workspaces.users.store', {workspace: selectedWorkspace.value.key}), {
            preserveScroll: true,
            onSuccess() {
                close();
                formAttach.reset();
            }
        }
    );
}

const modal = ref(false);

const open = () => {
    modal.value = true;
}

const close = () => {
    modal.value = false;
    selectedWorkspace.value = null;
}
</script>
<template>
    <SecondaryButton @click="open" size="sm">
        <Plus class="mr-xs"/>
        {{ $t('team.attach') }}
    </SecondaryButton>

    <DialogModal :show="modal"
                 max-width="md"
                 :scrollable-body="true"
                 :closeable="true"
                 @close="close">
        <template #header>
            {{ $t('team.attach_workspace') }}
        </template>

        <template #body>
            <SelectWorkspace v-model="selectedWorkspace" :exclude="attachedWorkspaces"/>
            <UserRole v-model="formAttach.role" class="mt-lg"/>
        </template>

        <template #footer>
            <SecondaryButton @click="close" class="mr-xs">{{ $t('general.cancel') }}</SecondaryButton>
            <PrimaryButton @click="attach" :disabled="!selectedWorkspace || formAttach.processing"
                           :isLoading="formAttach.processing"> {{ $t('team.attach') }}
            </PrimaryButton>
        </template>
    </DialogModal>
</template>
