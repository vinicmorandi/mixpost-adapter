<script setup>
import {inject} from "vue";
import {Link} from '@inertiajs/vue3'
import FacebookIcon from "@/Icons/Facebook.vue";

const workspaceCtx = inject('workspaceCtx');
</script>
<template>
    <Link :href="route('mixpost.accounts.add', {workspace: workspaceCtx.id, provider: 'facebook_page'})"
          method="post"
          as="button"
          type="button"
          class="w-full flex items-center px-lg py-4 hover:bg-facebook hover:bg-opacity-20 ease-in-out duration-200">
        <span class="flex mr-4">
            <FacebookIcon class="text-facebook"/>
        </span>
        <span class="flex flex-col items-start">
            <span class="font-semibold">Facebook Page</span>
            <span>{{ $t("service.facebook.connect_page") }}</span>
        </span>
    </Link>
</template>
