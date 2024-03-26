<script setup>
import useBootstrap from "../Composables/useBootstrap";
import {Link} from '@inertiajs/vue3';
import useAuth from "../Composables/useAuth";
import Logo from "@/Components/DataDisplay/Logo.vue"
import Notifications from "@/Components/Util/Notifications.vue";
import UserMenu from "../Components/Navigation/UserMenu.vue";
import Preloader from "../Components/Util/Preloader.vue";

const {bootstrapComplete} = useBootstrap();

const {user} = useAuth();
</script>
<template>
    <div class="flex flex-col h-screen min-h-full row-py row-px overflow-y-auto bg-stone-500" scroll-region>
        <template v-if="!bootstrapComplete">
            <Preloader/>
        </template>

        <template v-else>
            <div class="w-full max-w-5xl mx-auto">
                <div class="flex justify-between relative mb-12">
                    <Link :href="route('mixpost.home')" class="flex items-center">
                        <Logo/>
                    </Link>

                    <template v-if="user">
                        <UserMenu :responsive="true"/>
                    </template>
                </div>

                <slot/>
            </div>
        </template>

        <Notifications/>
    </div>
</template>
