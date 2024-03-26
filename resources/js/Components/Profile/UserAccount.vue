<script setup>
import { useI18n } from "vue-i18n";
import {useForm} from "@inertiajs/vue3";
import useAuth from "../../Composables/useAuth";
import useNotifications from "@/Composables/useNotifications";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import HorizontalGroup from "@/Components/Layout/HorizontalGroup.vue";
import Input from "../Form/Input.vue";
import Error from "../Form/Error.vue";
import Label from "../Form/Label.vue";

const { t: $t } = useI18n()

const {user} = useAuth();
const {notify} = useNotifications();

const form = useForm({
    name: user.value.name,
    email: user.value.email,
});
const save = () => {
    form.put(route('mixpost.profile.updateUser'), {
        preserveScroll: true,
        onSuccess() {
            notify('success', $t('account.account_updated'));
        }
    });
}
</script>
<template>
    <form @submit.prevent="save">
        <Error v-for="error in form.errors" :message="error" class="mb-xs"/>

        <HorizontalGroup>
            <template #title>
                <label for="name">{{ $t('general.name') }}</label>
            </template>

            <Input type="text" v-model="form.name" :error="form.errors.name" id="name"/>
        </HorizontalGroup>

        <HorizontalGroup class="mt-lg">
            <template #title>
                <label for="email">{{ $t('general.email') }}</label>
            </template>

            <Input type="email" v-model="form.email" :error="form.errors.email" id="email"/>
        </HorizontalGroup>

        <PrimaryButton type="submit" class="mt-lg">{{$t('general.save')}}</PrimaryButton>
    </form>
</template>
