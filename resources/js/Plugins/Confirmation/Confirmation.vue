<script setup>
import {inject} from "vue";
import {router} from "@inertiajs/vue3";
import ConfirmationModal from "@/Components/Modal/ConfirmationModal.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import DangerButton from "@/Components/Button/DangerButton.vue";
import PrimaryButton from "../../Components/Button/PrimaryButton.vue";

const confirmation = inject('confirmation');

const {data} = confirmation();

const onCancelClick = () => {
    const onCancel = data.value.onCancel;

    confirmation().close();
    confirmation().reset();

    if (typeof onCancel === 'function') {
        onCancel(confirmation());
    }
};

const onConfirmClick = () => {
    const onConfirm = data.value.onConfirm;

    if (typeof onConfirm === 'function') {
        data.value.onConfirm(confirmation());
    }
};

router.on('navigate', () => {
    if (data.value.show) {
        confirmation().reset();
    }
})
</script>
<template>
    <ConfirmationModal :show="data.show"
                       :variant="data.variant"
                       z-index-class="z-40"
                       @close="onCancelClick">
        <template #header>
            {{ data.title }}
        </template>
        <template #body>
            <div v-html="data.description"/>
        </template>
        <template #footer>
            <SecondaryButton @click="onCancelClick" class="mr-xs">{{ data.btnCancelName ? data.btnCancelName : $t('general.cancel') }}</SecondaryButton>

            <template v-if="data.variant === 'danger'">
                <DangerButton @click="onConfirmClick" :disabled="data.isLoading" :isLoading="data.isLoading">
                    {{ data.btnConfirmName ? data.btnConfirmName : $t('general.confirm') }}
                </DangerButton>
            </template>

            <template v-else>
                <PrimaryButton @click="onConfirmClick" :disabled="data.isLoading" :isLoading="data.isLoading">
                    {{ data.btnConfirmName ? data.btnConfirmName : $t('general.confirm') }}
                </PrimaryButton>
            </template>
        </template>
    </ConfirmationModal>
</template>
