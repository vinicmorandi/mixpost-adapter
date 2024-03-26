<script setup>
import {ref, onMounted, onUnmounted, useAttrs, computed, watch} from "vue";
import {useEditor, EditorContent} from '@tiptap/vue-3'
import {useI18n} from "vue-i18n";
import useEditorHelper from "@/Composables/useEditor";
import emitter from "@/Services/emitter";
import History from '@tiptap/extension-history'
import Placeholder from '@tiptap/extension-placeholder'
import Typography from '@tiptap/extension-typography'
import StripLinksOnPaste from "@/Extensions/TipTap/StripLinksOnPaste"
import Hashtag from "@/Extensions/TipTap/Hashtag"
import UserTag from "@/Extensions/TipTap/UserTag"
import Variable from "@/Extensions/TipTap/Variable"
import ClipboardTextParser from "../../Extensions/ProseMirror/ClipboardTextParser";

const {t: $t} = useI18n()

const attrs = useAttrs();

const props = defineProps({
    value: {
        required: true,
    },
    editable: {
        type: Boolean,
        default: true,
    },
    placeholder: {
        type: String,
        default: ''
    }
});

const emit = defineEmits(['update']);

const el = ref();
const focused = ref(false);

const {defaultExtensions} = useEditorHelper();


const editor = useEditor({
    editable: props.editable,
    content: props.value,
    extensions: [...defaultExtensions, ...[
        History,
        Placeholder.configure({
            placeholder: props.placeholder ? props.placeholder : $t('post.start_write'),
        }),
        Typography.configure({
            openDoubleQuote: false,
            closeDoubleQuote: false,
            openSingleQuote: false,
            closeSingleQuote: false
        }),
        StripLinksOnPaste,
        Hashtag,
        UserTag,
        Variable
    ]],
    editorProps: {
        attributes: {
            class: 'focus:outline-none min-h-[150px]',
        },
        clipboardTextParser: ClipboardTextParser,
    },
    onUpdate: () => {
        emit('update', editor.value.getHTML());
    },
    onFocus: () => {
        focused.value = true;
    },
    onBlur: () => {
        focused.value = false;
    }
});

const isEditor = (id) => {
    return attrs.hasOwnProperty('id') && id === attrs.id;
}

onMounted(() => {
    emitter.on('insertEmoji', e => {
        if (isEditor(e.editorId)) {
            editor.value.commands.insertContent(e.emoji.native);
        }
    });

    emitter.on('insertContent', e => {
        if (isEditor(e.editorId)) {
            editor.value.commands.insertContent(e.text);
        }
    });

    emitter.on('focusEditor', e => {
        if (isEditor(e.editorId)) {
            editor.value.commands.focus();
        }
    });
});

onUnmounted(() => {
    editor.value.destroy();
    emitter.off('insertEmoji');
    emitter.off('insertContent');
    emitter.off('focusEditor');
});

watch(() => props.value, (value) => {
    if (value !== editor.value.getHTML()) {
        editor.value.commands.setContent(value);
    }
})
</script>
<template>
    <div
        :class="{'border-primary-200 ring ring-primary-200 ring-opacity-50': focused}"
        class="border border-gray-200 rounded-md p-md pb-xs text-base transition-colors ease-in-out duration-200">
        <editor-content :editor="editor"/>
        <slot/>
    </div>
</template>
