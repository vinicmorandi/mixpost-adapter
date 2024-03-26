<script setup>
import {inject} from "vue";
import {Link, usePage} from '@inertiajs/vue3'
import FacebookIcon from "@/Icons/Facebook.vue";
import useAuth from "../../Composables/useAuth";
import Badge from "../DataDisplay/Badge.vue";

const workspaceCtx = inject('workspaceCtx');

const metaAppVersion = usePage().props.additionally.meta_app_version;
const {user} = useAuth();
</script>
<template>
    <template v-if="!(user.is_admin === false && metaAppVersion === 'v19.0')">
        <Link :href="route('mixpost.accounts.add', {workspace: workspaceCtx.id, provider: 'facebook_group'})"
              method="post"
              as="button"
              type="button"
              class="w-full flex items-center px-lg py-4 hover:bg-facebook hover:bg-opacity-20 ease-in-out duration-200">
        <span class="flex mr-4">
            <FacebookIcon class="text-facebook"/>
        </span>
            <span class="flex flex-col items-start">
            <span class="font-semibold">Facebook Group <template v-if="metaAppVersion === 'v19.0'"><Badge variant="error">Deprecated in v19</Badge></template></span>
            <span>{{ $t("service.facebook.connect_group") }}</span>

            <template v-if="metaAppVersion === 'v19.0'">
                <span class="text-xs text-red-500 text-left">The Facebook Groups API is deprecated in v19. In the facebook documentation it is missing for the new API changes. We are looking for a quick solution and will come with an update.</span>
            </template>
        </span>
        </Link>
    </template>
</template>
