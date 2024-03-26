<script setup>
import {sortBy} from "lodash";
import {computed} from "vue";
import TableRow from "@/Components/DataDisplay/TableRow.vue";
import TableCell from "@/Components/DataDisplay/TableCell.vue";
import Avatar from "@/Components/DataDisplay/Avatar.vue";
import WorkspaceItemAction from "./WorkspaceItemAction.vue";

const props = defineProps({
    item: {
        type: Object,
        required: true
    }
})

const topUsers = computed(() => {
    return sortBy(props.item.users.slice(0, 3), 'pivot.joined_at')
})

const remainingUserCount = computed(() => {
    return props.item.users.slice(3).length;
})
</script>
<template>
    <TableRow :hoverable="true">
        <TableCell class="w-10">
            <slot name="checkbox"/>
        </TableCell>

        <TableCell>
            <Avatar :backgroundColor="item.hex_color"
                    :name="item.name"
                    roundedClass="rounded-lg"
            />
        </TableCell>

        <TableCell>
            <div>{{ item.name }}</div>
        </TableCell>

        <TableCell>
            {{ item.created_at }}
        </TableCell>

        <TableCell>
            <div v-if="item.users.length" class="flex justify-start">
                <template v-for="(user, index) in topUsers" :key="user.id">
                    <Avatar :name="user.name" v-tooltip="user.name"
                            :class="{'-ml-6': index > 0}"
                            class="cursor-default mr-xs last:mr-xs"
                    />
                </template>

                <div v-if="remainingUserCount"
                     class="mt-xs"
                >
                    +{{ remainingUserCount }}
                </div>
            </div>
        </TableCell>

        <TableCell>
            <WorkspaceItemAction :itemId="item.uuid"/>
        </TableCell>
    </TableRow>
</template>
