<script setup>
import Select from "@/Components/Form/Select.vue";
import ProviderOptionWrap from "@/Components/ProviderVersionOptions/ProviderOptionWrap.vue";
import Label from "@/Components/Form/Label.vue";

const props = defineProps(['options'])
</script>
<template>
    <ProviderOptionWrap :title="$t('service.provider_options', {provider: 'LinkedIn'})" provider="linkedin">
        <div class="w-full">
            <Label>{{ $t('service.linkedin.visibility') }}</Label>
            <Select v-model="options.visibility">
                <option value="PUBLIC">{{ $t('service.linkedin.public') }}</option>
                <option value="CONNECTIONS">{{ $t('service.linkedin.connections') }}</option>
            </Select>
        </div>
    </ProviderOptionWrap>
</template>
