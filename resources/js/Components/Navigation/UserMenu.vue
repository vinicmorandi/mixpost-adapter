<script setup>
import {ref} from "vue";
import {router, usePage} from "@inertiajs/vue3";
import useAuth from "@/Composables/useAuth";
import useNotifications from "../../Composables/useNotifications";
import useEnterpriseConsole from "../../Composables/useEnterpriseConsole";
import useWorkspace from "../../Composables/useWorkspace";
import Dropdown from "@/Components/Dropdown/Dropdown.vue";
import DropdownItem from "@/Components/Dropdown/DropdownItem.vue";
import Avatar from "@/Components/DataDisplay/Avatar.vue";
import PencilSquareIcon from "@/Icons/PencilSquare.vue";
import CogIcon from "@/Icons/Cog.vue";
import ArrowRightOnRectangleIcon from "@/Icons/ArrowRightOnRectangle.vue";
import MenuDelimiter from "@/Components/Sidebar/MenuDelimiter.vue";
import Briefcase from "../../Icons/Briefcase.vue";
import Plus from "../../Icons/Plus.vue";
import CreditCard from "../../Icons/CreditCard.vue";
import Badge from "../DataDisplay/Badge.vue";

defineProps({
    responsive: {
        type: Boolean,
        default: false
    }
})

const {user, impersonating, workspaces} = useAuth();
const {activeWorkspaceId, activeWorkspace, isWorkspaceAdminRole} = useWorkspace();
const {notify} = useNotifications();

const open = ref(false);

const isWorkspaceActive = (id) => {
    return id === activeWorkspaceId.value;
}

const openWorkspace = (workspace) => {
    if (isWorkspaceActive(workspace.uuid)) {
        return;
    }

    if (usePage().component === 'Main/ErrorPage') {
        router.post(route('mixpost.switchWorkspace', {workspace: workspace.uuid, redirect: true}));
        return;
    }

    router.post(route('mixpost.switchWorkspace', {workspace: workspace.uuid}), {}, {
        onSuccess() {
            window.location.replace(route('mixpost.dashboard', {workspace: workspace.uuid}));
        },
        onError() {
            notify('error', 'Error!');
        }
    });
}

const {enterpriseConsole} = useEnterpriseConsole()
</script>
<template>
    <Dropdown width-classes="w-64" placement="top-start">
        <template #trigger>
            <div role="button" class="w-full flex">
                <Avatar :name="user.name" class="mr-sm"/>

                <div :class="{'hidden sm:flex' : responsive}" class="flex flex-col w-[calc(100%-3rem)]">
                    <div class="truncate">{{ user.name }}</div>
                    <div class="text-gray-500 text-sm truncate">
                        {{ activeWorkspace ? activeWorkspace.name : 'Console' }}
                    </div>
                </div>
            </div>

            <template v-if="impersonating">
                <div class="absolute mt-xs">
                    <Badge variant="warning">{{ $t('util.impersonating') }}</Badge>
                </div>
            </template>
        </template>

        <template #content>
            <div v-if="workspaces.length || enterpriseConsole.multiple_workspace_enabled"
                 class="flex flex-wrap items-center gap-xs p-sm max-h-64 overflow-y-auto">
                <template v-for="workspace in workspaces" :key="workspace.uuid">
                    <div @click="openWorkspace(workspace)"
                         role="button"
                         class="cursor-pointer group"
                    >
                        <div
                            class="rounded-lg p-0.5 border-2 border-gray-200 group-hover:bg-gray-200 transition-colors ease-in-out duration-200"
                            :class="{'border-primary-500': isWorkspaceActive(workspace.uuid)}"
                        >
                            <Avatar :backgroundColor="workspace.hex_color"
                                    :name="workspace.name"
                                    v-tooltip="workspace.name"
                                    size="md"
                                    roundedClass="rounded-lg"
                            />
                        </div>
                    </div>
                </template>

                <template
                    v-if="enterpriseConsole.multiple_workspace_enabled || (enterpriseConsole.url && !workspaces.some((workspace) => workspace.owner_id === user.id))">
                    <a :href="enterpriseConsole.create_workspace_url"
                       class="group"
                    >
                        <div
                            class="rounded-lg p-0.5 border-2 border-gray-200 group-hover:bg-gray-200 transition-colors ease-in-out duration-200"
                        >
                            <div class="w-10 h-10 p-xs">
                                <Plus/>
                            </div>
                        </div>
                    </a>
                </template>
            </div>

            <template
                v-if="!$page.props.is_admin_console && isWorkspaceAdminRole && enterpriseConsole.has_workspace_urls">
                <MenuDelimiter/>

                <DropdownItem v-if="enterpriseConsole.workspace_settings_url"
                              :href="enterpriseConsole.workspace_settings_url" as="a">
                    <template #icon>
                        <CogIcon/>
                    </template>
                    {{ $t('general.settings') }}
                </DropdownItem>

                <DropdownItem v-if="enterpriseConsole.workspace_billing_url"
                              :href="enterpriseConsole.workspace_billing_url" as="a">
                    <template #icon>
                        <CreditCard/>
                    </template>
                    {{ $t('finance.billing') }}
                </DropdownItem>
            </template>

            <MenuDelimiter/>

            <DropdownItem v-if="user.is_admin" :href="route('mixpost.dashboardAdmin')">
                <template #icon>
                    <CogIcon/>
                </template>
                {{ $t('dashboard.admin_console')}}
            </DropdownItem>

            <DropdownItem v-if="user.is_admin && enterpriseConsole.url" :href="enterpriseConsole.url" as="a">
                <template #icon>
                    <Briefcase/>
                </template>
                {{ $t('dashboard.enterprise_console') }}
            </DropdownItem>

            <DropdownItem :href="route('mixpost.profile.index')">
                <template #icon>
                    <PencilSquareIcon/>
                </template>
                {{ $t('profile.edit_profile') }}
            </DropdownItem>

            <DropdownItem :href="!impersonating ? route('mixpost.logout') : enterpriseConsole.stop_impersonating_url"
                          linkAs="button"
                          :linkMethod="!impersonating ? 'post' : 'delete'">
                <template #icon>
                    <ArrowRightOnRectangleIcon/>
                </template>
                {{ $t('auth.sign_out') }}
            </DropdownItem>
        </template>
    </Dropdown>
</template>
