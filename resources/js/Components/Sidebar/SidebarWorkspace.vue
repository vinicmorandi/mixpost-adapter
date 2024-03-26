<script setup>
import {inject} from "vue";
import {Link} from '@inertiajs/vue3';
import Logo from "@/Components/DataDisplay/Logo.vue"
import MenuItem from "@/Components/Sidebar/MenuItem.vue"
import MenuDelimiter from "@/Components/Sidebar/MenuDelimiter.vue"
import MenuGroupHeader from "@/Components/Sidebar/MenuGroupHeader.vue"
import MenuGroupBody from "@/Components/Sidebar/MenuGroupBody.vue"
import DarkButtonLink from "@/Components/Button/DarkButtonLink.vue"
import PlusIcon from "@/Icons/Plus.vue"
import GridIcon from "@/Icons/Grid.vue"
import CalendarIcon from "@/Icons/Calendar.vue"
import PhotoIcon from "@/Icons/Photo.vue"
import PackageIcon from "@/Icons/Package.vue"
import UserMenu from "@/Components/Navigation/UserMenu.vue";
import DashboardIcon from "@/Icons/Dashboard.vue";
import RectangleGroup from "../../Icons/RectangleGroup.vue";
import Forward from "../../Icons/Forward.vue";
import useWorkspace from "../../Composables/useWorkspace";

const workspaceCtx = inject('workspaceCtx');

const {isWorkspaceAdminRole} = useWorkspace();
</script>
<template>
    <div class="w-full h-full flex flex-col py-2xl bg-white border-r border-gray-200">
        <div class="relative mb-12 px-xl">
            <Link :href="route('mixpost.dashboard', {workspace: workspaceCtx.id})">
                <Logo/>
            </Link>
        </div>

        <div class="flex px-xl">
            <DarkButtonLink :href="route('mixpost.posts.create', {workspace: workspaceCtx.id})" class="w-full">
                <PlusIcon class="mr-xs"/>
                {{$t("media.create_post")}}
            </DarkButtonLink>
        </div>

        <div class="flex flex-col space-y-lg overflow-y-auto px-xl mt-2xl h-full">
            <MenuGroupBody>
                <MenuItem :url="route('mixpost.dashboard', {workspace: workspaceCtx.id})"
                          :active="$page.component === 'Workspace/Dashboard'">
                    <template #icon>
                        <DashboardIcon/>
                    </template>
                    {{ $t("dashboard.dashboard") }}
                </MenuItem>
            </MenuGroupBody>
            <MenuDelimiter/>
            <MenuGroupHeader :create-url="route('mixpost.posts.create', {workspace: workspaceCtx.id})" class="mt-lg">
                {{$t('post.content')}}
                <template #icon>
                    <PlusIcon/>
                </template>
            </MenuGroupHeader>
            <MenuGroupBody>
                <MenuItem :url="route('mixpost.posts.index', {workspace: workspaceCtx.id})"
                          :active="$page.component === 'Workspace/Posts/Index'">
                    <template #icon>
                        <GridIcon/>
                    </template>
                    {{$t('post.posts')}}
                </MenuItem>
                <MenuItem :url="route('mixpost.calendar', {workspace: workspaceCtx.id})"
                          :active="$page.component === 'Workspace/Calendar'">
                    <template #icon>
                        <CalendarIcon/>
                    </template>
                    {{$t('calendar.calendar')}}
                </MenuItem>
                <MenuItem :url="route('mixpost.media.index', {workspace: workspaceCtx.id})"
                          :active="$page.component === 'Workspace/Media'">
                    <template #icon>
                        <PhotoIcon/>
                    </template>
                    {{$t('media.media_library')}}
                </MenuItem>
                <MenuItem :url="route('mixpost.templates.index', {workspace: workspaceCtx.id})"
                          :active="$page.component === 'Workspace/Templates/Index'">
                    <template #icon>
                        <RectangleGroup/>
                    </template>
                    {{$t('template.templates')}}
                </MenuItem>
            </MenuGroupBody>

            <template v-if="isWorkspaceAdminRole">
                <MenuDelimiter/>
                <MenuGroupHeader :create-url="route('mixpost.posts.create', {workspace: workspaceCtx.id})">
                    {{$t('post.configuration')}}
                </MenuGroupHeader>
                <MenuGroupBody>
                    <MenuItem :url="route('mixpost.postingSchedule.index', {workspace: workspaceCtx.id})"
                              :active="$page.component === 'Workspace/PostingSchedule'">
                        <template #icon>
                            <Forward/>
                        </template>
                        {{ $t('posting_schedule.posting_schedule') }}
                    </MenuItem>
                    <MenuItem :url="route('mixpost.accounts.index', {workspace: workspaceCtx.id})"
                              :active="$page.component === 'Workspace/Accounts/Accounts'">
                        <template #icon>
                            <PackageIcon/>
                        </template>
                        {{ $t('post.accounts') }}
                    </MenuItem>
                </MenuGroupBody>
            </template>
        </div>

        <div class="px-xl pt-xl">
            <UserMenu/>
        </div>
    </div>
</template>
