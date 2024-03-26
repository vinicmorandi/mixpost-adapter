<script setup>
import {router} from "@inertiajs/vue3";
import {ref} from "vue";
import { useI18n } from "vue-i18n";
import useNotifications from "@/Composables/useNotifications";
import Panel from "@/Components/Surface/Panel.vue";
import Input from "@/Components/Form/Input.vue";
import PinterestIcon from "@/Icons/Pinterest.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import HorizontalGroup from "@/Components/Layout/HorizontalGroup.vue";
import Error from "@/Components/Form/Error.vue";
import ReadDocHelp from "@/Components/Util/ReadDocHelp.vue";
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

    router.put(route('mixpost.services.update', {service: 'pinterest'}), props.form, {
        preserveScroll: true,
        onSuccess() {
            notify('success', $t('service.service_saved', {service: 'Pinterest'}));
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
                <span class="mr-xs"><PinterestIcon class="text-pinterest"/></span>
                <span>Pinterest</span>
            </div>
        </template>

        <template #description>
            <p>
                <a href="https://developers.pinterest.com/apps/" class="link"
                   target="_blank">{{ $t('service.create_app', {name: 'Pinterest'}) }}</a>.
            </p>
            <ReadDocHelp :href="`${$page.props.mixpost.docs_link}/books/integration-of-social-platforms/page/pinterest`"
                         class="mt-xs"/>
        </template>

        <HorizontalGroup class="mt-lg">
            <template #title>
                <label for="id">App ID</label>
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
                <label for="secret">App secret key</label>
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
                <label for="env">Environment</label>
            </template>

            <Select v-model="form.environment"
                    :error="errors.hasOwnProperty('environment')"
                    id="env">
                <option value="sandbox">Sandbox</option>
                <option value="production">Production</option>
            </Select>

            <template #footer>
                <Error :message="errors.environment"/>
            </template>
        </HorizontalGroup>

        <PrimaryButton @click="save" class="mt-lg">{{ $t('general.save') }}</PrimaryButton>
    </Panel>
</template>
