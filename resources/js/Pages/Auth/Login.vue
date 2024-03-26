<script setup>
import {inject} from "vue";
import {Head, useForm, Link} from '@inertiajs/vue3';
import useEnterpriseConsole from "../../Composables/useEnterpriseConsole";
import MinimalLayout from "@/Layouts/Minimal.vue";
import Panel from "@/Components/Surface/Panel.vue";
import HorizontalGroup from "@/Components/Layout/HorizontalGroup.vue";
import Error from "@/Components/Form/Error.vue";
import Input from "@/Components/Form/Input.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import Label from "../../Components/Form/Label.vue";
import Checkbox from "../../Components/Form/Checkbox.vue";
import Flex from "../../Components/Layout/Flex.vue";

defineOptions({layout: MinimalLayout});

const routePrefix = inject('routePrefix');

const form = useForm({
    email: '',
    password: '',
    remember: true,
});

const submit = () => {
    form.post(route('mixpost.login'))
}

const {enterpriseConsole} = useEnterpriseConsole()
</script>
<template>
    <Head :title="$t('auth.sign_in')"/>

    <div class="w-full sm:max-w-lg mx-auto">
        <form @submit.prevent="submit">
            <Panel>
                <template #title>
                    {{ $t('auth.login_account') }}
                </template>

                <template #description>
                    {{ $t('auth.enter_details') }}
                </template>

                <Error v-for="error in form.errors" :message="error" class="mb-xs"/>

                <HorizontalGroup>
                    <template #title>
                        <label for="email">{{ $t('general.email') }}</label>
                    </template>

                    <div class="w-full">
                        <Input v-model="form.email" type="email" id="email" class="w-full" required/>
                    </div>
                </HorizontalGroup>

                <HorizontalGroup class="mt-md">
                    <template #title>
                        <label for="password">{{ $t('auth.password') }}</label>
                    </template>

                    <div class="w-full">
                        <Input v-model="form.password" type="password" id="password" class="w-full" required/>
                    </div>
                </HorizontalGroup>

                <div class="mt-md">
                    <Label>
                        <Checkbox v-model:checked="form.remember"/>
                        {{ $t('auth.remember_me') }}
                    </Label>
                </div>

                <Flex class="justify-between mt-lg">
                    <PrimaryButton :disabled="form.processing"
                                   :isLoading="form.processing"
                                   type="submit">
                        {{ $t('auth.login') }}
                    </PrimaryButton>

                    <template v-if="$page.props.is_forgot_password_enabled">
                        <Link
                            :href="route(`${routePrefix}.password.request`)"
                            class="link-primary">{{ $t('auth.forgot_password') }}
                        </Link>
                    </template>
                </Flex>

                <template v-if="enterpriseConsole.registration_url">
                    <div class="text-center mt-2xl">
                        <p class="text-black">{{ $t('auth.dont_have_account') }}
                            <a :href="enterpriseConsole.registration_url"
                               class="link-primary">{{ $t('auth.register_here') }}
                            </a>
                        </p>
                    </div>
                </template>
            </Panel>
        </form>
    </div>
</template>
