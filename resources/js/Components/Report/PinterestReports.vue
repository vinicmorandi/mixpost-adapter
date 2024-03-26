<script setup>
import Panel from "@/Components/Surface/Panel.vue";
import {computed} from "vue";
import ChartTrend from "../Chart/ChartTrend.vue";

const props = defineProps({
    data: {
        type: Object,
        required: true
    },
    isLoading: {
        type: Boolean,
        required: true,
    }
})

const getMetricCount = (value) => {
    return props.data.metrics.hasOwnProperty(value) ? props.data.metrics[value] : 0;
}

const getAudienceData = (value) => {
    return props.data.audience.hasOwnProperty(value) ? props.data.audience[value] : []
}

const chartData = computed(() => {
    return {
        labels: getAudienceData('labels'),
        aggregates: getAudienceData('values'),
    }
})
</script>
<template>
    <div class="row-px mt-2xl">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-sm">
            <Panel>
                <template #title><span v-tooltip="$t('service.pinterest.report.number_pins_saved') ">{{ $t('general.save') }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('save') }}</div>
            </Panel>

            <Panel>
                <template #title><span
                    v-tooltip="$t('service.pinterest.report.number_pin_clicks')">{{ $t('service.pinterest.report.pin_clicks') }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('pin_click') }}</div>
            </Panel>

            <Panel>
                <template #title><span
                    v-tooltip="$t('service.pinterest.report.number_impressions')">{{ $t('report.impressions') }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('impression') }}</div>
            </Panel>

            <Panel>
                <template #title><span
                    v-tooltip="$t('service.pinterest.report.number_impressions')">{{ $t('service.pinterest.save_rate') }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('save_rate') }}%</div>
            </Panel>

            <Panel>
                <template #title><span
                    v-tooltip="$t('service.pinterest.report.number_outbound_clicks')">{{ $t('service.pinterest.report.outbound_clicks') }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('outbound_click') }}</div>
            </Panel>
        </div>
    </div>

    <div class="row-px mt-2xl">
        <Panel>
            <template #title>{{ $t('report.audience') }}</template>
            <template #description>{{ $t('report.followers_per_day') }}</template>
            <ChartTrend :label="$t('report.followers')" :labels="chartData.labels" :aggregates="chartData.aggregates"/>
        </Panel>
    </div>
</template>
