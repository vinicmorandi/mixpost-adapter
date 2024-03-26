<script setup>
import Select from "@/Components/Form/Select.vue";
import Input from "@/Components/Form/Input.vue";
import ProviderOptionWrap from "@/Components/ProviderVersionOptions/ProviderOptionWrap.vue";
import Label from "../Form/Label.vue";

defineProps(['options'])
</script>
<template>
    <ProviderOptionWrap :title="$t('service.provider_options', {provider: 'YouTube'})" provider="youtube">
        <div>
            <div class="w-full mb-xs">
                <Input v-model="options.title" type="text" :placeholder="$t('service.youtube.title')" id="youtube_title"/>
            </div>

            <div class="w-full">
                <Label for="privacy-status">{{ $t('service.youtube.privacy_config') }}</Label>
                <Select v-model="options.status" id="privacy-status">
                    <option value="public">{{ $t('service.youtube.public') }}</option>
                    <option value="private">{{ $t('service.youtube.private') }}</option>
                    <option value="unlisted">{{ $t('service.youtube.unlisted') }}</option>
                </Select>
            </div>
        </div>
    </ProviderOptionWrap>
</template>
