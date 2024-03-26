<script setup>
import {inject, onMounted, onUnmounted, ref, watch} from "vue";
import { useI18n } from "vue-i18n";
import {Head} from '@inertiajs/vue3';
import {router} from "@inertiajs/vue3";
import emitter from "@/Services/emitter";
import useNotifications from "@/Composables/useNotifications";
import {cloneDeep, pickBy, throttle} from "lodash";
import useSelectable from "@/Composables/useSelectable";
import PageHeader from '@/Components/DataDisplay/PageHeader.vue';
import PostsFilter from '@/Components/Post/PostsFilter.vue';
import Tabs from "@/Components/Navigation/Tabs.vue"
import Tab from "@/Components/Navigation/Tab.vue"
import Panel from "@/Components/Surface/Panel.vue";
import Checkbox from "@/Components/Form/Checkbox.vue";
import Table from "@/Components/DataDisplay/Table.vue";
import TableRow from "@/Components/DataDisplay/TableRow.vue";
import TableCell from "@/Components/DataDisplay/TableCell.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import PureDangerButton from "@/Components/Button/PureDangerButton.vue";
import DangerButton from "@/Components/Button/DangerButton.vue"
import PostItem from "@/Components/Post/PostItem.vue";
import SelectableBar from "@/Components/DataDisplay/SelectableBar.vue";
import ConfirmationModal from "@/Components/Modal/ConfirmationModal.vue";
import Pagination from "@/Components/Navigation/Pagination.vue";
import NoResult from "@/Components/Util/NoResult.vue";
import TrashIcon from "@/Icons/Trash.vue";

const { t: $t } = useI18n()

const props = defineProps({
    filter: {
        type: Object,
        default: {}
    },
    posts: {
        type: Object,
    },
    has_failed_posts: {
        type: Boolean,
        default: false
    }
});

const filter = ref({
    keyword: props.filter.keyword,
    status: props.filter.status,
    tags: props.filter.tags,
    accounts: props.filter.accounts
})

const {
    selectedRecords,
    putPageRecords,
    toggleSelectRecordsOnPage,
    deselectRecord,
    deselectAllRecords
} = useSelectable();

const workspaceCtx = inject('workspaceCtx');

const itemsId = () => {
    return props.posts.data.map(item => item.id);
}

onMounted(() => {
    putPageRecords(itemsId());

    emitter.on('postDelete', id => {
        deselectRecord(id);
    });
});

onUnmounted(() => {
    emitter.off('postDelete');
})

watch(() => cloneDeep(filter.value), throttle(() => {
    router.get(route('mixpost.posts.index', {workspace: workspaceCtx.id}), pickBy(filter.value), {
        preserveState: true,
        only: ['posts', 'filter']
    });
}, 300))

watch(() => props.posts.data, () => {
    putPageRecords(itemsId());
})

const {notify} = useNotifications();
const confirmationDeletion = ref(false);

const deletePosts = () => {
    router.delete(route('mixpost.posts.multipleDelete', {workspace: workspaceCtx.id}), {
        data: {
            posts: selectedRecords.value,
            status: filter.value.status
        },
        onSuccess() {
            deselectAllRecords();
            notify('success', props.filter.status === 'trash' ? $t("post.posts_deleted") : $t("post.posts_to_trash"))
        },
        onFinish() {
            confirmationDeletion.value = false;
        }
    });
}
</script>
<template>
    <Head :title="$t('post.posts')"/>

    <div class="row-py mb-2xl">
        <PageHeader :title="$t('post.posts')">
            <PostsFilter v-model="filter" class="md:ml-2"/>
        </PageHeader>

        <div class="w-full row-px">
            <Tabs>
                <Tab @click="filter.status = null" :active="!$page.props.filter.status">All</Tab>
                <Tab @click="filter.status = 'draft'" :active="$page.props.filter.status === 'draft'">
                    {{ $t("post.drafts") }}
                </Tab>
                <Tab @click="filter.status = 'scheduled'" :active="$page.props.filter.status === 'scheduled'">
                    {{ $t("post.scheduled") }}
                </Tab>
                <Tab @click="filter.status = 'published'" :active="$page.props.filter.status === 'published'">
                    {{ $t("post.published") }}
                </Tab>
                <template v-if="has_failed_posts">
                    <Tab @click="filter.status = 'failed'" :active="$page.props.filter.status === 'failed'"
                         class="text-red-500">{{ $t("post.failed") }}
                    </Tab>
                </template>
                <Tab @click="filter.status = 'trash'" :active="$page.props.filter.status === 'trash'">
                    {{ $t("general.trash") }}
                </Tab>
            </Tabs>
        </div>

        <div class="w-full row-px mt-lg">
            <SelectableBar :count="selectedRecords.length" @close="deselectAllRecords">
                <PureDangerButton @click="confirmationDeletion = true" v-tooltip="$t('general.delete')">
                    <TrashIcon/>
                </PureDangerButton>
            </SelectableBar>

            <Panel :with-padding="false">
                <Table>
                    <template #head>
                        <TableRow>
                            <TableCell component="th" scope="col" class="w-10">
                                <Checkbox v-model:checked="toggleSelectRecordsOnPage" :disabled="!posts.meta.total"/>
                            </TableCell>
                            <TableCell component="th" scope="col" class="w-44">{{ $t("post.status") }}</TableCell>
                            <TableCell component="th" scope="col" class="!pl-0 text-left">
                                {{ $t("post.content") }}
                            </TableCell>
                            <TableCell component="th" scope="col" class="w-48">{{ $t("post.media") }}</TableCell>
                            <TableCell component="th" scope="col">{{ $t("post.labels") }}</TableCell>
                            <TableCell component="th" scope="col">{{ $t("post.accounts") }}</TableCell>
                            <TableCell component="th" scope="col">{{ $t("post.author") }}</TableCell>
                            <TableCell component="th" scope="col"/>
                        </TableRow>
                    </template>
                    <template #body>
                        <template v-for="item in posts.data" :key="item.id">
                            <PostItem :item="item" :filter="posts.filter"
                                      @onDelete="()=> {deselectRecord(item.id)}">
                                <template #checkbox>
                                    <Checkbox v-model:checked="selectedRecords" :value="item.id"/>
                                </template>
                            </PostItem>
                        </template>
                    </template>
                </Table>

                <NoResult v-if="!posts.meta.total" class="py-md px-md">{{ $t("post.no_posts_found") }}</NoResult>
            </Panel>

            <div v-if="posts.meta.links.length > 3" class="mt-lg">
                <Pagination :meta="posts.meta" :links="posts.links"/>
            </div>
        </div>
    </div>

    <ConfirmationModal :show="confirmationDeletion" variant="danger" @close="confirmationDeletion = false">
        <template #header>
            {{ $t("post.delete_posts") }}
        </template>
        <template #body>
            {{ $t("post.confirmation_delete_post") }}
        </template>
        <template #footer>
            <SecondaryButton @click="confirmationDeletion = false" class="mr-xs">{{
                    $t("general.cancel")
                }}
            </SecondaryButton>
            <DangerButton @click="deletePosts">{{ $t("general.delete") }}</DangerButton>
        </template>
    </ConfirmationModal>
</template>
