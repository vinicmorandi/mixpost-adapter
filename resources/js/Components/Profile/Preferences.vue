<script setup>
import {useI18n} from "vue-i18n";
import {Trans} from '@/Services/Internationalization'
import useNotifications from "@/Composables/useNotifications";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import Radio from "@/Components/Form/Radio.vue";
import Select from "@/Components/Form/Select.vue";
import HorizontalGroup from "@/Components/Layout/HorizontalGroup.vue";
import Error from "../Form/Error.vue";

const I18n = useI18n();
const {t: $t} = I18n;

const props = defineProps({
    form: {
        type: Object,
        require: true,
    },
    locales: {
        type: Array,
        required: true,
    },
    timezone_list: {
        type: Object,
        required: true,
    }
})

const getLocaleDirection = (locale) => {
    return props.locales.find((item) => item.long === locale).direction || 'ltr';
}

const isLocaleInBeta = (locale) => {
    const localesInProduction = ['en-GB', 'fr-CA', 'ar-SA', 'de-DE', 'es-MX'];

    return !localesInProduction.includes(locale);
}

const {notify} = useNotifications();
const save = () => {
    props.form.put(route('mixpost.profile.updatePreferences'), {
        preserveScroll: true,
        onSuccess() {
            Trans.changeLocale(I18n, props.form.locale, getLocaleDirection(props.form.locale));
            notify('success', $t('profile.preferences_updated'));
        }
    });
}
</script>
<template>
    <div>
        <Error v-for="error in form.errors" :message="error" class="mb-xs"/>

        <HorizontalGroup>
            <template #title>{{ $t('general.language') }}</template>

            <div>
                <Select v-model="form.locale">
                    <option v-for="locale in locales" :value="locale.long">
                        {{ locale.english }} - {{ locale.native }} - ({{ locale.long }})
                        {{ isLocaleInBeta(locale.long) ? '[Beta]' : '' }}
                    </option>
                </Select>

                <span v-if="isLocaleInBeta(form.locale)" class="text-xs text-gray-500 mt-xs block">
                  🚧 Language <span class="font-medium">{{ form.locale }}</span> is in beta and may not be fully translated or can contain errors.
                </span>
            </div>
        </HorizontalGroup>

        <HorizontalGroup class="mt-lg">
            <template #title>{{ $t('general.timezone') }}</template>

            <div>
                <Select v-model="form.timezone">
                    <optgroup v-for="(list, groupName) in timezone_list" :label="groupName">
                        <option v-for="(timezoneName,timezoneCode) in list" :value="timezoneCode">
                            {{ timezoneName }}
                        </option>
                    </optgroup>
                </Select>
            </div>
        </HorizontalGroup>

        <HorizontalGroup class="mt-lg">
            <template #title>{{ $t('profile.time_format') }}</template>

            <div class="flex items-center space-x-sm">
                <label>
                    <Radio v-model:checked="form.time_format" :value="12"/>
                    12 {{ $t('profile.hour') }}</label>
                <label>
                    <Radio v-model:checked="form.time_format" :value="24"/>
                    24 {{ $t('profile.hour') }}</label>
            </div>
        </HorizontalGroup>

        <HorizontalGroup class="mt-lg">
            <template #title>{{ $t('profile.first_day_week') }}</template>

            <div class="flex items-center space-x-sm">
                <label>
                    <Radio v-model:checked="form.week_starts_on" :value="0"/>
                    {{ $t('profile.sunday') }}</label>
                <label>
                    <Radio v-model:checked="form.week_starts_on" :value="1"/>
                    {{ $t('profile.monday') }}</label>
            </div>
        </HorizontalGroup>

        <PrimaryButton @click="save" class="mt-lg">{{ $t('general.save') }}</PrimaryButton>
    </div>
</template>
