<script setup>
import {computed} from "vue";
import {clone} from "lodash";
import {WEEKDAYS} from "../constants";

const props = defineProps({
    weekStartsOn: {
        required: false,
        type: Number,
        default: 0
    }
});

const items = computed(() => {
    const days = clone(WEEKDAYS);

    return days.splice(props.weekStartsOn ? 0 : 6).concat(days);
})
</script>
<template>
    <div class="grid grid-cols-7">
        <div v-for="(item, index) in items" :key="index" class="p-sm border-t border-r last:border-r-0 border-gray-200 text-center font-semibold">
            <span class="hidden sm:block">{{ $t(`calendar.weekdays.${item.key}.short`) }}</span>
            <span class="block sm:hidden">{{ $t(`calendar.weekdays.${item.key}.shortest`) }}</span>
        </div>
    </div>
</template>
