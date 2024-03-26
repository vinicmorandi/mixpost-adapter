<script setup>
import {ref} from "vue";
import {Head, useForm, Link} from '@inertiajs/vue3';
import useNotifications from "@/Composables/useNotifications";
import MinimalLayout from '@/Layouts/Minimal.vue';
import Preferences from "../../Components/Profile/Preferences.vue";
import UserAccount from "../../Components/Profile/UserAccount.vue";
import TwoFactorAuth from "../../Components/Profile/TwoFactorAuth.vue";
import Password from "../../Components/Profile/Password.vue";
import SecondaryButton from "../../Components/Button/SecondaryButton.vue";
import ActionSection from "../../Components/Surface/ActionSection.vue";
import SectionBorder from "../../Components/Surface/SectionBorder.vue";
import PageHeader from "../../Components/DataDisplay/PageHeader.vue";
import ArrowLeft from "../../Icons/ArrowLeft.vue";

defineOptions({layout: MinimalLayout})

const props = defineProps(['settings', 'locales', 'timezone_list', 'user_has_two_factor_auth_enabled'])

const form = useForm(props.settings);

const {notify} = useNotifications();

const tab = ref('account');
</script>
<template>
    <Head :title="$t('profile.edit_profile')"/>

    <PageHeader :title="$t('profile.edit_profile')" class="!px-0">
        <Link :href="route('mixpost.home')" class="flex items-center link">
            <SecondaryButton>
                <ArrowLeft class="mr-xs"/>
                <span class="hidden sm:block">{{ $t('profile.back_dashboard') }}</span>
            </SecondaryButton>
        </Link>
    </PageHeader>

    <div class="row-py mb-2xl w-full mx-auto !pt-0">
        <div class="w-full mb-lg">
            <div class="mt-2xl sm:mt-0">
                <ActionSection>
                    <template #title>{{ $t('profile.profile_information') }}</template>
                    <template #description>{{ $t('profile.update_your_account') }}</template>

                    <UserAccount/>
                </ActionSection>
            </div>

            <SectionBorder/>

            <div class="mt-2xl sm:mt-0">
                <ActionSection>
                    <template #title>{{ $t('profile.preferences') }}</template>
                    <template #description>{{ $t('profile.update_preferences') }}</template>

                    <Preferences :form="form"
                                 :timezone_list="timezone_list"
                                 :locales="locales"
                    />
                </ActionSection>
            </div>

            <template v-if="$page.props.is_two_factor_auth_enabled">
                <SectionBorder/>

                <div class="mt-2xl sm:mt-0">
                    <ActionSection>
                        <template #title>{{ $t('auth.two_factor_authentication') }}</template>
                        <template #description>{{ $t('auth.security_using_two_factor') }}
                        </template>

                        <TwoFactorAuth/>
                    </ActionSection>
                </div>
            </template>

            <SectionBorder/>

            <div class="mt-2xl sm:mt-0">
                <ActionSection>
                    <template #title>{{ $t('auth.update_password') }}</template>
                    <template #description>{{ $t('auth.confirm_secure_password') }}
                    </template>

                    <Password/>
                </ActionSection>
            </div>
        </div>
    </div>
</template>
