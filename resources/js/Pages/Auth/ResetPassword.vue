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

const routePrefix = inject('routePrefix');

defineOptions({layout: MinimalLayout});

const props = defineProps({
    token: {
        type: String,
        required: true,
    }
})

const form = useForm({
    token: props.token,
    email: '',
    password: '',
    password_confirmation: '',
});

const success = ref(false)

const submit = () => {
    form.post(route(`${routePrefix}.password.store`));
}
</script>
<template>
    <Head :title="$t('auth.reset_password')"/>

    <div class="w-full sm:max-w-lg mx-auto">
        <form @submit.prevent="submit">
            <Panel>
                <template #title>
                    {{$t('auth.reset_password')}}
                </template>

                <Error v-for="error in form.errors" :message="error" class="mb-xs"/>

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

                <PrimaryButton :disabled="form.processing"
                               :isLoading="form.processing"
                               type="submit"
                               class="mt-lg">{{ $t('auth.reset_password') }}
                </PrimaryButton>
            </Panel>
        </form>
    </div>
</template>
