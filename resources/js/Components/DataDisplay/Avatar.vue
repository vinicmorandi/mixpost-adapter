<script setup>
import {computed} from "vue";
import {lightOrDark} from "@/helpers";
import {colorLight, colorDark} from "@/Constants/ColorPallet";

const props = defineProps({
    name: {
        type: String,
        required: true
    },
    size: {
        type: String,
        default: 'md'
    },
    backgroundColor: {
        type: String,
        default: ''
    },
    roundedClass: {
        type: String,
        default: 'rounded-full'
    }
})

const wrapperClasses = computed(() => {
    return {
        'md': 'w-10 h-10',
        'lg': 'w-16 h-16'
    }[props.size];
});

const textClasses = computed(() => {
    return {
        'md': 'text-base',
        'lg': 'text-lg'
    }[props.size];
});

const parsedName = computed(() => {
    const parts = props.name.replace(/[^a-zA-Z0-9]/g, '').split(/[ -]/)
    let initials = ''

    for (let i = 0; i < parts.length; i++) {
        initials += parts[i][0]
    }

    if (initials.length > 3 && /[A-Z]/.test(initials)) {
        initials = initials.replace(/[a-z]+/g, '')
    }

    initials = initials.substring(0, 3).toUpperCase()

    return initials
});

const backgroundColor = computed(() => {
    if (props.backgroundColor) {
        return props.backgroundColor;
    }

    let hash = 0;

    for (let i = 0; i < props.name.length; i++) {
        hash = props.name.charCodeAt(i) + ((hash << 5) - hash);
    }

    let color = "#";

    for (let i = 0; i < 3; i++) {
        const value = (hash >> (i * 8)) & 0xff;
        color += ("00" + value.toString(16)).substr(-2);
    }

    return lightenColor(color, 100);
});

const textColor = computed(() => {
    if (!props.backgroundColor) {
        return colorDark;
    }

    return lightOrDark(props.backgroundColor) === 'light' ? colorDark : colorLight
})

const lightenColor = (backgroundColor, amt) => {
    // From https://css-tricks.com/snippets/javascript/lighten-darken-color/
    let usePound = false;
    if (backgroundColor[0] === "#") {
        backgroundColor = backgroundColor.slice(1);
        usePound = true;
    }
    const num = parseInt(backgroundColor, 16);
    let r = (num >> 16) + amt;
    if (r > 255) {
        r = 255;
    } else if (r < 0) {
        r = 0;
    }
    let b = ((num >> 8) & 0x00ff) + amt;
    if (b > 255) {
        b = 255;
    } else if (b < 0) {
        b = 0;
    }
    let g = (num & 0x0000ff) + amt;
    if (g > 255) {
        g = 255;
    } else if (g < 0) {
        g = 0;
    }
    return (usePound ? "#" : "") + (g | (b << 8) | (r << 16)).toString(16);
};
</script>
<template>
    <div class="flex items-center justify-center">
        <div :class="[wrapperClasses, roundedClass]"
             :style="{'background': backgroundColor, color: textColor}"
             class="flex items-center justify-center p-xs">
            <div :class="textClasses">{{ parsedName }}</div>
        </div>
    </div>
</template>
