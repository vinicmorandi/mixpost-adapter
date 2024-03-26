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
        <div class="grid grid-cols-4 gap-sm">
            <Panel>
                <template #title><span
                    v-tooltip="$t('report.posts_liked')">{{ $t('report.likes') }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('likes') }}</div>
            </Panel>

            <Panel>
                <template #title><span
                    v-tooltip="$t('service.tiktok.report.views_video')">{{ $t('service.tiktok.report.views') }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('views') }}</div>
            </Panel>

            <Panel>
                <template #title><span
                    v-tooltip="$t('service.tiktok.report.number_shares')">{{
                        $t('service.tiktok.report.shares')
                    }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('shares') }}</div>
            </Panel>

            <Panel>
                <template #title><span
                    v-tooltip="$t('service.tiktok.report.number_comments')">{{ $t('report.comments') }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('comments') }}</div>
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
