<script setup>
import {inject, onMounted, onUnmounted, watch} from "vue";
import {Head, router, Link} from '@inertiajs/vue3';
import { useI18n } from "vue-i18n";
import AdminLayout from "@/Layouts/Admin.vue";
import emitter from "@/Services/emitter";
import useNotifications from "@/Composables/useNotifications";
import useSelectable from "@/Composables/useSelectable";
import PageHeader from "@/Components/DataDisplay/PageHeader.vue";
import PureDangerButton from "@/Components/Button/PureDangerButton.vue";
import Panel from "@/Components/Surface/Panel.vue";
import Checkbox from "@/Components/Form/Checkbox.vue";
import Table from "@/Components/DataDisplay/Table.vue";
import TableRow from "@/Components/DataDisplay/TableRow.vue";
import TableCell from "@/Components/DataDisplay/TableCell.vue";
import SelectableBar from "@/Components/DataDisplay/SelectableBar.vue";
import NoResult from "@/Components/Util/NoResult.vue";
import TrashIcon from "@/Icons/Trash.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import PageItem from "../../../Components/Page/PageItem.vue";
import GeneratePageSamples from "../../../Components/Page/GeneratePageSamples.vue";
import Flex from "../../../Components/Layout/Flex.vue";

defineOptions({layout: AdminLayout});

const { t: $t } = useI18n()

const routePrefix = inject('routePrefix');

const props = defineProps({
    pages: {
        type: Object,
    }
});

const pageTitle = $t("page.pages");

const {notify} = useNotifications();
const confirmation = inject('confirmation');

const {
    selectedRecords,
    putPageRecords,
    toggleSelectRecordsOnPage,
    deselectRecord,
    deselectAllRecords
} = useSelectable();

const itemsId = () => {
    return props.pages.data.map(item => item.uuid);
}

onMounted(() => {
    putPageRecords(itemsId());

    emitter.on('pageDelete', id => {
        deselectRecord(id);
    });
});

onUnmounted(() => {
    emitter.off('pageDelete');
})

watch(() => props.pages.data, () => {
    putPageRecords(itemsId());
})

const deletePages = () => {
    confirmation()
        .title($t("page.delete_pages"))
        .description($t("page.confirm_delete_pages"))
        .destructive()
        .onConfirm((dialog) => {
            dialog.isLoading(true);

            router.delete(route(`${routePrefix}.pages.multipleDelete`), {
                data: {
                    pages: selectedRecords.value,
                },
                preserveScroll: true,
                onSuccess() {
                    deselectAllRecords();
                    notify('success', $t('page.pages_deleted'))
                },
                onFinish() {
                    dialog.reset();
                }
            });
        })
        .show();
}
</script>
<template>
    <Head :title="pageTitle"/>

    <div class="w-full mx-auto max-w-6xl row-py">
        <PageHeader :title="pageTitle">
            <template #description>{{ $t('page.page_desc') }}</template>

            <GeneratePageSamples/>
        </PageHeader>

        <div class="mt-lg row-px w-full">
            <SelectableBar :count="selectedRecords.length" @close="deselectAllRecords">
                <PureDangerButton @click="deletePages" v-tooltip="$t('general.delete')">
                    <TrashIcon/>
                </PureDangerButton>
            </SelectableBar>

            <Flex>
                <Link :href="route(`${routePrefix}.pages.create`)" class="mb-xs sm:mb-0">
                    <PrimaryButton>{{ $t('page.create_page') }}</PrimaryButton>
                </Link>
            </Flex>

            <Panel :with-padding="false" class="mt-lg">
                <Table>
                    <template #head>
                        <TableRow>
                            <TableCell component="th" scope="col" class="w-10">
                                <Checkbox v-model:checked="toggleSelectRecordsOnPage"
                                          :disabled="!pages.data.length"/>
                            </TableCell>
                            <TableCell component="th" scope="col">{{ $t('general.name') }}</TableCell>
                            <TableCell component="th" scope="col">{{ $t('page.url_path') }}</TableCell>
                            <TableCell component="th" scope="col">{{ $t('general.status') }}</TableCell>
                            <TableCell component="th" scope="col">{{ $t('general.created_at') }}</TableCell>
                            <TableCell component="th" scope="col"/>
                        </TableRow>
                    </template>
                    <template #body>
                        <template v-for="item in pages.data" :key="item.uuid">
                            <PageItem :item="item" @onDelete="()=> {deselectRecord(item.uuid)}">
                                <template #checkbox>
                                    <Checkbox v-model:checked="selectedRecords" :value="item.uuid"/>
                                </template>
                            </PageItem>
                        </template>
                    </template>
                </Table>

                <NoResult v-if="!pages.data.length" class="p-md">{{ $t("page.no_pages_found") }}</NoResult>
            </Panel>
        </div>
    </div>
</template>
