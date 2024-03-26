<script setup>
import {inject, ref, shallowRef} from "vue";
import {useI18n} from "vue-i18n";
import {useForm} from "@inertiajs/vue3";
import useNotifications from "../../Composables/useNotifications";
import usePageBlock from "../../Composables/usePageBlock";
import {cloneDeep} from "lodash";
import SecondaryButton from "../Button/SecondaryButton.vue";
import DialogModal from "../Modal/DialogModal.vue";
import PrimaryButton from "../Button/PrimaryButton.vue";
import Plus from "../../Icons/Plus.vue";
import Dropdown from "../Dropdown/Dropdown.vue";
import DropdownItem from "../Dropdown/DropdownItem.vue";
import FormBlock from "./FormBlock.vue";

const {t: $t} = useI18n()

const routePrefix = inject('routePrefix');
const confirmation = inject('confirmation');

const emit = defineEmits(['add']);

const {notify} = useNotifications();
const {modules} = usePageBlock();

const modal = ref(false);
const moduleComponent = shallowRef(null);

const form = useForm({
    name: '',
    module: null,
    content: {},
    status: 1,
})

const open = (module) => {
    const context = modules[module];

    if (context === undefined) {
        notify('error', $t('page.module_not_found'));
        return;
    }

    moduleComponent.value = context.component;

    form.content = cloneDeep(context.content);
    form.module = module;

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

const store = () => {
    form.post(route(`${routePrefix}.blocks.store`), {
        preserveScroll: true,
        preserveState: true,
        only: ['blocks'],
        onSuccess: (response) => {
            emit('add', response.props.blocks.data[0]);
            close();
            notify('success', $t('page.block_created') + "\n" + $t('page.save_page'));
        }
    })
}
</script>
<template>
    <Dropdown width-classes="w-32" placement="bottom">
        <template #trigger>
            <SecondaryButton size="sm" class="mr-xs">
                <template #icon>
                    <Plus/>
                </template>
                {{ $t('page.create_block') }}
            </SecondaryButton>
        </template>

        <template #content>
            <DropdownItem as="button" @click="open('Editor')">
                Editor
            </DropdownItem>
            <DropdownItem as="button" @click="open('Html')">
                Html
            </DropdownItem>
        </template>
    </Dropdown>

    <DialogModal :show="modal"
                 max-width="5xl"
                 :scrollable-body="true"
                 :closeable="true"
                 @close="attemptClose">
        <template #header>
            {{ $t('page.create_block') }} [<span class="font-semibold capitalize">{{ form.module }}</span>]
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
            <PrimaryButton @click="store"
                           :disabled="form.processing"
                           :isLoading="form.processing">
                {{ $t('page.create_add_to_page') }}
            </PrimaryButton>
        </template>
    </DialogModal>
</template>
