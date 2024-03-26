<script setup>
import {Link} from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true
    },
    method: {
        type: String,
        default: 'get'
    },
    as: {
        type: String,
        default: 'a'
    },
    native: {
        type: Boolean,
        default: false,
    },
    type: {
        type: String,
        default: null
    },
    hiddenTextOnSmallScreen: {
        type: Boolean,
        default: false,
    }
});
const classes = 'relative inline-flex items-center text-gray-400 hover:text-primary-500 transition-colors ease-in-out duration-200';
</script>
<template>
    <a v-if="native" :href="href" :class="classes">
        <span v-if="$slots.icon" class="inline-flex"
              :class="{'sm:mr-xs': $slots.default, 'mr-0 sm:mr-xs': hiddenTextOnSmallScreen, 'mr-xs': !hiddenTextOnSmallScreen}">
            <slot name="icon"/>
        </span>

        <span v-if="$slots.default" :class="{'hidden sm:inline': hiddenTextOnSmallScreen}">
            <slot/>
        </span>
    </a>

    <Link v-else :href="href" :method="method" :as="as" :type="type"
          :class="classes">
        <span v-if="$slots.icon" class="inline-flex"
              :class="{'sm:mr-xs': $slots.default, 'mr-0 sm:mr-xs': hiddenTextOnSmallScreen, 'mr-xs': !hiddenTextOnSmallScreen && $slots.default}">
            <slot name="icon"/>
        </span>

        <span v-if="$slots.default" class="inline-flex items-center" :class="{'hidden sm:inline': hiddenTextOnSmallScreen}">
            <slot/>
        </span>
    </Link>
</template>
