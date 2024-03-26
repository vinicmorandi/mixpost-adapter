<script setup>
import {inject, ref} from "vue";
import emitter from "@/Services/emitter";
import {router} from "@inertiajs/vue3";
import useAuth from "@/Composables/useAuth";
import PureButtonLink from "@/Components/Button/PureButtonLink.vue";
import Eye from "../../Icons/Eye.vue";
import PencilSquare from "../../Icons/PencilSquare.vue";
import Dropdown from "../Dropdown/Dropdown.vue";
import Trash from "../../Icons/Trash.vue";
import DropdownItem from "../Dropdown/DropdownItem.vue";
import DropdownButton from "../Dropdown/DropdownButton.vue";

const routePrefix = inject('routePrefix');
const confirmation = inject('confirmation');

const props = defineProps({
    itemId: {
        type: Number,
        required: true,
    }
})

const emit = defineEmits(['onDelete'])

const {user} = useAuth();

const confirmationDeletion = ref(false);

const deleteUser = () => {
    confirmation()
        .title($t("user.delete_user"))
        .description($t("user.confirm_delete_user"))
        .destructive()
        .onConfirm((dialog) => {
            dialog.isLoading(true);

            router.delete(route(`${routePrefix}.users.delete`, {
                user: props.itemId,
            }), {
                onSuccess() {
                    confirmationDeletion.value = false;
                    emit('onDelete')
                    emitter.emit('userDelete', props.itemId);
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
            <PureButtonLink :href="route(`${routePrefix}.users.view`, {user: itemId})" v-tooltip='$t("general.view")'>
                <Eye/>
            </PureButtonLink>

            <Dropdown width-classes="w-32" placement="bottom-end">
                <template #trigger>
                    <DropdownButton/>
                </template>

                <template #content>
                    <DropdownItem :href="route(`${routePrefix}.users.edit`, {user: itemId})">
                        <PencilSquare class="mr-xs"/>
                        {{ $t("general.edit") }}
                    </DropdownItem>

                    <template v-if="user.id !== itemId">
                        <DropdownItem @click="deleteUser" as="button">
                            <Trash class="text-red-500 mr-xs"/>
                            {{ $t("general.delete") }}
                        </DropdownItem>
                    </template>
                </template>
            </Dropdown>
        </div>
    </div>
</template>
