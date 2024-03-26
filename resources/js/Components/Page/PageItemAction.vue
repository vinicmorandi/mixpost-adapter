<script setup>
import {inject, ref} from "vue";
import { useI18n } from "vue-i18n";
import emitter from "@/Services/emitter";
import {router} from "@inertiajs/vue3";
import useNotifications from "@/Composables/useNotifications";
import PencilSquare from "../../Icons/PencilSquare.vue";
import Dropdown from "../Dropdown/Dropdown.vue";
import Trash from "../../Icons/Trash.vue";
import DropdownItem from "../Dropdown/DropdownItem.vue";
import DropdownButton from "../Dropdown/DropdownButton.vue";
import PureButtonLink from "../Button/PureButtonLink.vue";
import ArrowTopRightOnSquare from "../../Icons/ArrowTopRightOnSquare.vue";

const { t: $t } = useI18n()

const routePrefix = inject('routePrefix');
const confirmation = inject('confirmation');

const props = defineProps({
    itemId: {
        type: String,
        required: true,
    },
    urlPath: {
        type: String,
        required: true,
    }
})

const emit = defineEmits(['onDelete'])

const {notify} = useNotifications();

const confirmationDeletion = ref(false);

const deletePage = () => {
    confirmation()
        .title($t('page.delete_pages'))
        .description($t('page.confirm_delete_pages'))
        .destructive()
        .onConfirm((dialog) => {
            dialog.isLoading(true);

            router.delete(route(`${routePrefix}.pages.delete`, {
                page: props.itemId,
            }), {
                onSuccess() {
                    confirmationDeletion.value = false;
                    notify('success', $t('page.page_deleted'))
                    emit('onDelete')
                    emitter.emit('pageDelete', props.itemId);
                    dialog.reset();
                },
                onFinish() {
                    dialog.close();
                }
            })
        })
        .show();
}
</script>
<template>
    <div>
        <div class="flex flex-row items-center justify-end gap-xs">
            <PureButtonLink :href="route(`${routePrefix}.pages.show`, {slug: urlPath})" :native="true" target="_blank"
                            v-tooltip="$t('general.open')">
                <ArrowTopRightOnSquare/>
            </PureButtonLink>

            <Dropdown width-classes="w-32" placement="bottom-end">
                <template #trigger>
                    <DropdownButton/>
                </template>

                <template #content>
                    <DropdownItem :href="route(`${routePrefix}.pages.edit`, {page: itemId})">
                        <PencilSquare class="mr-xs"/>
                        {{ $t('general.edit') }}
                    </DropdownItem>

                    <DropdownItem @click="deletePage" as="button">
                        <Trash class="text-red-500 mr-xs"/>
                        {{ $t('general.delete') }}
                    </DropdownItem>
                </template>
            </Dropdown>
        </div>
    </div>
</template>
