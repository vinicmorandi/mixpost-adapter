<script setup>
import {Head, useForm} from '@inertiajs/vue3';
import MinimalLayout from "@/Layouts/Minimal.vue";
import Panel from "@/Components/Surface/Panel.vue";
import HorizontalGroup from "@/Components/Layout/HorizontalGroup.vue";
import Error from "@/Components/Form/Error.vue";
import Input from "@/Components/Form/Input.vue";
import Select from "@/Components/Form/Select.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";

defineOptions({layout: MinimalLayout});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
});

const submit = () => {
    form.post(route('mixpost.installation'))
}
</script>
<template>
    <Head :title="$t('installation.installation')"/>

    <div class="w-full sm:max-w-lg mx-auto">
        <form @submit.prevent="submit">
            <Panel>
                <template #title>
                    {{ $t('installation.installation') }}
                </template>
                <template #description>
                    Bananilson silva
                </template>

                <Error v-for="error in form.errors" :message="error" class="mb-xs"/>

                <HorizontalGroup>
                    <template #title>
                        <label for="name">{{ $t('general.name') }}</label>
                    </template>

                    <Input v-model="form.name" :error="form.errors.name" type="text" id="name" class="w-full" required
                           autofocus autocomplete="name"/>
                </HorizontalGroup>

                <HorizontalGroup class="mt-md">
                    <template #title>
                        <label for="email">{{ $t('general.email') }}</label>
                    </template>

                    <Input v-model="form.email" :error="form.errors.email" type="email" id="email" class="w-full"
                           required autocomplete="username"/>
                </HorizontalGroup>

                <HorizontalGroup class="mt-md">
                    <template #title>
                        <label for="password">{{ $t('auth.password') }}</label>
                    </template>

                    <Input v-model="form.password" :error="form.errors.password" type="password" id="password"
                           class="w-full" required autocomplete="new-password"/>
                </HorizontalGroup>

                <HorizontalGroup class="mt-md">
                    <template #title>
                        <label for="password_confirmation">{{ $t('auth.confirm_password') }}</label>
                    </template>

                    <div class="w-full">
                        <Input v-model="form.password_confirmation" :error="form.errors.password_confirmation"
                               type="password" id="password_confirmation" class="w-full" required
                               autocomplete="new-password"/>
                    </div>
                </HorizontalGroup>

                <HorizontalGroup v-if="form.errors.timezone" class="mt-lg">
                    <template #title>{{ $t('general.timezone') }}</template>

                    <div>
                        <Select v-model="form.timezone">
                            <optgroup v-for="(list, groupName) in $page.props.timezone_list" :label="groupName">
                                <option v-for="(timezoneName,timezoneCode) in list" :value="timezoneCode">
                                    {{ timezoneName }}
                                </option>
                            </optgroup>
                        </Select>
                    </div>

                    <template #footer>
                        <Error :message="$t('installation.select_timezone')"/>
                    </template>
                </HorizontalGroup>

                <PrimaryButton :disabled="form.processing"
                               :isLoading="form.processing"
                               type="submit"
                               class="mt-lg">{{ $t('user.create_user') }}
                </PrimaryButton>
            </Panel>
        </form>
    </div>
</template>
