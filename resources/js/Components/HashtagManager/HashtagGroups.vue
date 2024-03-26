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

const workspaceCtx = inject('workspaceCtx');

const props = defineProps({
    groups: {
        type: Array,
        default: []
    }
})

const emit = defineEmits(['update', 'edit', 'insert'])

const isLoading = ref(false);

const fetch = () => {
    NProgress.start();

    if (!props.groups.length) {
        isLoading.value = true;
    }

    axios.get(route('mixpost.hashtaggroups.index', {workspace: workspaceCtx.id})).then((response) => {
        emit('update', response.data.data);
    }).finally(() => {
        NProgress.done();
        isLoading.value = false;
    });
}

const deleteHashtag = (group) => {
    axios.delete(route('mixpost.hashtaggroups.delete', {
        workspace: workspaceCtx.id,
        hashtaggroup: group.id
    }));

    emit('update', props.groups.filter((item) => item.id !== group.id));
}

onMounted(() => {
    fetch();
})
</script>
<template>
    <div class="relative mt-sm">
        <Preloader v-if="!groups.length && isLoading"/>

        <NoResult v-if="!groups.length && !isLoading">
            {{ $t('hashtag.dont_have_groups') }}
        </NoResult>

        <template v-else>
            <ListGroup>
                <template v-for="group in groups" :key="group.id">
                    <ListItem class="group">
                        <div class="flex items-center justify-between">
                            <div class="w-full">
                                <div>{{ group.name }}</div>
                                <div class="text-gray-500">{{ group.content }}</div>
                            </div>
                            <div class="flex justify-end">
                                <PrimaryButton
                                        @click="$emit('insert', group.content)"
                                        size="sm"
                                        class="group-visible mr-xs">
                                    {{ $t('general.insert') }}
                                </PrimaryButton>
                                <Dropdown width-classes="w-32" placement="bottom-end">
                                    <template #trigger>
                                        <PureButton class="mt-1">
                                            <EllipsisVertical/>
                                        </PureButton>
                                    </template>

                                    <template #content>
                                        <DropdownItem @click="$emit('edit', group)" as="button">
                                            <PencilSquare class="!w-5 !h-5 mr-1"/>
                                            {{ $t('general.edit') }}
                                        </DropdownItem>

                                        <DropdownItem @click="deleteHashtag(group)" as="button">
                                            <Trash class="!w-5 !h-5 mr-1 text-red-500"/>
                                            {{ $t('general.delete') }}
                                        </DropdownItem>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                    </ListItem>
                </template>
            </ListGroup>
        </template>
    </div>
</template>
