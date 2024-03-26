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
                <template #title><span v-tooltip="$t('report.posts_liked')">
                    {{ $t('report.likes') }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('likes') }}</div>
            </Panel>

            <Panel>
                <template #title><span
                    v-tooltip="$t('service.instagram.report.number_comments_posts')">{{ $t('report.comments') }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('comments') }}</div>
            </Panel>

            <Panel>
                <template #title><span v-tooltip="$t('report.impressions_posts')">{{ $t('report.impressions') }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('impressions') }}</div>
            </Panel>

            <Panel>
                <template #title><span
                    v-tooltip="$t('service.instagram.report.engagement_report')">{{
                        $t('service.instagram.report.engagement')
                    }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('engagement') }}</div>
            </Panel>

            <Panel>
                <template #title><span
                    v-tooltip="$t('service.instagram.report.clicks_divided_impressions')">{{
                        $t('service.instagram.report.engagement_rate')
                    }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('engagement_rate') }}%</div>
            </Panel>

            <Panel>
                <template #title><span v-tooltip="$t('service.instagram.report.number_taps_email')">{{
                        $t('service.instagram.report.email_contacts')
                    }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('email_contacts') }}</div>
            </Panel>

            <Panel>
                <template #title><span
                    v-tooltip="$t('service.instagram.report.number_new_followers')">{{
                        $t('service.instagram.report.follower_count')
                    }} </span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('follower_count') }}</div>
            </Panel>

            <Panel>
                <template #title><span v-tooltip="$t('service.instagram.report.number_directions_clicks')">
                    {{ $t('service.instagram.report.directions_clicks') }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('directions_click') }}</div>
            </Panel>

            <Panel>
                <template #title><span v-tooltip="$t('service.instagram.report.number_phone_call_clicks')"> {{
                        $t('service.instagram.report.phone_call_clicks')
                    }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('phone_call_clicks') }}</div>
            </Panel>

            <Panel>
                <template #title><span
                    v-tooltip="$t('service.instagram.report.number_profile_views')"> {{
                        $t('service.instagram.report.profile_views')
                    }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('profile_views') }}</div>
            </Panel>

            <Panel>
                <template #title><span
                    v-tooltip="$t('service.instagram.report.number_reach')">{{
                        $t('service.instagram.report.reach')
                    }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('reach') }}</div>
            </Panel>

            <Panel>
                <template #title><span
                    v-tooltip="$t('service.instagram.report.number_text_message_clicks')">{{
                        $t('service.instagram.report.text_message_clicks')
                    }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('text_message_clicks') }}</div>
            </Panel>

            <Panel>
                <template #title><span
                    v-tooltip="$t('service.instagram.report.number_website_clicks')">{{
                        $t('service.instagram.report.website_clicks')
                    }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('website_clicks') }}</div>
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
