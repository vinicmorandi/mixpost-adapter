<script setup>
import {inject, ref} from "vue";
import {Head, useForm} from '@inertiajs/vue3';
import MinimalLayout from "@/Layouts/Minimal.vue";
import Panel from "@/Components/Surface/Panel.vue";
import HorizontalGroup from "@/Components/Layout/HorizontalGroup.vue";
import Error from "@/Components/Form/Error.vue";
import Input from "@/Components/Form/Input.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import Label from "../../Components/Form/Label.vue";
import Alert from "../../Components/Util/Alert.vue";

const routePrefix = inject('routePrefix');

defineOptions({layout: MinimalLayout});

const form = useForm({
    email: '',
});

const success = ref(false)

const submit = () => {
    form.post(route(`${routePrefix}.password.email`), {
        onSuccess: () => {
            success.value = true;
            form.reset();
        }
    });
}
</script>
<template>
    <Head :title="$t('auth.forgot_password')"/>

    <div class="w-full sm:max-w-lg mx-auto">
        <form @submit.prevent="submit">
            <Panel>
                <template #title>
                    {{ $t('auth.confirm_forgot_password') }}
                </template>

                <template #description>
                    {{ $t('auth.let_email_address') }}
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

                <Alert
                    v-if="success"
                    :closeable="false"
                    variant="success"
                    class="mt-lg"
                >
                    {{ $t('auth.password_reset_link') }}
                </Alert>

                <PrimaryButton :disabled="form.processing"
                               :isLoading="form.processing"
                               type="submit"
                               class="mt-lg"> {{ $t('auth.send_password_reset') }}
                </PrimaryButton>
            </Panel>
        </form>
    </div>
</template>
