<script setup>
import {computed, inject, onMounted} from "vue";
import {usePage, Link} from "@inertiajs/vue3";
import useAuth from "../../Composables/useAuth";
import {snakeCase} from "lodash";
import useMedia from "@/Composables/useMedia";
import MediaSelectable from "@/Components/Media/MediaSelectable.vue";
import MediaFile from "@/Components/Media/MediaFile.vue";
import Masonry from "@/Components/Layout/Masonry.vue";
import SearchInput from "@/Components/Util/SearchInput.vue";
import MediaCredit from "@/Components/Media/MediaCredit.vue";
import NoResult from "@/Components/Util/NoResult.vue";
import Alert from "@/Components/Util/Alert.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";

const workspaceCtx = inject('workspaceCtx');

const props = defineProps({
    columns: {
        type: Number,
        default: 3
    }
})

const {user} = useAuth();

const appName = computed(() => {
    return snakeCase(usePage().props.app.name);
})

const enabled = computed(() => {
    return usePage().props.is_configured_service.unsplash;
})

const {
    isLoaded,
    keyword,
    page,
    items,
    endlessPagination,
    selected,
    toggleSelect,
    deselectAll,
    isSelected,
    createObserver
} = useMedia('mixpost.media.fetchStock', {workspace: workspaceCtx.id});

onMounted(() => {
    if (enabled.value) {
        createObserver();
    }
});

defineExpose({selected, deselectAll})
</script>
<template>
    <div v-if="enabled">
        <SearchInput v-model="keyword" :placeholder="$t('service.unsplash.search')"/>

        <div v-if="items.length" class="mt-lg">
            <Masonry :items="items" :columns="columns">
                <template #default="{item}">
                    <MediaSelectable v-if="item" :active="isSelected(item)" @click="toggleSelect(item)">
                        <MediaFile :media="item" class="group">
                            <MediaCredit>
                                <div>{{ $t('media.image_source') }}: <a
                                    :href="`https://unsplash.com/?utm_source=${appName}&utm_medium=referral`"
                                    target="_blank" class="link">Unsplash</a>
                                </div>
                                <div>{{ $t('media.author') }}: <a :href="`${item.credit_url}?utm_source=${appName}&utm_medium=referral`"
                                           target="_blank" class="link">{{ item.name }}</a>
                                </div>
                            </MediaCredit>
                        </MediaFile>
                    </MediaSelectable>
                </template>
            </Masonry>
        </div>

        <NoResult v-if="isLoaded && !items.length" class="mt-lg">{{ $t('media.no_images_found') }}</NoResult>

        <div ref="endlessPagination" class="-z-10 w-full"/>
    </div>

    <template v-if="!enabled">
        <Alert variant="warning" :closeable="false">
            {{ $t('service.not_configured_service', {service: 'Unsplash'}) }}
        </Alert>

        <template v-if="user.is_admin">
            <Link :href="route('mixpost.services.index')" class="block mt-md">
                <PrimaryButton>{{ $t('media.click_configure') }}</PrimaryButton>
            </Link>
        </template>
    </template>
</template>
