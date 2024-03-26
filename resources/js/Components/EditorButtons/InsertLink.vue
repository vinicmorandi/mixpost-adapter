<script setup>
import {ref} from "vue";
import Input from "../Form/Input.vue";
import VerticalGroup from "../Layout/VerticalGroup.vue";
import Select from "../Form/Select.vue";
import PrimaryButton from "../Button/PrimaryButton.vue";
import DialogModal from "../Modal/DialogModal.vue";
import SecondaryButton from "../Button/SecondaryButton.vue";
import Link from "../../Icons/Link.vue";
import Unlink from "../../Icons/Editor/Unlink.vue";

const props = defineProps({
    editor: {
        required: true,
    }
});

const emit = defineEmits(['insert']);

const modal = ref(false);
const href = ref('');
const target = ref('_self');

const open = () => {
    modal.value = true;

    const {href: linkHref, target: linkTarget} = props.editor.getAttributes('link');

    if (linkHref) {
        href.value = linkHref;
    }

    if (linkTarget) {
        target.value = linkTarget;
    }
}

const close = () => {
    modal.value = false;
    href.value = '';
    target.value = '_self';
}

const insert = () => {
    props.editor.commands.setLink({
        href: href.value,
        target: target.value
    });

    close();
}

const remove = () => {
    props.editor.chain().focus().unsetLink().run();
    close();
}
</script>

<template>
    <button @click="open"
            :class="{ 'is-active': editor.isActive('link') }"
            type="button"
            class="flex">
        <Link/>
    </button>

    <DialogModal :show="modal"
                 max-width="sm"
                 :closeable="true"
                 :scrollable-body="true"
                 @close="close">
        <template #header>
            {{ $t('general.insert')}}/{{ $t('editor.edit_link')}}
        </template>

        <template #body>
            <VerticalGroup>
                <template #title>
                    <label for="href">URL</label>
                </template>

                <div class="w-full flex">
                    <Input type="text" v-model="href" id="href" class="mr-xs"/>
                    <SecondaryButton @click="remove"
                                     size="md"
                                     :disabled="!editor.isActive('link')">
                        <template #icon>
                            <Unlink/>
                        </template>
                    </SecondaryButton>
                </div>
            </VerticalGroup>

            <VerticalGroup class="mt-lg">
                <template #title>
                    <label for="target">{{ $t('editor.open_link_in')}}</label>
                </template>

                <Select v-model="target" id="target">
                    <option value="_self">{{ $t('editor.current_window')}}</option>
                    <option value="_blank">{{ $t('editor.new_window')}}</option>
                </Select>
            </VerticalGroup>
        </template>

        <template #footer>
            <SecondaryButton @click="close" class="mr-xs">{{ $t('general.cancel') }}</SecondaryButton>
            <PrimaryButton @click="insert">{{ $t('general.insert')}}</PrimaryButton>
        </template>
    </DialogModal>
</template>
