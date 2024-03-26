<script setup>
import Draggable from 'vuedraggable'
import {cloneDeep} from "lodash";
import {nanoid} from "nanoid";
import ListGroup from "../DataDisplay/ListGroup.vue";
import ListItem from "../DataDisplay/ListItem.vue";
import Trash from "../../Icons/Trash.vue";
import PureButton from "../Button/PureButton.vue";
import GripVertical from "../../Icons/GripVertical.vue";
import NoResult from "../Util/NoResult.vue";
import Badge from "../DataDisplay/Badge.vue";
import EditBlock from "./EditBlock.vue";

const props = defineProps({
    modelValue: {
        required: true,
        type: Array,
    }
});

const emit = defineEmits(['update:modelValue']);

const update = (blockRandomKey, blockId, data) => {
    // The page can have attached the same block multiple times
    // We have to update the state for all of them
    const blocks = props.modelValue.map((item) => {
        if (item.id === blockId) {
            return Object.assign(
                item,
                Object.assign(data,
                    {random_key: item.random_key === blockRandomKey ? blockRandomKey : nanoid()}
                )
            );
        }

        return item;
    });

    emit('update:modelValue', blocks);
}

const removeBlockItems = (blockId) => {
    // The page can have attached the same block multiple times
    // We have to delete all of them
    const blocks = cloneDeep(props.modelValue).filter((item) => {
        return item.id !== blockId;
    });

    emit('update:modelValue', blocks);
}

const remove = (index) => {
    emit('update:modelValue', props.modelValue.filter((_, i) => i !== index));
}
</script>
<template>
    <template v-if="modelValue.length">
        <ListGroup>
            <Draggable
                :list="modelValue"
                v-bind="{
                animation: 200,
                group: 'blocks',
            }"
                handle=".handle"
                item-key="random_key"
            >
                <template #item="{element, index}">
                    <ListItem class="group">
                        <div class="flex items-center justify-between">
                            <div class="w-full">
                                <div class="flex">
                                    <PureButton class="handle mr-xs cursor-move">
                                        <template #icon>
                                            <GripVertical/>
                                        </template>
                                    </PureButton>

                                    <div>
                                        <div class="flex">
                                            <div class="font-medium">
                                                {{ element.name }}
                                            </div>
                                        </div>
                                        <div class="font-medium text-gray-400">{{ element.module }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <Badge v-if="!element.status"
                                       variant="error"
                                       class="mr-xs">
                                    {{ $t('general.disabled') }}
                                </Badge>

                                <div class="flex mr-xs">
                                    <EditBlock :block="element"
                                               @delete="removeBlockItems(element.id)"
                                               @update="update(element.random_key, element.id, $event)"
                                    />
                                </div>

                                <PureButton
                                    @click="remove(index)"
                                    :destructive="true"
                                    v-tooltip="$t('page.remove_from_page')"
                                    class="group-visible">
                                    <template #icon>
                                        <Trash/>
                                    </template>
                                </PureButton>
                            </div>
                        </div>
                    </ListItem>
                </template>
            </Draggable>
        </ListGroup>
    </template>
    <template v-else>
        <NoResult>
            {{ $t('page.page_no_blocks_added') }}
        </NoResult>
    </template>
</template>
