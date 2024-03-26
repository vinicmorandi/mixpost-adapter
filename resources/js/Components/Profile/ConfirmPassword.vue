<script setup>
import {inject} from "vue";
import {useForm} from "@inertiajs/vue3";
import DialogModal from "../Modal/DialogModal.vue";
import SecondaryButton from "../Button/SecondaryButton.vue";
import PrimaryButton from "../Button/PrimaryButton.vue";
import Label from "../Form/Label.vue";
import Input from "../Form/Input.vue";
import HorizontalGroup from "../Layout/HorizontalGroup.vue";
import Error from "../Form/Error.vue";

const routePrefix = inject('routePrefix');

const props = defineProps({
    show: {
        type: Boolean,
        default: true,
    }
})

const emit = defineEmits(['close', 'confirm'])

const close = () => {
    emit('close');
}

const form = useForm({
    password: '',
});

const confirm = () => {
    form.post(route(`${routePrefix}.profile.confirmPassword`), {
        preserveScroll: true,
        onSuccess() {
            emit('confirm');
            form.reset();
            close();
        }
    });
}
</script>
<template>
    <DialogModal :show="show"
                 max-width="lg"
                 :closeable="true"
                 :scrollable-body="true"
                 @close="close">
        <template #header>
            {{ $t('auth.confirm_password') }}
        </template>

        <template #body>
            <template v-if="show">
                <div>{{ $t('profile.security_confirm_password') }}</div>

                <HorizontalGroup class="mt-lg">
                    <template #title>
                        <label for="password">{{ $t('auth.password') }}</label>
                    </template>

                    <Input v-model="form.password" :error="form.errors.password" type="password" id="password"
                           class="w-full"
                           autocomplete="password"/>

                    <template #footer>
                        <Error :message="form.errors.password"/>
                    </template>
                </HorizontalGroup>
            </template>
        </template>

        <template #footer>
            <SecondaryButton @click="close" class="mr-xs">{{ $t('general.cancel') }}</SecondaryButton>
            <PrimaryButton @click="confirm"
                           :disabled="form.processing"
                           :isLoading="form.processing"
                           class="mr-xs">{{ $t('general.confirm') }}
            </PrimaryButton>
        </template>
    </DialogModal>
</template>
