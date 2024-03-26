<script setup>
import {useForm, usePage} from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import {inject, ref} from "vue";
import useAuth from "../../Composables/useAuth";
import useNotifications from "../../Composables/useNotifications";
import DialogModal from "../Modal/DialogModal.vue";
import SecondaryButton from "../Button/SecondaryButton.vue";
import Input from "../Form/Input.vue";
import Label from "../Form/Label.vue";
import Checkbox from "../Form/Checkbox.vue";
import Error from "../Form/Error.vue";
import VerticalGroup from "../Layout/VerticalGroup.vue";
import LabelSuffix from "../Form/LabelSuffix.vue";
import SuccessButton from "../Button/SuccessButton.vue";
import UploadButton from "../Media/UploadButton.vue";

const { t: $t } = useI18n()

const routePrefix = inject('routePrefix');

const show = ref(false);

const open = () => {
    show.value = true;
}

const close = () => {
    show.value = false;
}

const {user} = useAuth();
const {notify} = useNotifications();

const form = useForm({
    logo_url: usePage().props.mixpost.theme.logo,
    brand_name: usePage().props.app.name,
    email: user.value.email,
    register_url: '',
    destroy: false,
})

const generate = () => {
    form.post(route(`${routePrefix}.pages.generateSamples`), {
        preserveScroll: true,
        onSuccess() {
            notify('success', $t('page.sample_pages_generated'));
            close();
            form.reset();
        }
    });
}
</script>
<template>
    <SuccessButton @click="open">{{ $t('page.generate_samples') }}</SuccessButton>

    <DialogModal :show="show"
                 max-width="sm"
                 :closeable="true"
                 :scrollable-body="true"
                 @close="close">
        <template #header>
            {{ $t('page.generate_page_samples') }}
        </template>

        <template #body>
            <VerticalGroup>
                <template #title>
                    <label for="brand_name"> {{ $t('page.brand_logo') }}
                        <LabelSuffix :danger="true">*</LabelSuffix>
                    </label>
                </template>

                <div>
                    <UploadButton :withPreload="true" @upload="(e)=> {
                        if(e.is_local_driver) {
                           form.logo_url = `/storage/${e.path}`;
                            return;
                        }

                         form.logo_url = e.url;
                        }">
                        <SecondaryButton>{{ $t('general.upload') }}</SecondaryButton>
                    </UploadButton>

                    <img v-if="form.logo_url"
                         :src="form.logo_url"
                         class="w-auto max-h-20 mt-xs"
                         alt="Logo"/>
                </div>

                <template #footer>
                    <Error :message="form.errors.logo_url"/>
                </template>
            </VerticalGroup>

            <VerticalGroup class=" mt-lg
                ">
                <template #title>
                    <label for="brand_name">{{ $t('page.brand_name') }}
                        <LabelSuffix :danger="true">*</LabelSuffix>
                    </label>
                </template>

                <Input type="text" v-model="form.brand_name" :error="form.errors.brand_name" id="brand_name"/>

                <template #footer>
                    <Error :message="form.errors.brand_name"/>
                </template>
            </VerticalGroup>

            <VerticalGroup class="mt-lg">
                <template #title>
                    <label for="email">{{ $t('page.contact_email') }}
                        <LabelSuffix :danger="true">*</LabelSuffix>
                    </label>
                </template>

                <Input type="text" v-model="form.email" :error="form.errors.email" id="email"/>

                <template #footer>
                    <Error :message="form.errors.email"/>
                </template>
            </VerticalGroup>

            <VerticalGroup class="mt-lg">
                <template #title>
                    <label for="register_url">{{ $t('page.register_url') }}</label>
                </template>

                <Input type="text" v-model="form.register_url" :error="form.errors.register_url" id="register_url"/>

                <template #footer>
                    <Error :message="form.errors.register_url"/>
                </template>
            </VerticalGroup>

            <VerticalGroup class="mt-lg">
                <label>
                    <Checkbox v-model:checked="form.destroy" :value="false"/>
                    <span class="ml-xs">{{ $t('page.destroy_existing_pages') }}</span>
                </label>

                <template #footer>
                    <Error :message="form.errors.destroy"/>
                </template>
            </VerticalGroup>
        </template>

        <template #footer>
            <SecondaryButton @click="close" class="mr-xs">{{ $t('general.cancel') }}</SecondaryButton>
            <SuccessButton @click="generate"
                           :disabled="form.processing"
                           :isLoading="form.processing">
                {{ $t('page.generate') }}
            </SuccessButton>
        </template>
    </DialogModal>
</template>
