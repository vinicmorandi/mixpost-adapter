<script setup>
import {computed, inject, ref} from "vue";
import {Head, useForm} from '@inertiajs/vue3';
import MinimalLayout from "@/Layouts/Minimal.vue";
import Panel from "@/Components/Surface/Panel.vue";
import HorizontalGroup from "@/Components/Layout/HorizontalGroup.vue";
import Error from "@/Components/Form/Error.vue";
import Input from "@/Components/Form/Input.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import Label from "../../Components/Form/Label.vue";
import Flex from "../../Components/Layout/Flex.vue";

defineOptions({layout: MinimalLayout});

const routePrefix = inject('routePrefix');

const CODE = 'code';
const RECOVERY_CODE = 'recovery_code';

const type = ref(CODE);

const form = useForm({
    code: '',
    recovery_code: '',
});

const isUsingAppCode = computed(() => {
    return type.value === CODE;
});

const isUsingRecoveryCode = computed(() => {
    return type.value === RECOVERY_CODE;
});

const useAppCode = () => {
    type.value = CODE;
}

const useRecoveryCode = () => {
    type.value = RECOVERY_CODE;
}

const submit = () => {
    form.post(route('mixpost.two-factor.login'))
}
</script>
<template>
    <Head :title="$t('auth.sign_in')"/>

    <div class="w-full sm:max-w-lg mx-auto">
        <form @submit.prevent="submit">
            <Panel>
                <template #title>
                    {{ $t('auth.two_factor_authentication') }}
                </template>

                <template #description>
                    <span v-if="isUsingAppCode"> {{ $t('auth.confirm_access_authentication_code') }}</span>
                    <span v-if="isUsingRecoveryCode">{{ $t('auth.confirm_access_emergency_codes') }}</span>
                </template>

                <Error v-for="error in form.errors" :message="error" class="mb-xs"/>

                <template v-if="isUsingAppCode">
                    <HorizontalGroup>
                        <template #title>
                            <label for="code">{{ $t("profile.code") }}</label>
                        </template>

                        <Input v-model="form.code" type="text" id="code" class="w-full" autofocus required/>
                    </HorizontalGroup>
                </template>

                <template v-if="isUsingRecoveryCode">
                    <HorizontalGroup>
                        <template #title>
                            <label for="recovery_code">{{ $t("auth.recovery_code") }}</label>
                        </template>

                        <Input v-model="form.recovery_code" type="text" id="recovery_code" class="w-full" autofocus
                               required/>
                    </HorizontalGroup>
                </template>

                <Flex class="justify-between mt-lg">
                    <PrimaryButton :disabled="form.processing"
                                   :isLoading="form.processing"
                                   type="submit">
                        {{ $t('auth.login') }}
                    </PrimaryButton>

                    <template v-if="isUsingAppCode">
                        <div @click="useRecoveryCode"
                             role="button"
                             class="link-primary">{{ $t("auth.use_recovery_code") }}
                        </div>
                    </template>

                    <template v-if="isUsingRecoveryCode">
                        <div @click="useAppCode"
                             role="button"
                             class="link-primary">{{ $t("auth.use_authentication_code") }}
                        </div>
                    </template>
                </Flex>
            </Panel>
        </form>
    </div>
</template>
