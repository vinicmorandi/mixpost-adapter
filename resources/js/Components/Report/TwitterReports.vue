<script setup>
import {computed} from "vue";
import ls from "../../Services/ls";
import Panel from "@/Components/Surface/Panel.vue";
import Alert from "../Util/Alert.vue";
import useAuth from "../../Composables/useAuth";
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

const isFreeTier = computed(() => {
    return props.data.tier === 'free';
});

const {user} = useAuth();

const showAlert = computed(() => {
    if (!user.value.is_admin) {
        return false;
    }

    return !ls.get('twitter_api_tier_alert') && isFreeTier.value;
});

const closeAlert = () => {
    ls.set('twitter_api_tier_alert', true);
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
        <div v-if="showAlert" class="mb-lg">
            <Alert variant="warning" @close="closeAlert">
                <div>
                    <div>{{ $t('service.twitter.reports_limited') }}</div>
                    <a href="https://developer.twitter.com/en/portal/dashboard" target="_blank" class="underline">
                        {{  $t('service.twitter.upgrade') }}</a>
                </div>
            </Alert>
        </div>

        <div v-if="!isFreeTier" class="grid grid-cols-1 md:grid-cols-3 gap-sm">
            <Panel>
                <template #title><span v-tooltip="$t('report.posts_liked')">{{ $t('report.likes') }}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('likes') }}</div>
            </Panel>

            <Panel>
                <template #title><span v-tooltip="$t('service.twitter.number_retweets')">{{$t('service.twitter.retweets')}}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('retweets') }}</div>
            </Panel>

            <Panel>
                <template #title><span v-tooltip="$t('report.impressions_posts')">{{$t('report.impressions')}}</span>
                </template>
                <div class="font-bold text-primary-500 text-2xl">{{ getMetricCount('impressions') }}</div>
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
