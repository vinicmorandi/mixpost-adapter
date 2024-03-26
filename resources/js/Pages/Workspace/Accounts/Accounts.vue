<script setup>
import {computed, inject, ref, toRaw, watch} from "vue";
import {router, useForm, usePage} from "@inertiajs/vue3";
import {Head} from "@inertiajs/vue3";
import {isEmpty} from "lodash";
import { useI18n } from "vue-i18n";
import useNotifications from "@/Composables/useNotifications";
import PageHeader from "@/Components/DataDisplay/PageHeader.vue";
import Panel from "@/Components/Surface/Panel.vue";
import Modal from "@/Components/Modal/Modal.vue";
import ConfirmationModal from "@/Components/Modal/ConfirmationModal.vue";
import Account from "@/Components/Account/Account.vue";
import AddTwitterAccount from "@/Components/Account/AddTwitterAccount.vue";
import AddFacebookPage from "@/Components/Account/AddFacebookPage.vue";
import AddFacebookGroup from "@/Components/Account/AddFacebookGroup.vue";
import AddMastodonAccount from "@/Components/Account/AddMastodonAccount.vue";
import AddInstagramAccount from "@/Components/Account/AddInstagramAccount.vue";
import AddYoutubeAccount from "@/Components/Account/AddYoutubeAccount.vue";
import AddPinterestAccount from "@/Components/Account/AddPinterestAccount.vue";
import AddLinkedinProfile from "@/Components/Account/AddLinkedinProfile.vue";
import AddLinkedinPage from "@/Components/Account/AddLinkedinPage.vue";
import AddTikTokAccount from "@/Components/Account/AddTikTokAccount.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import DangerButton from "@/Components/Button/DangerButton.vue";
import Dropdown from "@/Components/Dropdown/Dropdown.vue";
import DropdownItem from "@/Components/Dropdown/DropdownItem.vue";
import GridAddElement from "@/Components/DataDisplay/GridAddElement.vue";
import EllipsisVerticalIcon from "@/Icons/EllipsisVertical.vue";
import RefreshIcon from "@/Icons/Refresh.vue";
import TrashIcon from "@/Icons/Trash.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import PureButton from "@/Components/Button/PureButton.vue";
import PencilSquare from "../../../Icons/PencilSquare.vue";
import DialogModal from "../../../Components/Modal/DialogModal.vue";
import Input from "../../../Components/Form/Input.vue";
import AlertUnconfiguredService from "../../../Components/Service/AlertUnconfiguredService.vue";

const { t: $t } = useI18n()

const workspaceCtx = inject("workspaceCtx");

const title = $t("account.accounts");

const {notify} = useNotifications();

const addAccountModal = ref(false);
const confirmationAccountDeletion = ref(null);
const accountIsDeleting = ref(false);

const updateAccount = (accountUuid) => {
    router.put(
        route("mixpost.accounts.update", {
            workspace: workspaceCtx.id,
            account: accountUuid,
        }),
        {},
        {
            preserveScroll: true,
            onSuccess(response) {
                if (response.props.flash.error) {
                    return;
                }

                notify("success", $t("account.account_updated"));
            },
        }
    );
};

const deleteAccount = () => {
    router.delete(
        route("mixpost.accounts.delete", {
            workspace: workspaceCtx.id,
            account: confirmationAccountDeletion.value,
        }),
        {
            preserveScroll: true,
            onStart() {
                accountIsDeleting.value = true;
            },
            onSuccess() {
                confirmationAccountDeletion.value = null;
                notify("success", $t("account.account_deleted"));
            },
            onFinish() {
                accountIsDeleting.value = false;
            },
        }
    );
};

const closeConfirmationAccountDeletion = () => {
    if (accountIsDeleting.value) {
        return;
    }

    confirmationAccountDeletion.value = null;
};

const showSuffixForm = ref(false);
const suffixForm = useForm({
    uuid: null,
    name: "",
});

// Account Suffix
const openSuffixForm = (account) => {
    showSuffixForm.value = true;
    suffixForm.uuid = account.uuid;
    suffixForm.name = account.suffix;
};

const closeSuffixForm = () => {
    showSuffixForm.value = null;
    suffixForm.reset();
};

const updateSuffix = () => {
    suffixForm.put(
        route("mixpost.accounts.updateSuffix", {
            workspace: workspaceCtx.id,
            account: suffixForm.uuid,
        }),
        {
            onSuccess() {
                closeSuffixForm();
            },
        }
    );
};

const errors = computed(() => {
    return usePage().props.errors;
});

watch(
    () => errors.value,
    () => {
        if (!isEmpty(errors.value)) {
            notify("error", toRaw(errors.value));
        }
    }
);
</script>
<template>
    <Head :title="title"/>

    <div class="w-full max-w-6xl mx-auto row-py">
        <PageHeader :title="title">
            <template #description>
                Bananinnha
            </template>
        </PageHeader>

        <div class="mt-lg row-px w-full">
            <AlertUnconfiguredService
                :isConfigured="$page.props.is_configured_service"
            />

            <div
                class="w-full grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6"
            >
                <button @click="addAccountModal = true">
                    <GridAddElement>
                        {{ $t("account.add_account") }}
                    </GridAddElement>
                </button>
                <template
                    v-for="account in $page.props.accounts"
                    :key="account.id"
                >
                    <Panel class="relative">
                        <div class="flex flex-col justify-center relative">
                            <Account
                                size="lg"
                                :img-url="account.image"
                                :provider="account.provider"
                                :name="account.name"
                                :active="true"
                            />

                            <div
                                v-if="!account.authorized"
                                class="absolute top-0 left-0"
                            >
                                <div
                                    v-tooltip="$t('account.unauthorized')"
                                    class="w-md h-md bg-red-500 rounded-full"
                                ></div>
                            </div>

                            <div
                                class="mt-sm font-semibold text-center break-words"
                            >
                                {{ account.name }}
                            </div>
                            <div class="mt-1 text-center text-stone-800">
                                {{ $t("account.added") }}
                                {{ account.created_at }}
                            </div>
                        </div>

                        <div class="absolute top-0 right-0 mt-sm mr-sm">
                            <Dropdown width-classes="w-42">
                                <template #trigger>
                                    <PureButton>
                                        <EllipsisVerticalIcon/>
                                    </PureButton>
                                </template>

                                <template #content>
                                    <DropdownItem
                                        @click="updateAccount(account.uuid)"
                                        as="button"
                                    >
                                        <template #icon>
                                            <RefreshIcon/>
                                        </template>
                                        {{ $t("general.update") }}
                                    </DropdownItem>
                                    <DropdownItem
                                        @click="openSuffixForm(account)"
                                        as="button"
                                    >
                                        <template #icon>
                                            <PencilSquare/>
                                        </template>
                                        {{ $t("account.edit_suffix") }}
                                    </DropdownItem>
                                    <DropdownItem
                                        @click="
                                            confirmationAccountDeletion =
                                                account.uuid
                                        "
                                        as="button"
                                    >
                                        <template #icon>
                                            <TrashIcon class="text-red-500"/>
                                        </template>
                                        {{ $t("general.delete") }}
                                    </DropdownItem>
                                </template>
                            </Dropdown>
                        </div>
                    </Panel>
                </template>
            </div>
        </div>
    </div>

    <DialogModal
        :show="showSuffixForm"
        max-width="sm"
        :closeable="true"
        :scrollable-body="true"
        @close="closeSuffixForm"
    >
        <template #header>
            {{ $t("account.edit_account_suffix") }}
        </template>

        <template #body>
            <div class="mt-xs">
                <Input
                    type="text"
                    v-model="suffixForm.name"
                    :placeholder="$t('account.enter_suffix')"
                />
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="closeSuffixForm" class="mr-xs">{{
                    $t("general.close")
                }}
            </SecondaryButton>
            <PrimaryButton
                @click="updateSuffix"
                :isLoading="suffixForm.processing"
                :disabled="suffixForm.processing"
            >
                {{ $t("general.save") }}
            </PrimaryButton>
        </template>
    </DialogModal>

    <ConfirmationModal
        :show="confirmationAccountDeletion !== null"
        @close="closeConfirmationAccountDeletion"
        variant="danger"
    >
        <template #header>{{ $t("account.delete_account") }}</template>
        <template #body>
            {{ $t("account.confirm_delete_account") }}
        </template>
        <template #footer>
            <SecondaryButton
                @click="closeConfirmationAccountDeletion"
                :disabled="accountIsDeleting"
                class="mr-xs"
            >
                {{ $t("general.cancel") }}
            </SecondaryButton>
            <DangerButton
                @click="deleteAccount"
                :is-loading="accountIsDeleting"
                :disabled="accountIsDeleting"
            >
                {{ $t("general.delete") }}
            </DangerButton>
        </template>
    </ConfirmationModal>

    <Modal
        :show="addAccountModal"
        :closeable="true"
        @close="addAccountModal = false"
    >
        <div class="flex flex-col">
            <AddFacebookPage
                v-if="$page.props.is_configured_service.facebook"
            />
            <AddFacebookGroup
                v-if="$page.props.is_configured_service.facebook"
            />
            <AddInstagramAccount
                v-if="$page.props.is_configured_service.facebook"
            />
            <AddMastodonAccount/>
            <AddYoutubeAccount
                v-if="$page.props.is_configured_service.google"
            />
            <AddPinterestAccount
                v-if="$page.props.is_configured_service.pinterest"
            />
            <AddLinkedinProfile
                v-if="$page.props.is_configured_service.linkedin"
            />
            <AddLinkedinPage
                v-if="
                    $page.props.is_configured_service.linkedin &&
                    $page.props.additionally.linkedin_supports_pages
                "
            />
            <AddTikTokAccount v-if="$page.props.is_configured_service.tiktok"/>
            <AddTwitterAccount
                v-if="$page.props.is_configured_service.twitter"
            />
        </div>
    </Modal>
</template>
