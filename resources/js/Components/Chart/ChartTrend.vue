<script setup>
import {computed} from "vue";
import useTheme from "../../Composables/useTheme";
import ChartBar from "../Package/ChartBar.vue";

const props = defineProps({
    label: {
        type: String,
        required: true
    },
    labels: {
        type: Array,
        required: true
    },
    aggregates: {
        type: Array,
        required: true
    }
})

const theme = useTheme();

const chartData = computed(() => {
    return {
        labels: props.labels,
        datasets: [
            Object.assign(theme.getDefaultChartDataSetColors(), {
                label: props.label,
                type: 'line',
                data: props.aggregates,
                yAxisID: 'y1',
                order: 0,
            })
        ]
    }
})
</script>
<template>
    <ChartBar :data="chartData"/>
</template>
