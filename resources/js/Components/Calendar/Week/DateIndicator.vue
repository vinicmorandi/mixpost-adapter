<script setup>
import {computed} from "vue";
import {startOfWeek, endOfWeek} from "date-fns";
import useDateLocalize from "../../../Composables/useDateLocalize";

const props = defineProps({
    selectedDate: {
        type: Date,
        required: true
    },
    weekStartsOn: {
        required: false,
        type: Number,
        default: 0
    },
})

const {translatedFormat} = useDateLocalize();

const label = computed(() => {
    const start = startOfWeek(props.selectedDate, {
        weekStartsOn: props.weekStartsOn,
    });

    const end = endOfWeek(props.selectedDate, {
        weekStartsOn: props.weekStartsOn,
    });

    return `${translatedFormat(start, 'MMM do')} - ${translatedFormat(end, 'MMM do')}`
});
</script>
<template>
    <div class="text-gray-700 font-semibold text-lg">{{ label }}</div>
</template>

