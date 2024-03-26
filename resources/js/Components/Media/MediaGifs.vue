<script setup>
import {computed, inject, onMounted} from "vue";
import {usePage, Link} from "@inertiajs/vue3";
import useAuth from "../../Composables/useAuth";
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

const enabled = computed(() => {
    return usePage().props.is_configured_service.tenor;
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
} = useMedia('mixpost.media.fetchGifs', {workspace: workspaceCtx.id});

onMounted(() => {
    if (enabled.value) {
        createObserver();
    }
});

defineExpose({selected, deselectAll})
</script>
<template>
    <div v-if="enabled">
        <SearchInput v-model="keyword" :placeholder="$t('service.tenor.search_gifs')"/>

        <div v-if="items.length" class="mt-lg">
            <Masonry :items="items" :columns="columns">
                <template #default="{item}">
                    <MediaSelectable v-if="item" :active="isSelected(item)" @click="toggleSelect(item)">
                        <MediaFile :media="item" :key="item.id" class="group">
                            <MediaCredit>
                                {{ $t('service.tenor.gif') }}
                            </MediaCredit>
                        </MediaFile>
                    </MediaSelectable>
                </template>
            </Masonry>
        </div>

        <NoResult v-if="isLoaded && !items.length" class="mt-lg">{{ $t('media.no_gifs_found') }}</NoResult>

        <div ref="endlessPagination" class="-z-10 w-full"/>
    </div>

    <template v-if="!enabled">
        <Alert variant="warning" :closeable="false">
            {{ $t('service.not_configured_service', {service: 'Tenor'}) }}
        </Alert>

        <template v-if="user.is_admin">
            <Link :href="route('mixpost.services.index')" class="block mt-md">
                <PrimaryButton> {{ $t('media.click_configure') }}</PrimaryButton>
            </Link>
        </template>
    </template>
</template>
