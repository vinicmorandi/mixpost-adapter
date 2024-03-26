<script setup>
import {inject} from "vue";
import {Head, Link, router} from "@inertiajs/vue3";
import NProgress from "nprogress";
import useTemplate from "../../../Composables/useTemplate";
import PageHeader from "../../../Components/DataDisplay/PageHeader.vue";
import Panel from "../../../Components/Surface/Panel.vue";
import PrimaryButton from "../../../Components/Button/PrimaryButton.vue";
import Trash from "../../../Icons/Trash.vue";
import PencilSquare from "../../../Icons/PencilSquare.vue";
import Dropdown from "../../../Components/Dropdown/Dropdown.vue";
import DropdownItem from "../../../Components/Dropdown/DropdownItem.vue";
import NoResult from "../../../Components/Util/NoResult.vue";
import PureButton from "../../../Components/Button/PureButton.vue";
import EllipsisVertical from "../../../Icons/EllipsisVertical.vue";
import Masonry from "../../../Components/Layout/Masonry.vue";
import TemplateItem from "../../../Components/TemplateManager/TemplateItem.vue";
import ListGroup from "../../../Components/DataDisplay/ListGroup.vue";
import ListItem from "../../../Components/DataDisplay/ListItem.vue";
import Plus from "../../../Icons/Plus.vue";

const workspaceCtx = inject('workspaceCtx');

const props = defineProps({
    templates: {
        type: Array,
        required: true,
    }
})

const {createPost, formatTemplateContent, deleteTemplate} = useTemplate();
</script>
<template>
    <Head :title="$t('template.templates')"/>

    <div class="row-py">
        <PageHeader :title="$t('template.templates')">
            <Link :href="route('mixpost.templates.create', {workspace: workspaceCtx.id})">
                <PrimaryButton size="sm">
                    <Plus class="mr-xs"/>
                    {{ $t('template.create_template') }}
                </PrimaryButton>
            </Link>
        </PageHeader>

        <div class="w-full row-px">
            <Panel>
                <div class="relative mt-sm">
                    <NoResult v-if="!templates.length">
                        {{ $t('template.do_not_have_templates') }}
                    </NoResult>

                    <template v-else>
                        <Masonry :items="templates" :columns="3">
                            <template #default="{item}">
                                <ListGroup class="group">
                                    <ListItem :withClassesForLast="false">
                                        <TemplateItem :template="item">
                                            <template #action>
                                                <div class="flex justify-end">
                                                    <PrimaryButton
                                                        @click="()=> {
                                                            createPost(formatTemplateContent(item.content));
                                                        }"
                                                        size="sm"
                                                        class="group-visible mr-xs">
                                                        {{ $t('general.use') }}
                                                    </PrimaryButton>

                                                    <Dropdown width-classes="w-32" placement="bottom-end">
                                                        <template #trigger>
                                                            <PureButton class="mt-1">
                                                                <EllipsisVertical/>
                                                            </PureButton>
                                                        </template>

                                                        <template #content>
                                                            <DropdownItem linkAs="a"
                                                                          :href="route('mixpost.templates.edit', {workspace: workspaceCtx.id, template: item.id})">
                                                                <PencilSquare class="!w-5 !h-5 mr-1"/>
                                                                {{ $t('general.edit') }}
                                                            </DropdownItem>

                                                            <DropdownItem @click="deleteTemplate(item)" as="button">
                                                                <Trash class="!w-5 !h-5 mr-1 text-red-500"/>
                                                                {{ $t('general.delete') }}
                                                            </DropdownItem>
                                                        </template>
                                                    </Dropdown>
                                                </div>
                                            </template>
                                        </TemplateItem>
                                    </ListItem>
                                </ListGroup>
                            </template>
                        </Masonry>
                    </template>
                </div>
            </Panel>
        </div>
    </div>
</template>
