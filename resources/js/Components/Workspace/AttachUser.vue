<script setup>
import {inject, ref} from "vue";
import { useI18n } from "vue-i18n";
import {useForm} from "@inertiajs/vue3";
import useNotifications from "../../Composables/useNotifications";
import DialogModal from "../Modal/DialogModal.vue";
import PrimaryButton from "../Button/PrimaryButton.vue";
import SecondaryButton from "../Button/SecondaryButton.vue";
import UserRole from "../Workspace/UserRole.vue";
import Plus from "../../Icons/Plus.vue";
import SelectUser from "../User/SelectUser.vue";

const { t: $t } = useI18n()

const routePrefix = inject('routePrefix');
const {notify} = useNotifications();

const props = defineProps({
    workspace: {
        type: Object
    },
    attachedUsers: {
        type: Array,
        default: []
    }
})

const selectedUser = ref(null);

const formAttach = useForm({
    user_id: null,
    role: 'admin'
});

const attach = () => {
    formAttach.transform((data) => {
        return {
            ...data,
            user_id: selectedUser.value.key,
        }
    }).post(route('mixpost.workspaces.users.store', {workspace: props.workspace.uuid}), {
            preserveScroll: true,
            onSuccess() {
                close();
                formAttach.reset();

                notify('success', $t('team.user_attached'))
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
    selectedUser.value = null;
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
            {{ $t('team.attach_user') }}
        </template>

        <template #body>
            <SelectUser v-model="selectedUser" :exclude="attachedUsers"/>
            <UserRole v-model="formAttach.role" class="mt-lg"/>
        </template>

        <template #footer>
            <SecondaryButton @click="close" class="mr-xs">{{ $t('general.cancel') }}</SecondaryButton>
            <PrimaryButton @click="attach" :disabled="!selectedUser || formAttach.processing"
                           :isLoading="formAttach.processing"> {{ $t('team.attach') }}
            </PrimaryButton>
        </template>
    </DialogModal>
</template>
