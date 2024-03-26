<script setup>
import {Link} from "@inertiajs/vue3";
import useEnterpriseConsole from "../../Composables/useEnterpriseConsole";
import Logo from "@/Components/DataDisplay/Logo.vue"
import MenuItem from "@/Components/Sidebar/MenuItem.vue"
import MenuDelimiter from "@/Components/Sidebar/MenuDelimiter.vue"
import MenuGroupHeader from "@/Components/Sidebar/MenuGroupHeader.vue"
import MenuGroupBody from "@/Components/Sidebar/MenuGroupBody.vue"
import UserMenu from "@/Components/Navigation/UserMenu.vue";
import DashboardIcon from "@/Icons/Dashboard.vue";
import GridIcon from "@/Icons/Grid.vue"
import UsersIcon from "@/Icons/Users.vue"
import ServerStackIcon from "@/Icons/ServerStack.vue"
import InformationCircle from "../../Icons/InformationCircle.vue";
import Document from "../../Icons/Document.vue";
import BookOpen from "../../Icons/BookOpen.vue";
import PaintBrush from "../../Icons/PaintBrush.vue";
import Briefcase from "../../Icons/Briefcase.vue";

const {enterpriseConsole} = useEnterpriseConsole()
</script>
<template>
    <div class="w-full h-full flex flex-col pb-2xl pt-20 bg-white border-r border-gray-200">
        <div class="absolute top-0 w-full text-center">
            <div class="bg-primary-50 border-b border-r border-gray-200 p-xs">
                <span class="text-black font-medium text-sm">{{ $t('dashboard.admin_console') }}</span>
            </div>
        </div>
        <div class="relative mb-12 px-xl">
            <Link :href="route('mixpost.dashboardAdmin')">
                <Logo/>
            </Link>
        </div>

        <div class="flex flex-col space-y-lg overflow-y-auto px-xl h-full">
            <MenuGroupBody>
                <MenuItem :url="route('mixpost.dashboardAdmin')"
                          :active="$page.component === 'Admin/Dashboard'">
                    <template #icon>
                        <DashboardIcon/>
                    </template>
                    {{ $t("dashboard.dashboard") }}
                </MenuItem>

                <template v-if="enterpriseConsole.url">
                    <MenuItem :url="enterpriseConsole.url" :active="$page.component === 'Admin/Users/Users'">
                        <template #icon>
                            <Briefcase/>
                        </template>
                        {{ $t('dashboard.enterprise') }}
                    </MenuItem>
                </template>

                <template v-if="!enterpriseConsole.url">
                    <MenuItem :url="route('mixpost.users.index')" :active="$page.component === 'Admin/Users/Users'">
                        <template #icon>
                            <UsersIcon/>
                        </template>
                        {{ $t('user.users') }}
                    </MenuItem>
                    <MenuItem :url="route('mixpost.workspaces.index')"
                              :active="$page.component === 'Admin/Workspaces/Workspaces'">
                        <template #icon>
                            <GridIcon/>
                        </template>
                        {{ $t('workspace.workspaces') }}
                    </MenuItem>
                </template>
            </MenuGroupBody>
            <MenuDelimiter/>
            <MenuGroupHeader>
                {{ $t('post.configuration') }}
            </MenuGroupHeader>
            <MenuGroupBody>
                <MenuItem :url="route('mixpost.services.index')"
                          :active="$page.component === 'Admin/Configuration/Services'">
                    <template #icon>
                        <ServerStackIcon/>
                    </template>
                    {{ $t('service.services') }}
                </MenuItem>
                <MenuItem :url="route('mixpost.pages.index')" :active="$page.component === 'Admin/Pages/Pages'">
                    <template #icon>
                        <BookOpen/>
                    </template>
                    {{ $t('page.pages') }}
                </MenuItem>
                <MenuItem :url="route('mixpost.configs.theme.form')"
                          :active="$page.component === 'Admin/Configs/ThemeConfig'">
                    <template #icon>
                        <PaintBrush/>
                    </template>
                    {{ $t('theme.customization') }}
                </MenuItem>
            </MenuGroupBody>
            <MenuDelimiter/>
            <MenuGroupHeader>
                {{ $t('system.system') }}
            </MenuGroupHeader>
            <MenuGroupBody>
                <MenuItem :url="route('mixpost.system.status')" :active="$page.component === 'Admin/System/Status'">
                    <template #icon>
                        <InformationCircle/>
                    </template>
                    {{ $t('system.status') }}
                </MenuItem>
                <MenuItem :url="route('mixpost.system.logs.index')" :active="$page.component === 'Admin/System/Logs'">
                    <template #icon>
                        <Document/>
                    </template>
                    {{ $t('system.logs') }}
                </MenuItem>
            </MenuGroupBody>
        </div>

        <div class="px-xl pt-xl">
            <UserMenu/>
        </div>
    </div>
</template>
