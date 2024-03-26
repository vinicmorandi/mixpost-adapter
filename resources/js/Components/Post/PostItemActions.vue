<script setup>
import {computed, inject, ref} from "vue";
import {useI18n} from "vue-i18n";
import {usePage} from "@inertiajs/vue3";
import {router} from "@inertiajs/vue3";
import emitter from "@/Services/emitter";
import useNotifications from "@/Composables/useNotifications";
import ConfirmationModal from "@/Components/Modal/ConfirmationModal.vue";
import PureButtonLink from "@/Components/Button/PureButtonLink.vue";
import PureButton from "@/Components/Button/PureButton.vue";
import EllipsisVerticalIcon from "@/Icons/EllipsisVertical.vue"
import Dropdown from "@/Components/Dropdown/Dropdown.vue"
import DropdownItem from "@/Components/Dropdown/DropdownItem.vue"
import SecondaryButton from "@/Components/Button/SecondaryButton.vue"
import DangerButton from "@/Components/Button/DangerButton.vue"
import PencilSquareIcon from "@/Icons/PencilSquare.vue";
import DuplicateIcon from "@/Icons/Duplicate.vue";
import TrashIcon from "@/Icons/Trash.vue";
import ArrowUturnLeft from "../../Icons/ArrowUturnLeft.vue";

const {t: $t} = useI18n()

const workspaceCtx = inject('workspaceCtx');

const props = defineProps({
    itemId: {
        type: String,
        required: true,
    },
    trashed: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['onDelete'])

const confirmationDeletion = ref(false);

const filterStatus = computed(() => {
    const pageProps = usePage().props;

    return pageProps.hasOwnProperty('filter') ? pageProps.filter.status : null;
});

const {notify} = useNotifications();

const deletePost = () => {
    router.delete(route('mixpost.posts.delete', {
        workspace: workspaceCtx.id,
        post: props.itemId,
        status: filterStatus.value
    }), {
        onSuccess() {
            confirmationDeletion.value = false;
            notify('success', filterStatus.value === 'trash' ? $t("post.post_deleted_permanently") : $t("post.post_moved_to_trash"))
            emit('onDelete')
            emitter.emit('postDelete', props.itemId);
        }
    })
}

const duplicate = () => {
    router.post(route('mixpost.posts.duplicate', {workspace: workspaceCtx.id, post: props.itemId}), {}, {
        onSuccess() {
            notify('success', $t('post.post_duplicated'))
        }
    })
}

const restore = () => {
    router.post(route('mixpost.posts.restore', {workspace: workspaceCtx.id, post: props.itemId}), {}, {
        onSuccess() {
            notify('success', $t('post.post_restored'))
        }
    })
}
</script>
<template>
    <div>
        <div class="flex flex-row items-center gap-xs">
            <PureButtonLink :href="route('mixpost.posts.edit', { workspace: workspaceCtx.id, post: itemId })"
                            v-tooltip="$t('general.edit')">
                <PencilSquareIcon/>
            </PureButtonLink>

            <Dropdown width-classes="w-32" placement="bottom-end">
                <template #trigger>
                    <PureButton class="mt-1">
                        <EllipsisVerticalIcon/>
                    </PureButton>
                </template>

                <template #content>
                    <template v-if="trashed">
                        <DropdownItem @click="restore" as="button">
                            <ArrowUturnLeft class="!w-5 !h-5 mr-1"/>
                            {{ $t("general.restore") }}
                        </DropdownItem>
                    </template>

                    <DropdownItem @click="duplicate" as="button">
                        <DuplicateIcon class="!w-5 !h-5 mr-1"/>
                        {{ $t("general.duplicate") }}
                    </DropdownItem>

                    <DropdownItem @click="confirmationDeletion = true" as="button">
                        <TrashIcon class="!w-5 !h-5 mr-1 text-red-500"/>
                        {{ $t("general.delete") }}
                    </DropdownItem>
                </template>
            </Dropdown>
        </div>

        <ConfirmationModal :show="confirmationDeletion" variant="danger" @close="confirmationDeletion = false">
            <template #header>
                {{ $t("post.delete_post") }}
            </template>
            <template #body>
                {{ $t("post.confirm_delete_post") }}
            </template>
            <template #footer>
                <SecondaryButton @click="confirmationDeletion = false" class="mr-xs"> {{ $t("general.cancel") }}
                </SecondaryButton>
                <DangerButton @click="deletePost">{{
                        trashed ? $t("general.delete_permanently") : $t("general.delete")
                    }}
                </DangerButton>
            </template>
        </ConfirmationModal>
    </div>
</template>
