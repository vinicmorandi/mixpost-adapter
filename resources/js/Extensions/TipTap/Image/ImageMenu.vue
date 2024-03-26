<script setup>
import {Dropdown as VDropdown} from "floating-vue";
import '@css/overrideVDropdown.css'
import {ImageDisplay} from "./util";
import PureButton from "../../../Components/Button/PureButton.vue";
import Trash from "../../../Icons/Trash.vue";
import AlignRight from "../../../Icons/Editor/AlignRight.vue";
import AlignCenter from "../../../Icons/Editor/AlignCenter.vue";
import AlignLeft from "../../../Icons/Editor/AlignLeft.vue";
import {computed} from "vue";
import {Editor} from '@tiptap/vue-3';
import {nodeViewProps} from '@tiptap/vue-3';

const props = defineProps({
    editor: {
        required: true,
        type: Editor,
    },
    node: nodeViewProps['node'],
    updateAttributes: nodeViewProps['updateAttributes'],
    deleteNode: {
        required: true,
        type: Function,
    },
    selected: {
        required: true,
        type: Boolean
    },
});

const currentDisplay = computed(() => {
    return props.node.attrs.display;
})

const updateDisplay = (display) => {
    if (display === currentDisplay.value) {
        props.updateAttributes({
            'display': ImageDisplay.INLINE
        });

        return;
    }

    props.updateAttributes({
        display
    });
}

const remove = () => {
    props.deleteNode()
}
</script>
<template>
    <VDropdown :triggers="[]"
               :shown="selected"
               :autoHide="false"
    >
        <template #popper="{ hide }">
            <div class="p-md bg-white">
                <div class="editorClassicMenu flex gap-xs">
                    <button @click="updateDisplay(ImageDisplay.FLOAT_LEFT)"
                            :class="{ 'is-active': ImageDisplay.FLOAT_LEFT === currentDisplay }"
                            v-tooltip="$t('editor.align_left')"
                            type="button">
                        <AlignLeft/>
                    </button>

                    <button @click="updateDisplay(ImageDisplay.CENTER)"
                            :class="{ 'is-active': ImageDisplay.CENTER === currentDisplay }"
                            v-tooltip="$t('editor.align_center')"
                            type="button">
                        <AlignCenter/>
                    </button>

                    <button @click="updateDisplay(ImageDisplay.FLOAT_RIGHT)"
                            :class="{ 'is-active': ImageDisplay.FLOAT_RIGHT === currentDisplay }"
                            v-tooltip="$t('editor.align_right')"
                            type="button">
                        <AlignRight/>
                    </button>

                    <PureButton @click="remove"
                                v-tooltip="$t('editor.remove_image')"
                                destructive
                    >
                        <Trash/>
                    </PureButton>
                </div>
            </div>
        </template>
    </VDropdown>
</template>
