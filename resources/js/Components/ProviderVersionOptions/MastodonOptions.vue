<script setup>
import ProviderOptionWrap from "@/Components/ProviderVersionOptions/ProviderOptionWrap.vue";
import Checkbox from "../Form/Checkbox.vue";

defineProps(['options'])
</script>
<template>
    <ProviderOptionWrap :title="$t('service.provider_options', {provider: 'Mastodon'})" provider="mastodon">
        <label>
            <Checkbox v-model:checked="options.sensitive"/>
            {{ $t('service.mastodon.mark_media') }}
        </label>
    </ProviderOptionWrap>
</template>
