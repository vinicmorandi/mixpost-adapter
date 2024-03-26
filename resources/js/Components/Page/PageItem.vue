<script setup>
import TableRow from "@/Components/DataDisplay/TableRow.vue";
import TableCell from "@/Components/DataDisplay/TableCell.vue";
import PageItemAction from "@/Components/Page/PageItemAction.vue";
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
            {{ item.name }}
        </TableCell>

        <TableCell>
           {{ item.slug }}
        </TableCell>

        <TableCell>
            <Badge v-if="item.status" variant="success">{{ $t('general.enabled') }}</Badge>
            <Badge v-else variant="error">{{ $t('general.disabled') }}</Badge>
        </TableCell>

        <TableCell>
            {{ item.created_at }}
        </TableCell>

        <TableCell>
            <PageItemAction :itemId="item.uuid" :urlPath="item.url_path"/>
        </TableCell>
    </TableRow>
</template>
