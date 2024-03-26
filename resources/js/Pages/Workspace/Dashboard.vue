<script setup>
import {computed, inject, onMounted, ref, watch} from "vue";
import {Head, Link} from '@inertiajs/vue3';
import {useI18n} from "vue-i18n";
import NProgress from 'nprogress'
import {find} from "lodash";
import useNotifications from "@/Composables/useNotifications";
import PageHeader from '@/Components/DataDisplay/PageHeader.vue';
import Account from "@/Components/Account/Account.vue"
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import Tabs from "@/Components/Navigation/Tabs.vue"
import Tab from "@/Components/Navigation/Tab.vue"
import TwitterReports from "@/Components/Report/TwitterReports.vue"
import FacebookPageReports from "@/Components/Report/FacebookPageReports.vue"
import FacebookGroupReports from "@/Components/Report/FacebookGroupReports.vue"
import InstagramReports from "@/Components/Report/InstagramReports.vue"
import MastodonReports from "@/Components/Report/MastodonReports.vue"
import PinterestReports from "@/Components/Report/PinterestReports.vue"
import LinkedinReports from "@/Components/Report/LinkedinReports.vue"
import LinkedinPageReports from "@/Components/Report/LinkedinPageReports.vue"
import TikTokReports from "@/Components/Report/TikTokReports.vue"
import YoutubeReports from "@/Components/Report/YoutubeReports.vue"

const {t: $t} = useI18n()

const props = defineProps({
    accounts: {
        required: true,
        type: Array,
    }
})

const {notify} = useNotifications();
const workspaceCtx = inject('workspaceCtx');

const isLoading = ref(false);
const data = ref({
    metrics: {},
    audience: {}
});

const selectAccount = (account) => {
    workspaceCtx.dashboard_filter.account_id = account.id;
}

const isAccountSelected = (account) => {
    return workspaceCtx.dashboard_filter.account_id === account.id;
}

const selectPeriod = (value) => {
    workspaceCtx.dashboard_filter.period = value;
}

const isPeriodSelected = (value) => {
    return workspaceCtx.dashboard_filter.period === value;
}

const fetch = () => {
    isLoading.value = true;
    NProgress.start();

    axios.get(route('mixpost.reports', {workspace: workspaceCtx.id}), {
        params: workspaceCtx.dashboard_filter
    }).then(function (response) {
        data.value = response.data;
    }).catch(() => {
        notify('error', $t('dashboard.error_retrieving_analytics'));
    }).finally(() => {
        isLoading.value = false;
        NProgress.done();
    });
}

const providers = {
    'twitter': TwitterReports,
    'facebook_page': FacebookPageReports,
    'facebook_group': FacebookGroupReports,
    'instagram': InstagramReports,
    'mastodon': MastodonReports,
    'pinterest': PinterestReports,
    'linkedin': LinkedinReports,
    'linkedin_page': LinkedinPageReports,
    'tiktok': TikTokReports,
    'youtube': YoutubeReports
};

const component = computed(() => {
    const account = find(props.accounts, {id: workspaceCtx.dashboard_filter.account_id});

    if (account === undefined) {
        return;
    }

    return providers[account.provider];
});

onMounted(() => {
    if (!props.accounts.length) {
        return null;
    }

    if (!workspaceCtx.dashboard_filter.account_id) {
        selectAccount(props.accounts[0]);
        return null;
    }

    fetch();
})

watch(workspaceCtx.dashboard_filter, () => {
    fetch()
});
</script>
<template>
    <Head :title="$t('dashboard.dashboard')"/>

    <div class="row-py">
        <PageHeader :title="$t('dashboard.dashboard')">
            <Tabs v-if="accounts.length">
                <Tab @click="selectPeriod('7_days')" :active="isPeriodSelected('7_days')">7 {{ $t("dashboard.days") }}
                </Tab>
                <Tab @click="selectPeriod('30_days')" :active="isPeriodSelected('30_days')">30
                    {{ $t("dashboard.days") }}
                </Tab>
                <Tab @click="selectPeriod('90_days')" :active="isPeriodSelected('90_days')">90
                    {{ $t("dashboard.days") }}
                </Tab>
            </Tabs>
        </PageHeader>

        <div class="row-px flex items-center">
            <div class="w-full">
                <div v-if="accounts.length" class="flex flex-wrap items-center gap-sm">
                    <template v-for="account in accounts" :key="account.id">
                        <button @click="selectAccount(account)" type="button">
                            <Account
                                :provider="account.provider"
                                :name="account.name"
                                :active="isAccountSelected(account)"
                                :img-url="account.image"
                                v-tooltip="account.name"
                            />
                        </button>
                    </template>
                </div>
                <div v-else>
                    <p class="mb-xs"> {{ $t("account.add_social_account") }}</p>
                    <Link :href="route('mixpost.accounts.index', {workspace: workspaceCtx.id})">
                        <PrimaryButton>{{ $t("account.add_account", 2) }}</PrimaryButton>
                    </Link>
                </div>
            </div>
        </div>

        <component :is="component" :data="data" :isLoading="isLoading"/>
    </div>
</template>
