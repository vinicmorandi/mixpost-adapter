<script setup>
import Radio from "@/Components/Form/Radio.vue";
import ProviderOptionWrap from "@/Components/ProviderVersionOptions/ProviderOptionWrap.vue";

defineProps(['options'])
</script>
<template>
    <ProviderOptionWrap :title="$t('service.facebook.page_options')" provider="facebook_page">
        <div>
            <div class="flex items-center space-x-sm">
                <label>
                    <Radio v-model:checked="options.type" value="post"/>
                    {{ $t('service.meta.post') }}
                </label>
                <label>
                    <Radio v-model:checked="options.type" value="reel"/>
                    {{ $t('service.meta.reel') }}
                </label>
                <label>
                    <Radio v-model:checked="options.type" value="story"/>
                    {{ $t('service.meta.story') }}
                </label>
            </div>
        </div>
    </ProviderOptionWrap>
</template>
