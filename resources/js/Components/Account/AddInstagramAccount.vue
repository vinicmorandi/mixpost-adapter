<script setup>
import {inject} from "vue";
import {Link} from '@inertiajs/vue3'
import InstagramIcon from "@/Icons/Instagram.vue";

const workspaceCtx = inject('workspaceCtx');
</script>
<template>
    <Link :href="route('mixpost.accounts.add', {workspace: workspaceCtx.id, provider: 'instagram'})"
          method="post"
          as="button"
          type="button"
          class="w-full flex items-center px-lg py-4 hover:bg-twitter hover:bg-opacity-20 ease-in-out duration-200">
        <span class="flex mr-4">
            <InstagramIcon class="text-instagram"/>
        </span>
        <span class="flex flex-col items-start">
            <span class="font-semibold">Instagram</span>
            <span>{{ $t("service.instagram.connect_account") }}</span>
        </span>
    </Link>
</template>
