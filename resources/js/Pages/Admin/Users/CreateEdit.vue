<script setup>
import {computed, inject} from "vue";
import {Head, useForm, Link, router} from '@inertiajs/vue3';
import {useI18n} from "vue-i18n";
import usePageMode from "@/Composables/usePageMode";
import useAuth from "@/Composables/useAuth";
import useNotifications from "@/Composables/useNotifications";
import AdminLayout from "@/Layouts/Admin.vue";
import PageHeader from "@/Components/DataDisplay/PageHeader.vue";
import Panel from "@/Components/Surface/Panel.vue";
import HorizontalGroup from "@/Components/Layout/HorizontalGroup.vue";
import Error from "@/Components/Form/Error.vue";
import Input from "@/Components/Form/Input.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import Radio from "@/Components/Form/Radio.vue";
import SecondaryButton from "../../../Components/Button/SecondaryButton.vue";
import DangerButton from "../../../Components/Button/DangerButton.vue";
import Eye from "../../../Icons/Eye.vue";
import Trash from "../../../Icons/Trash.vue";
import InputHidden from "../../../Components/Form/InputHidden.vue";

defineOptions({layout: AdminLayout});

const { t: $t } = useI18n()

const routePrefix = inject('routePrefix');
const confirmation = inject('confirmation');

const props = defineProps({
    mode: {
        type: String,
        default: 'create',
    },
    user: {
        type: Object
    }
})

const {notify} = useNotifications();
const {user: authUser} = useAuth();

const pageTitle = computed(() => {
    if (isCreate.value) {
        return $t('user.create_user');
    }

    return 'Edit user';
});

const {isCreate, isEdit} = usePageMode();

const form = useForm({
    name: isEdit.value ? props.user.name : '',
    email: isEdit.value ? props.user.email : '',
    is_admin: isEdit.value ? props.user.is_admin : false,
    password: '',
    password_confirmation: ''
});

const store = () => {
    form.post(route('mixpost.users.store'), {
        preserveScroll: true,
        preserveState: true
    })
}

const update = () => {
    form.put(route('mixpost.users.update', {user: props.user.id}), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            const messages = [$t('user.user_has_been_updated'), $t('auth.password_has_been_changed')];

            notify('success', !form.password ? messages[0] : messages.join("\n"));
        }
    })
}

const submit = () => {
    if (isCreate.value) {
        store();
    }

    if (isEdit.value) {
        update();
    }
}

const deleteUser = () => {
    confirmation()
        .title($t('user.delete_user'))
        .description($t('user.confirm_delete_user'))
        .destructive()
        .onConfirm((dialog) => {
            dialog.isLoading(true);

            router.delete(route(`${routePrefix}.users.delete`, {user: props.user.id}), {
                onSuccess(response) {
                    if (!response.props.flash.error) {
                        notify('success', $t('user.delete_user'));
                    }
                },
                onFinish() {
                    dialog.isLoading(false);
                }
            })
        })
        .show();
}
</script>
<template>
    <Head :title="pageTitle"/>

    <div class="row-py mb-2xl w-full sm:max-w-3xl mx-auto">
        <PageHeader :title="pageTitle">
            <div v-if="isEdit" class="flex">
                <Link :href="route(`${routePrefix}.users.view`, {user: user.id})" class="mr-xs">
                    <SecondaryButton size="sm">
                        <template #icon>
                            <Eye/>
                        </template>
                        {{ $t('general.view') }}
                    </SecondaryButton>
                </Link>
                <DangerButton @click="deleteUser" size="sm">
                    <template #icon>
                        <Trash/>
                    </template>
                </DangerButton>
            </div>
        </PageHeader>

        <div class="row-px">
            <form method="post" @submit.prevent="submit">
                <Panel>
                    <template #title>{{ $t('user.user_details') }}</template>
                    <HorizontalGroup>
                        <template #title>
                            <label for="name">{{ $t('general.name') }}</label>
                        </template>

                        <Input v-model="form.name"
                               :error="form.errors.name"
                               type="text"
                               id="name"
                               class="w-full"
                               autofocus
                               required/>

                        <template #footer>
                            <Error :message="form.errors.name"/>
                        </template>
                    </HorizontalGroup>

                    <HorizontalGroup class="mt-md">
                        <template #title>
                            <label for="email">{{ $t('general.email') }}</label>
                        </template>

                        <Input v-model="form.email"
                               :error="form.errors.email"
                               type="email"
                               id="email"
                               class="w-full"
                               autocomplete="off"
                               required/>

                        <template #footer>
                            <Error :message="form.errors.email"/>
                        </template>
                    </HorizontalGroup>

                    <HorizontalGroup class="mt-lg">
                        <template #title>{{ $t("user.system_admin") }}</template>

                        <div class="flex items-center space-x-sm">
                            <label>
                                <Radio v-model:checked="form.is_admin" :value="false"
                                       :disabled="isEdit && user.id === authUser.id"/>
                                {{ $t("general.no") }}</label>
                            <label>
                                <Radio v-model:checked="form.is_admin" :value="true"
                                       :disabled="isEdit && user.id === authUser.id"/>
                                {{ $t("general.yes") }}</label>
                        </div>
                    </HorizontalGroup>
                </Panel>

                <Panel class="mt-lg">
                    <template #title>{{ $t('auth.password') }}</template>
                    <template #description>
                        <span v-if="isEdit">
                            {{ $t('auth.leave_blank_password') }}
                        </span>
                    </template>

                    <HorizontalGroup class="mt-md">
                        <template #title>
                            <label for="password">{{ $t('auth.password') }}</label>
                        </template>

                        <InputHidden v-model="form.password"
                                     :error="form.errors.password"
                                     id="password"
                                     :required="isCreate"
                                     autocomplete="new-password"/>

                        <template #footer>
                            <Error :message="form.errors.password"/>
                        </template>
                    </HorizontalGroup>

                    <HorizontalGroup class="mt-md">
                        <template #title>
                            <label for="password_confirmation">{{ $t('auth.confirm_password') }}</label>
                        </template>

                        <InputHidden v-model="form.password_confirmation"
                                     :error="form.errors.password_confirmation"
                                     id="password_confirmation"
                                     :required="!!form.password"
                                     autocomplete="new-password"/>

                        <template #footer>
                            <Error :message="form.errors.password_confirmation"/>
                        </template>
                    </HorizontalGroup>
                </Panel>

                <PrimaryButton type="submit" :disabled="form.processing" class="mt-lg">{{
                        isCreate ? $t('general.create') : $t('general.update')
                    }}
                </PrimaryButton>
            </form>
        </div>
    </div>
</template>
