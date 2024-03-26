<script setup>
import {inject, onMounted, ref} from "vue";
import { useI18n } from "vue-i18n";
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
import Badge from "../DataDisplay/Badge.vue";

const { t: $t } = useI18n()

const workspaceCtx = inject('workspaceCtx');

const props = defineProps({
    variables: {
        type: Array,
        default: []
    }
})

const emit = defineEmits(['update', 'edit', 'insert'])

const isLoading = ref(false);

const fetch = () => {
    NProgress.start();

    if (!props.variables.length) {
        isLoading.value = true;
    }

    axios.get(route('mixpost.variables.index', {workspace: workspaceCtx.id})).then((response) => {
        emit('update', [...[
            {
                id: 'platform',
                name: 'platform',
                value: $t("variable.platform_post"),
                default: true,
            },
            {
                id: 'account',
                name: 'account',
                value: $t("variable.name_account"),
                default: true,
            }
        ], ...response.data.data]);
    }).finally(() => {
        NProgress.done();
        isLoading.value = false;
    });
}

const deleteVariable = (variable) => {
    axios.delete(route('mixpost.variables.delete', {
        workspace: workspaceCtx.id,
        variable: variable.id
    }));

    emit('update', props.variables.filter((item) => item.id !== variable.id));
}

onMounted(() => {
    fetch();
})
</script>
<template>
    <div class="relative mt-sm">
        <Preloader v-if="!variables.length && isLoading"/>

        <NoResult v-if="!variables.length && !isLoading">
            {{ $t('variable.dont_have_variables') }}
        </NoResult>

        <template v-else>
            <ListGroup>
                <template v-for="variable in variables" :key="variable.id">
                    <ListItem class="group">
                        <div class="flex items-center justify-between">
                            <div class="w-full">
                                <div class="flex">
                                    <div class="text-primary-500 font-medium mr-xs">
                                        &#123;&#123;{{ variable.name }}&#125;&#125;
                                    </div>
                                    <Badge v-if="variable.default">{{ $t('general.default') }}</Badge>
                                </div>
                                <div class="text-gray-500">{{ variable.value }}</div>
                            </div>

                            <div class="flex justify-end">
                                <PrimaryButton
                                    @click="$emit('insert', `{{${variable.name}}}`)"
                                    size="sm"
                                    class="group-visible mr-xs">
                                    {{ $t('general.use') }}
                                </PrimaryButton>
                                <template v-if="!variable.default">
                                    <Dropdown width-classes="w-32" placement="bottom-end">
                                        <template #trigger>
                                            <PureButton class="mt-1">
                                                <EllipsisVertical/>
                                            </PureButton>
                                        </template>

                                        <template #content>
                                            <DropdownItem @click="$emit('edit', variable)" as="button">
                                                <PencilSquare class="!w-5 !h-5 mr-1"/>
                                                {{ $t('general.edit') }}
                                            </DropdownItem>

                                            <DropdownItem @click="deleteVariable(variable)" as="button">
                                                <Trash class="!w-5 !h-5 mr-1 text-red-500"/>
                                                {{ $t('general.delete') }}
                                            </DropdownItem>
                                        </template>
                                    </Dropdown>
                                </template>
                            </div>
                        </div>
                    </ListItem>
                </template>
            </ListGroup>
        </template>
    </div>
</template>
