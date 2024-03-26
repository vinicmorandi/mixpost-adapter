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
        <div class="grid grid-cols-3 gap-sm">
            <Panel>
                <template #title><span v-tooltip="$t('service.mastodon.report.number_replies')">
                    {{ $t('service.mastodon.report.replies') }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('replies') }}</div>
            </Panel>

            <Panel>
                <template #title><span
                    v-tooltip="$t('service.mastodon.report.number_reblogs')">{{ $t('service.mastodon.report.reblogs') }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('reblogs') }}</div>
            </Panel>

            <Panel>
                <template #title><span
                    v-tooltip="$t('service.mastodon.report.favourites_number')">{{ $t('service.mastodon.report.favourites') }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('favourites') }}</div>
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
