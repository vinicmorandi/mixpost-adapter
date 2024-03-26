<script setup>
import TableRow from "@/Components/DataDisplay/TableRow.vue";
import TableCell from "@/Components/DataDisplay/TableCell.vue";
import UserItemAction from "@/Components/User/UserItemAction.vue";
import Avatar from "@/Components/DataDisplay/Avatar.vue";
import Badge from "../DataDisplay/Badge.vue";

const props = defineProps({
    item: {
        type: Object,
        required: true
    }
})
</script>
<template>
    <TableRow :hoverable="true">
        <TableCell class="w-10">
            <slot name="checkbox"/>
        </TableCell>

        <TableCell>
            <Avatar :name="item.name"/>
        </TableCell>

        <TableCell>
            <div>{{ item.name }}</div>
            <Badge v-if="item.is_admin" class="mt-xs">{{ $t("user.system_admin") }}</Badge>
        </TableCell>

        <TableCell>
            {{ item.email }}
        </TableCell>

        <TableCell>
            {{ item.created_at }}
        </TableCell>

        <TableCell>
            <UserItemAction :itemId="item.id"/>
        </TableCell>
    </TableRow>
</template>
