<script setup>
import {computed, inject, ref} from "vue";
import { useI18n } from "vue-i18n";
import {useForm} from "@inertiajs/vue3";
import useNotifications from "../../Composables/useNotifications";
import Select from "@/Components/Form/Select.vue";
import Input from "@/Components/Form/Input.vue";
import ProviderOptionWrap from "@/Components/ProviderVersionOptions/ProviderOptionWrap.vue";
import Label from "@/Components/Form/Label.vue";
import SecondaryButton from "../Button/SecondaryButton.vue";
import LabelSuffix from "../Form/LabelSuffix.vue";
import VerticalGroup from "../Layout/VerticalGroup.vue";
import PrimaryButton from "../Button/PrimaryButton.vue";
import DialogModal from "../Modal/DialogModal.vue";
import Error from "../Form/Error.vue";

const { t: $t } = useI18n()

const workspaceCtx = inject('workspaceCtx');

const props = defineProps(['options', 'accounts', 'activeVersion'])

const {notify} = useNotifications();

const availableAccounts = computed(() => {
    if (props.activeVersion === 0) {
        return props.accounts;
    }

    return props.accounts.filter(account => account.id === props.activeVersion);
})

const addBoardModal = ref(false);
const boardForm = useForm({
    name: '',
    account_uuid: null,
});

const openBoardModal = (account) => {
    boardForm.account_uuid = account.uuid;
    addBoardModal.value = true;
}

const closeBoardModal = () => {
    addBoardModal.value = false;
}

const storeBoard = () => {
    boardForm.clearErrors();

    boardForm.post(route('mixpost.provider.pinterest.storeBoard', {workspace: workspaceCtx.id}), {
        preserveScroll: true,
        onSuccess() {
            notify('success', $t('service.pinterest.board_created'))
            closeBoardModal();
            boardForm.reset();
        },
        onError() {
            notify('error', $t('service.pinterest.board_not_added'))
        }
    })
}
</script>
<template>

    <ProviderOptionWrap :title="$t('service.provider_options', {provider: 'Pinterest'})" provider="pinterest">
        <div class="w-full">
            <Input v-model="options.title" type="text" :placeholder="$t('general.title')" id="p_title"/>
        </div>

        <div class="w-full mt-xs">
            <Input v-model="options.link" type="text" :placeholder="$t('general.link')" id="p_link"/>
        </div>

        <div class="mt-xs">
            <div v-for="account in availableAccounts" :key="account.id" class="w-full mt-xs">
                <Label>
                    <span v-html="$t('service.pinterest.select_board_for', {account: account.name})"/>
                    <LabelSuffix :danger="true">*</LabelSuffix>
                </Label>

                <div class="flex items-center">
                    <div class="mr-xs">
                        <!--Every pinterest account should have chosen a board-->
                        <Select v-model="options.boards[`account-${account.id}`]">
                            <option v-for="board in account.relationships.boards" :value="board.id">
                                {{ board.name }}
                            </option>
                        </Select>
                    </div>

                    <div class="w-auto">
                        <SecondaryButton @click="openBoardModal(account)">
                            {{ $t('service.pinterest.create_board') }}
                        </SecondaryButton>
                    </div>
                </div>
            </div>
        </div>
    </ProviderOptionWrap>

    <DialogModal :show="addBoardModal"
                 :closeable="true"
                 max-width="sm"
                 @close="addBoardModal = false">
        <template #header>
            {{ $t('service.pinterest.create_board') }}
        </template>

        <template #body>
            <VerticalGroup v-if="addBoardModal">
                <template #title>
                    <label for="board_name">{{ $t('service.pinterest.board_name') }}
                        <LabelSuffix :danger="true">*</LabelSuffix>
                    </label>
                </template>
                <Input type="text" v-model="boardForm.name" :error="boardForm.errors.name" id="board_name" required
                       autofocus/>
                <template #footer>
                    <Error :message="boardForm.errors.name"/>
                </template>
            </VerticalGroup>
        </template>

        <template #footer>
            <PrimaryButton @click="storeBoard"
                           :isLoading="boardForm.processing"
                           :disabled="boardForm.processing"
                           class="mr-xs"> {{ $t('service.pinterest.create_board') }}
            </PrimaryButton>
            <SecondaryButton @click="closeBoardModal">{{ $t('general.close') }}</SecondaryButton>
        </template>
    </DialogModal>
</template>
