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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-sm">
            <Panel>
                <template #title><span
                    v-tooltip="$t('service.facebook.report.number_people_page')">{{ $t('service.facebook.report.page_engaged_users') }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('page_engaged_users') }}</div>
            </Panel>

            <Panel>
                <template #title><span
                    v-tooltip="$t('service.facebook.report.number_times_post_engagements')">{{ $t('service.facebook.report.post_engagements') }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('page_post_engagements') }}</div>
            </Panel>

            <Panel>
                <template #title><span
                    v-tooltip=" $t('service.facebook.report.number_times_posts_impressions') ">{{ $t('service.facebook.report.posts_impressions') }}</span>
                </template>
                <div class="font-semibold text-primary-500 text-2xl">{{
                        getMetricCount('page_posts_impressions')
                    }}
                </div>
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
