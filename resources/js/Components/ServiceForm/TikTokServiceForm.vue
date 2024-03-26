<script setup>
import {ref} from "vue";
import { useI18n } from "vue-i18n";
import {router} from "@inertiajs/vue3";
import useNotifications from "@/Composables/useNotifications";
import Panel from "@/Components/Surface/Panel.vue";
import Input from "@/Components/Form/Input.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import HorizontalGroup from "@/Components/Layout/HorizontalGroup.vue";
import Error from "@/Components/Form/Error.vue";
import ReadDocHelp from "@/Components/Util/ReadDocHelp.vue";
import ProviderIcon from "../Account/ProviderIcon.vue";
import Select from "../Form/Select.vue";
import InputHidden from "../Form/InputHidden.vue";

const { t: $t } = useI18n()

const props = defineProps({
    form: {
        required: true,
        type: Object
    }
})

const {notify} = useNotifications();
const errors = ref({});

const save = () => {
    errors.value = {};

    router.put(route('mixpost.services.update', {service: 'tiktok'}), props.form, {
        preserveScroll: true,
        onSuccess() {
            notify('success', $t('service.service_saved', {service: 'TikTok'}));
        },
        onError: (err) => {
            errors.value = err;
        },
    });
}
</script>
<template>
    <Panel>
        <template #title>
            <div class="flex items-center">
                <span class="mr-xs">
                    <ProviderIcon provider="tiktok"/>
                </span>
                <span>TikTok</span>
            </div>
        </template>

        <template #description>
            <a href="https://developers.tiktok.com/apps/" class="link" target="_blank">
                {{ $t('service.create_app', {name: 'TikTok'}) }}</a>.
            <ReadDocHelp :href="`${$page.props.mixpost.docs_link}/books/integration-of-social-platforms/page/tiktok`"
                         class="mt-xs"/>
        </template>

        <HorizontalGroup class="mt-lg">
            <template #title>
                <label for="id">Client Key</label>
            </template>

            <Input v-model="form.client_id"
                   :error="errors.hasOwnProperty('client_id')"
                   type="text"
                   autocomplete="off"
                   id="id"
            />

            <template #footer>
                <Error :message="errors.client_id"/>
            </template>
        </HorizontalGroup>

        <HorizontalGroup class="mt-lg">
            <template #title>
                <label for="secret">Client secret</label>
            </template>

            <InputHidden v-model="form.client_secret"
                         :error="errors.hasOwnProperty('client_secret')"
                         id="secret"
                         autocomplete="new-password"/>

            <template #footer>
                <Error :message="errors.client_secret"/>
            </template>
        </HorizontalGroup>

        <HorizontalGroup class="mt-lg">
            <template #title>
                <label for="type">Share Type</label>
            </template>

            <Select v-model="form.share_type"
                    :error="errors.hasOwnProperty('share_type')"
                    id="type">
                <option value="inbox">Inbox</option>
                <option value="direct">Direct Post</option>
            </Select>

            <template #footer>
                <Error :message="errors.environment"/>
            </template>
        </HorizontalGroup>

        <PrimaryButton @click="save" class="mt-lg">{{ $t('general.save') }}</PrimaryButton>
    </Panel>
</template>
