<script setup>
import {inject, ref, shallowRef} from "vue";
import { useI18n } from "vue-i18n";
import {router, useForm} from "@inertiajs/vue3";
import useNotifications from "../../Composables/useNotifications";
import usePageBlock from "../../Composables/usePageBlock";
import {cloneDeep} from "lodash";
import SecondaryButton from "../Button/SecondaryButton.vue";
import DialogModal from "../Modal/DialogModal.vue";
import PrimaryButton from "../Button/PrimaryButton.vue";
import FormBlock from "./FormBlock.vue";
import PureButton from "../Button/PureButton.vue";
import PencilSquare from "../../Icons/PencilSquare.vue";
import DangerButton from "../Button/DangerButton.vue";

const { t: $t } = useI18n()

const routePrefix = inject('routePrefix');
const confirmation = inject('confirmation');

const props = defineProps({
    block: {
        required: true,
        type: Object,
    }
})

const emit = defineEmits(['update', 'delete']);

const {notify} = useNotifications();
const {modules} = usePageBlock();

const modal = ref(false);
const moduleComponent = shallowRef(null);

const form = useForm(cloneDeep(props.block));

const open = () => {
    const context = modules[props.block.module];

    if (context === undefined) {
        notify('error', $t('page.module_not_found'));
        return;
    }

    moduleComponent.value = context.component;

    modal.value = true;
}

const close = () => {
    modal.value = false;
    form.reset();
}

const attemptClose = () => {
    if (!form.isDirty) {
        close();
        return;
    }

    confirmation()
        .title($t('page.are_you_sure'))
        .description($t('page.unsaved_will_lost'))
        .btnConfirmName($t('general.discard'))
        .onConfirm((dialog) => {
            dialog.close();
            close();
        })
        .show();
}

const update = () => {
    form.put(route(`${routePrefix}.blocks.update`, {block: props.block.id}), {
        preserveScroll: true,
        preserveState: true,
        only: ['blocks'],
        onSuccess: (response) => {
            const updateBlock = response.props.blocks.data.find(block => block.id === props.block.id);

            if (updateBlock) {
                emit('update', updateBlock);
            }

            notify('success', $t('page.block_updated'));
        }
    })
}

const deleteBlock = () => {
    confirmation()
        .title($t('page.delete_block'))
        .description($t('page.confirm_delete_block'))
        .destructive()
        .onConfirm((dialog) => {
            dialog.isLoading(true);

            router.delete(route(`${routePrefix}.blocks.delete`, {
                block: props.block.id
            }), {
                only: ['blocks'],
                onSuccess() {
                    notify('success', $t('page.block_deleted'))
                    emit('delete');
                    dialog.reset();
                    close();
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
    <PureButton
        @click="open"

        class="group-visible">
        <template #icon>
            <PencilSquare/>
        </template>
    </PureButton>

    <DialogModal :show="modal"
                 max-width="5xl"
                 :scrollable-body="true"
                 :closeable="true"
                 @close="attemptClose">
        <template #header>
            {{ $t('page.edit_block') }}[<span class="font-semibold capitalize">{{ form.module }}</span>]
        </template>

        <template #body>
            <div class="mt-xs">
                <template v-if="modal">
                    <FormBlock :form="form" :moduleComponent="moduleComponent"/>
                </template>
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="attemptClose" class="mr-xs">{{ $t('general.cancel') }}</SecondaryButton>
            <DangerButton @click="deleteBlock" class="mr-xs">{{ $t('general.delete') }}</DangerButton>
            <PrimaryButton @click="update"
                           :disabled="form.processing"
                           :isLoading="form.processing">
                {{ $t('general.update') }}
            </PrimaryButton>
        </template>
    </DialogModal>
</template>
