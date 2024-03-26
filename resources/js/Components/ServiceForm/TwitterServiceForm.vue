<script setup>
import {ref} from "vue";
import { useI18n } from "vue-i18n";
import {router} from "@inertiajs/vue3";
import useNotifications from "@/Composables/useNotifications";
import Panel from "@/Components/Surface/Panel.vue";
import Input from "@/Components/Form/Input.vue";
import TwitterIcon from "@/Icons/Twitter.vue";
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

    router.put(route('mixpost.services.update', {service: 'twitter'}), props.form, {
        preserveScroll: true,
        onSuccess() {
            notify('success', $t('service.service_saved', {service: 'X'}));
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
                <span class="mr-xs"><TwitterIcon class="text-twitter"/></span>
                <span>X</span>
            </div>
        </template>

        <template #description>
            <a href="https://developer.twitter.com/en/portal/projects-and-apps" class="link" target="_blank">
                {{ $t('service.create_app', {name: 'X'}) }} </a>. {{ $t('service.twitter.form.edit_app') }}
            <ReadDocHelp :href="`${$page.props.mixpost.docs_link}/books/integration-of-social-platforms/page/twitter`"
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

        <HorizontalGroup class="mt-lg">
            <template #title>
                <label for="secret">API Secret</label>
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
                <label for="tier">Tier</label>
            </template>

            <Select v-model="form.tier"
                    :error="errors.hasOwnProperty('tier')"
                    id="tier">
                <option value="legacy">Legacy</option>
                <option value="free">Free</option>
                <option value="basic">Basic</option>
            </Select>

            <template #footer>
                <Error :message="errors.environment"/>
            </template>
        </HorizontalGroup>

        <PrimaryButton @click="save" class="mt-lg">{{ $t('general.save') }}</PrimaryButton>
    </Panel>
</template>
