<script setup>
import {inject, onMounted, ref} from "vue";
import NProgress from "nprogress";
import Preloader from "../Util/Preloader.vue";
import Dropdown from "../Dropdown/Dropdown.vue";
import PureButton from "../Button/PureButton.vue";
import EllipsisVertical from "../../Icons/EllipsisVertical.vue";
import DropdownItem from "../Dropdown/DropdownItem.vue";
import Trash from "../../Icons/Trash.vue";
import PencilSquare from "../../Icons/PencilSquare.vue";
import PrimaryButton from "../Button/PrimaryButton.vue";
import ListGroup from "../DataDisplay/ListGroup.vue";
import ListItem from "../DataDisplay/ListItem.vue";
import NoResult from "../Util/NoResult.vue";
import TemplateItem from "./TemplateItem.vue";
import Masonry from "../Layout/Masonry.vue";

const workspaceCtx = inject('workspaceCtx');

const props = defineProps({
    templates: {
        type: Array,
        default: []
    }
})

const emit = defineEmits(['update', 'edit', 'select'])

const isLoading = ref(false);

const fetch = () => {
    NProgress.start();

    if (!props.templates.length) {
        isLoading.value = true;
    }

    axios.get(route('mixpost.templates.api.index', {workspace: workspaceCtx.id})).then((response) => {
        emit('update', response.data.data);
    }).finally(() => {
        NProgress.done();
        isLoading.value = false;
    });
}

const deleteTemplate = (template) => {
    axios.delete(route('mixpost.templates.api.delete', {
        workspace: workspaceCtx.id,
        template: template.id
    }));

    emit('update', props.templates.filter((item) => item.id !== template.id));
}

onMounted(() => {
    fetch();
})
</script>
<template>
    <div class="relative mt-sm">
        <Preloader v-if="!templates.length && isLoading"/>

        <NoResult v-if="!templates.length && !isLoading">
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
                                            @click="$emit('select', item)"
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
                                                <DropdownItem @click="$emit('edit', item)" as="button">
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
</template>
