<script setup>
import { useI18n } from "vue-i18n";
import {router} from "@inertiajs/vue3";
import {ref} from "vue";
import useNotifications from "@/Composables/useNotifications";
import Panel from "@/Components/Surface/Panel.vue";
import Input from "@/Components/Form/Input.vue";
import GoogleIcon from "@/Icons/Google.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import HorizontalGroup from "@/Components/Layout/HorizontalGroup.vue";
import Error from "@/Components/Form/Error.vue";
import ReadDocHelp from "@/Components/Util/ReadDocHelp.vue";
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

    router.put(route('mixpost.services.update', {service: 'google'}), props.form, {
        preserveScroll: true,
        onSuccess() {
            notify('success', $t('service.service_saved', {service: 'Google'}));
        },
        onError: (err) => {
            errors.value = err;
        },
    });
}
</script>
<template>
    <Panel class="mt-lg">
        <template #title>
            <div class="flex items-center">
                <span class="mr-xs"><GoogleIcon class="text-google"/></span>
                <span>Google</span>
            </div>
        </template>

        <template #description>
            <p>
                <a href="https://console.developers.google.com/" class="link" target="_blank">
                    {{ $t('service.create_app', {name: 'Google Console'}) }}</a>.
            </p>
            <ReadDocHelp :href="`${$page.props.mixpost.docs_link}/books/integration-of-social-platforms/page/google`"
                         class="mt-xs"/>
        </template>

        <HorizontalGroup class="mt-lg">
            <template #title>
                <label for="id">Client ID</label>
            </template>

            <Input v-model="form.client_id"
                   :error="errors.hasOwnProperty('client_id')"
                   type="text"
                   id="id"
                   autocomplete="off"/>

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

        <PrimaryButton @click="save" class="mt-lg">{{ $t('general.save') }}</PrimaryButton>
    </Panel>
</template>
