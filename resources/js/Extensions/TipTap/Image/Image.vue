<script setup>
import {NodeViewWrapper, nodeViewProps} from '@tiptap/vue-3';
import {resolveImg, ImageDisplay, clamp} from "./util";
import {computed, onBeforeMount, onMounted, ref} from "vue";
import ImageMenu from "./ImageMenu.vue";

const props = defineProps({
    ...nodeViewProps
});

const MIN_SIZE = 20;
const MAX_SIZE = 100000;

const resizeDirections = {
    TOP_LEFT: 'tl',
    TOP_RIGHT: 'tr',
    BOTTOM_LEFT: 'bl',
    BOTTOM_RIGHT: 'br'
}

const src = computed(() => {
    return props.node.attrs.src;
})

const width = computed(() => {
    return props.node.attrs.width;
})

const height = computed(() => {
    return props.node.attrs.height;
})

const display = computed(() => {
    return props.node.attrs.display;
})

const imageViewClass = computed(() => {
    return {
        'float-none inline-block': display.value === ImageDisplay.INLINE,
        'flex justify-center': display.value === ImageDisplay.CENTER,
        'float-left ml-0 mr-md': display.value === ImageDisplay.FLOAT_LEFT,
        'float-right ml-xs mr-0': display.value === ImageDisplay.FLOAT_RIGHT,
    }
})

const originalSize = ref({
    width: 0,
    height: 0,
})

const maxSize = ref({
    width: MAX_SIZE,
    height: MAX_SIZE,
})

const resizing = ref(false)

onBeforeMount(async () => {
    const result = await resolveImg(src.value);

    if (!result.complete) {
        result.width = MIN_SIZE;
        result.height = MIN_SIZE;
    }

    originalSize.value = {
        width: result.width,
        height: result.height,
    };
})

const getMaxSize = () => {
    const {width} = getComputedStyle(props.editor.view.dom);

    maxSize.value = parseInt(width, 10);
}

const selectImage = () => {
    props.editor.commands.setNodeSelection(props.getPos());
}

onMounted(() => {
    getMaxSize();
})

const resizerState = ref({
    x: 0,
    y: 0,
    w: 0,
    h: 0,
    dir: '',
});

const onMouseDown = (e, direction) => {
    e.preventDefault();
    e.stopPropagation();

    resizerState.value.x = e.clientX;
    resizerState.value.y = e.clientY;

    const originalWidth = originalSize.value.width;
    const originalHeight = originalSize.value.height;
    const aspectRatio = originalWidth / originalHeight;

    let {width, height} = props.node.attrs;
    const maxWidth = maxSize.value.width;

    if (width && !height) {
        width = width > maxWidth ? maxWidth : width;
        height = Math.round(width / aspectRatio);
    } else if (height && !width) {
        width = Math.round(height * aspectRatio);
        width = width > maxWidth ? maxWidth : width;
    } else if (!width && !height) {
        width = originalWidth > maxWidth ? maxWidth : originalWidth;
        height = Math.round(width / aspectRatio);
    } else {
        width = width > maxWidth ? maxWidth : width;
    }

    resizerState.value.w = width;
    resizerState.value.h = height;
    resizerState.value.dir = direction;

    resizing.value = true;

    onEvents();
}

const onMouseMove = (e) => {
    e.preventDefault();
    e.stopPropagation();

    if (!resizing.value) {
        return;
    }

    const {x, y, w, h, dir} = resizerState.value;

    const dx = (e.clientX - x) * (/l/.test(dir) ? -1 : 1);
    const dy = (e.clientY - y) * (/t/.test(dir) ? -1 : 1);

    props.updateAttributes({
        width: clamp(w + dx, MIN_SIZE, maxSize.value.width),
        height: Math.max(h + dy, MIN_SIZE),
    });
}

const onMouseUp = (e) => {
    e.preventDefault();
    e.stopPropagation();

    if (!resizing.value) {
        return;
    }

    resizing.value = false;

    resizerState.value = {
        x: 0,
        y: 0,
        w: 0,
        h: 0,
        dir: '',
    };

    offEvents();
    selectImage();
}

const onEvents = () => {
    document.addEventListener('mousemove', onMouseMove, true);
    document.addEventListener('mouseup', onMouseUp, true);
}

const offEvents = () => {
    document.removeEventListener('mousemove', onMouseMove, true);
    document.removeEventListener('mouseup', onMouseUp, true);
}
</script>
<template>
    <NodeViewWrapper
        as="span"
        :class="imageViewClass"
        class="leading-none max-w-full select-none align-baseline"
    >
        <figure
            class="drag-handle"
            contentEditable="false"
            draggable="true"
            data-drag-handle
        >
            <div
                class="relative inline-block clear-both max-w-full outline-solid outline-1"
                :class="{'outline-primary-500': selected}">
                <img
                    :src="src"
                    :title="node.attrs.title"
                    :alt="node.attrs.alt"
                    :width="width"
                    :height="height"
                    :data-display="node.attrs.display"
                    class="cursor-pointer m-0"
                    @click="selectImage"
                />

                <div
                    v-if="editor.isEditable"
                    v-show="selected || resizing"
                    class="border border-primary-500 h-full left-0 absolute top-0 w-full z-10"
                >
                <span
                    v-for="direction in resizeDirections"
                    :key="direction"
                    class="bg-primary-500 border border-white rounded box-border block h-sm w-sm absolute z-10"
                    :class="{
                        'tl': 'left-[-6px] top-[-6px] cursor-nwse-resize',
                        'tr': 'right-[-6px] top-[-6px] cursor-nesw-resize',
                        'bl': 'left-[-6px] bottom-[-6px] cursor-nesw-resize',
                        'br': 'right-[-6px] bottom-[-6px] cursor-nwse-resize',
                    }[direction]"
                    @mousedown="onMouseDown($event, direction)"
                />
                </div>

                <ImageMenu :editor="editor"
                           :node="node"
                           :selected="selected"
                           :updateAttributes="updateAttributes"
                           :deleteNode="deleteNode"
                />
            </div>
        </figure>
    </NodeViewWrapper>
</template>
