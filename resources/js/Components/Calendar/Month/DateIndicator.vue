<script setup>
import {computed} from "vue";
import useDateLocalize from "../../../Composables/useDateLocalize";

const {translatedFormat} = useDateLocalize();

const props = defineProps({
    selectedDate: {
        type: Date,
        required: true
    }
})

const label = computed(() => {
    return translatedFormat(props.selectedDate, 'MMMM yyyy');
});
</script>
<template>
    <div class="text-gray-700 font-semibold text-lg">{{ label }}</div>
</template>

