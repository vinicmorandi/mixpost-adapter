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

    router.put(route('mixpost.services.update', {service: 'tenor'}), props.form, {
        preserveScroll: true,
        onSuccess() {
            notify('success', $t('service.service_saved', {service: 'Tenor'}));
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
                Tenor
            </div>
        </template>

        <template #description>
            <p>{{ $t('service.tenor.use_gif') }}</p>
            <p>
                <a href="https://console.cloud.google.com/" class="link" target="_blank">
                    {{ $t('service.create_app', {name: 'Google Console'}) }}</a>.
            </p>
            <ReadDocHelp :href="`${$page.props.mixpost.docs_link}/books/integration-of-social-platforms/page/tenor`"
                         class="mt-xs"/>
        </template>

        <HorizontalGroup class="mt-lg">
            <template #title>
                <label for="key">API Key</label>
            </template>

            <Input v-model="form.client_id"
                   :error="errors.hasOwnProperty('client_id')"
                   type="text"
                   id="key"
                   autocomplete="off"/>

            <template #footer>
                <Error :message="errors.client_id"/>
            </template>
        </HorizontalGroup>

        <PrimaryButton @click="save" class="mt-lg">{{ $t('general.save') }}</PrimaryButton>
    </Panel>
</template>
