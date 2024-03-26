<script setup>
import {computed} from "vue";
import {usePage} from "@inertiajs/vue3";
import InstagramOptions from "@/Components/ProviderVersionOptions/InstagramOptions.vue";
import YoutubeOptions from "@/Components/ProviderVersionOptions/YoutubeOptions.vue";
import PinterestOptions from "@/Components/ProviderVersionOptions/PinterestOptions.vue";
import LinkedinOptions from "@/Components/ProviderVersionOptions/LinkedinOptions.vue";
import TikTokOptions from "@/Components/ProviderVersionOptions/TikTokOptions.vue";
import usePostVersions from "@/Composables/usePostVersions";
import MastodonOptions from "../ProviderVersionOptions/MastodonOptions.vue";
import FacebookPageOptions from "../ProviderVersionOptions/FacebookPageOptions.vue";

const props = defineProps({
    selectedAccounts: {
        required: true,
        type: Array
    },
    versions: {
        required: true,
        type: Array,
    },
    activeVersion: {
        required: true,
        type: Number,
    }
})

const {
    getAccountVersion,
} = usePostVersions();

const options = computed(() => {
    return getAccountVersion(props.versions, props.activeVersion).options;
})

const selectedAccountsWithVersion = computed(() => {
    return props.selectedAccounts.filter((account) => {
        return props.versions.some(version => version.account_id === account.id)
    });
});

const activeVersionHasProvider = (provider) => {
    return selectedAccountsWithVersion.value.some(account => props.activeVersion === account.id && account.provider === provider);
}

const isProviderSelected = (provider) => {
    return props.selectedAccounts.some(account => account.provider === provider);
}

const providerHaveVersion = (provider) => {
    return selectedAccountsWithVersion.value.some(account => account.provider === provider);
}

// TODO: Fix original version for all show{Provider}'s functions
// TODO: need to identify by account_id & provider
// Example: 2 accounts, one have specific version and another is in original version,
// the original version will have no options

const showMastodon = computed(() => {
    if (props.activeVersion === 0 && isProviderSelected('mastodon')) {
        return !providerHaveVersion('mastodon');
    }

    return activeVersionHasProvider('mastodon');
});

const showFacebookPage = computed(() => {
    if (props.activeVersion === 0 && isProviderSelected('facebook_page')) {
        return !providerHaveVersion('facebook_page');
    }

    return activeVersionHasProvider('facebook_page');
})

const showInstagram = computed(() => {
    if (props.activeVersion === 0 && isProviderSelected('instagram')) {
        return !providerHaveVersion('instagram');
    }

    return activeVersionHasProvider('instagram');
})

const showYoutube = computed(() => {
    if (props.activeVersion === 0 && isProviderSelected('youtube')) {
        return !providerHaveVersion('youtube');
    }

    return activeVersionHasProvider('youtube');
});

const showPinterest = computed(() => {
    if (props.activeVersion === 0 && isProviderSelected('pinterest')) {
        return !providerHaveVersion('pinterest');
    }

    return activeVersionHasProvider('pinterest');
});

const pinterestAccounts = computed(() => {
    if (showPinterest.value) {
        return props.selectedAccounts.filter((account) => account.provider === 'pinterest');
    }

    return [];
})

const showLinkedin = computed(() => {
    if (props.activeVersion === 0 && isProviderSelected('linkedin')) {
        return !providerHaveVersion('linkedin');
    }

    return activeVersionHasProvider('linkedin');
});

const showTiktok = computed(() => {
    if (usePage().props.service_configs.tiktok.share_type === 'inbox') {
        return false;
    }

    if (props.activeVersion === 0 && isProviderSelected('tiktok')) {
        return !providerHaveVersion('tiktok');
    }

    return activeVersionHasProvider('tiktok');
});

const tiktokAccounts = computed(() => {
    if (showTiktok.value) {
        return props.selectedAccounts.filter((account) => account.provider === 'tiktok');
    }

    return [];
})
</script>
<template>
    <div v-if="selectedAccounts.length">
        <div v-if="showMastodon" class="pb-md">
            <MastodonOptions :options="options.mastodon"/>
        </div>

        <div v-if="showFacebookPage" class="pb-md">
            <FacebookPageOptions :options="options.facebook_page"/>
        </div>

        <div v-if="showInstagram" class="pb-md">
            <InstagramOptions :options="options.instagram"/>
        </div>

        <div v-if="showYoutube" class="pb-md">
            <YoutubeOptions :options="options.youtube"/>
        </div>

        <div v-if="showPinterest" class="pb-md">
            <PinterestOptions :options="options.pinterest"
                              :accounts="pinterestAccounts"
                              :activeVersion="activeVersion"/>
        </div>

        <div v-if="showLinkedin" class="pb-md">
            <LinkedinOptions :options="options.linkedin"/>
        </div>

        <div v-if="showTiktok" class="pb-md">
            <TikTokOptions :options="options.tiktok"
                           :accounts="tiktokAccounts"
                           :activeVersion="activeVersion"/>
        </div>
    </div>
</template>
