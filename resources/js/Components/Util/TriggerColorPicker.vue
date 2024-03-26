<script setup>
import {computed, ref} from "vue";
import ColorPicker from "../Package/ColorPicker.vue";
import DialogModal from "../Modal/DialogModal.vue";
import PrimaryButton from "../Button/PrimaryButton.vue";
import SecondaryButton from "../Button/SecondaryButton.vue";

const props = defineProps({
    color: {
        required: true,
        type: String,
    }
});

const emit = defineEmits(['update']);

const show = ref(null);
const selected = ref(null);

const open = () => {
    show.value = true;

    selected.value = props.color;
}

const close = () => {
    show.value = null;
}

const isPickerOpen = computed(() => {
    return show.value !== null;
})
const done = () => {
    emit('update', selected.value);

    close();
}
</script>
<template>
    <div @click="open" role="button">
        <slot/>
    </div>

    <DialogModal :show="isPickerOpen" max-width="md" @close="close">
        <template #header>
            {{ $t('vendor.color_picker') }}
        </template>
        <template #body>
            <template v-if="isPickerOpen" class="flex flex-col">
                <ColorPicker v-model="selected"/>
            </template>
        </template>
        <template #footer>
            <SecondaryButton @click="close" class="mr-xs">{{ $t('general.cancel') }}</SecondaryButton>
            <PrimaryButton @click="done">{{ $t('general.done') }}</PrimaryButton>
        </template>
    </DialogModal>
</template>
