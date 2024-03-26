<script setup>
import {computed} from "vue";
import usePost from "@/Composables/usePost";
import ExclamationIcon from "@/Icons/Exclamation.vue"

const {accountsHitTextLimit, accountsHitMediaLimit} = usePost();

const show = computed(() => {
    return accountsHitTextLimit.value.length !== 0 || accountsHitMediaLimit.value.length !== 0;
})

const resolveProvider = (provider) => {
    if (['facebook_page', 'facebook_group'].includes(provider)) {
        return 'facebook';
    }

    if (['linkedin_page'].includes(provider)) {
        return 'LinkedIn';
    }

    return provider;
}
</script>
<template>
    <div v-if="show"
         class="w-full flex items-center row-px py-xs md:py-md flex-row border-b border-gray-200 text-red-500 bg-red-50">
        <div class="w-8 h-8 mr-sm flex items-center">
            <ExclamationIcon/>
        </div>

        <div>
            <div v-if="accountsHitTextLimit">
                <p v-for="item in accountsHitTextLimit">
                    <span v-html="$t('post.check_limit_characters', {
                            'provider': resolveProvider(item.provider),
                            'limit': item.limit,
                        })"/>
                    <span v-if="item.account_name">
                        {{ $t('post.check_version', {'account_name': account_name}) }}
                    </span>
                </p>
            </div>

            <div v-if="accountsHitMediaLimit">
                <p v-for="item in accountsHitMediaLimit">
                    <span v-if="item.mixing.hit">
                        <span
                            v-html="$t('post.you_cannot_mix_vgi', {'provider': resolveProvider(item.mixing.provider)})"/>
                    </span>
                    <span v-else>
                    <span v-if="item.photos.hit">
                        <span v-html="$t('post.limit_images', {
                                'provider': resolveProvider(item.photos.provider),
                                'limit': item.photos.limit
                            })"/>
                    </span>
                    <span v-if="item.videos.hit">
                        <span
                            v-html=" $t('post.limit_videos', { 'provider': resolveProvider(item.videos.provider), 'limit': item.videos.limit})"/>
                    </span>
                    <span v-if="item.gifs.hit">
                        <span
                            v-html=" $t('post.limit_gifs', { 'provider': resolveProvider(item.gifs.provider), 'limit': item.gifs.limit})"/>
                    </span>
                </span>
                    <span v-if="item.account_name">
                        {{ $t('post.check_version', {'account_name': item.account_name}) }}</span>
                </p>
            </div>
        </div>
    </div>
</template>
