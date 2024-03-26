<script setup>
import {inject} from "vue";
import {Link} from '@inertiajs/vue3'
import PinterestIcon from "@/Icons/Pinterest.vue";

const workspaceCtx = inject('workspaceCtx');
</script>
<template>
    <Link :href="route('mixpost.accounts.add', {workspace: workspaceCtx.id, provider: 'pinterest'})"
          method="post"
          as="button"
          type="button"
          class="w-full flex items-center px-lg py-4 hover:bg-twitter hover:bg-opacity-20 ease-in-out duration-200">
        <span class="flex mr-4">
            <PinterestIcon class="text-pinterest"/>
        </span>
        <span class="flex flex-col items-start">
            <span class="font-semibold">Pinterest</span>
            <span>{{ $t("service.pinterest.connect_profile") }}</span>
        </span>
    </Link>
</template>
