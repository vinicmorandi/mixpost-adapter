<script setup>
import {computed, inject, onMounted, ref} from "vue";
import { useI18n } from "vue-i18n";
import {random} from "lodash";
import {Head, useForm, Link, router} from '@inertiajs/vue3';
import AdminLayout from "@/Layouts/Admin.vue";
import usePageMode from "@/Composables/usePageMode";
import useNotifications from "@/Composables/useNotifications";
import PageHeader from "@/Components/DataDisplay/PageHeader.vue";
import Panel from "@/Components/Surface/Panel.vue";
import HorizontalGroup from "@/Components/Layout/HorizontalGroup.vue";
import Error from "@/Components/Form/Error.vue";
import Input from "@/Components/Form/Input.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import SecondaryButton from "../../../Components/Button/SecondaryButton.vue";
import DangerButton from "../../../Components/Button/DangerButton.vue";
import DialogModal from "../../../Components/Modal/DialogModal.vue";
import ColorPicker from "../../../Components/Package/ColorPicker.vue";
import {COLOR_PALLET_LIST} from "../../../Constants/ColorPallet";
import Eye from "../../../Icons/Eye.vue";
import Trash from "../../../Icons/Trash.vue";
import ArrowTopRightOnSquare from "../../../Icons/ArrowTopRightOnSquare.vue";

defineOptions({layout: AdminLayout});

const { t: $t } = useI18n()

const props = defineProps({
    mode: {
        required: true,
        type: String,
        default: 'create',
    },
    workspace: {
        type: Object
    }
})

const {notify} = useNotifications();

const routePrefix = inject('routePrefix');
const confirmation = inject('confirmation')

const pageTitle = computed(() => {
    if (isCreate.value) {
        return $t("workspace.create_workspace");
    }

    return $t("workspace.edit_workspace");
});

const {isCreate, isEdit} = usePageMode();

const changeColorModal = ref(false);
const changeColorHex = ref('');

const form = useForm({
    name: isEdit.value ? props.workspace.name : '',
    hex_color: isEdit.value ? props.workspace.hex_color : ''
});

const selectColor = () => {
    form.hex_color = changeColorHex.value
    changeColorModal.value = false;
}

const pickRandomColor = () => {
    const colorList = COLOR_PALLET_LIST();

    return colorList[random(0, colorList.length - 1)]
}

onMounted(() => {
    if (isCreate.value) {
        const randomColor = pickRandomColor();

        form.hex_color = randomColor;
        changeColorHex.value = randomColor;
    }

    if (isEdit.value) {
        changeColorHex.value = props.workspace.hex_color;
    }
})

const store = (login = false) => {
    form.transform((data) => ({
        ...data,
        login
    })).post(route(`${routePrefix}.workspaces.store`))
}

const update = () => {
    form.put(route(`${routePrefix}.workspaces.update`, {'workspace': props.workspace.uuid}), {
        onSuccess: () => {
            notify('success', $t('workspace.workspace_updated'));
        }
    })
}

const deleteWorkspace = () => {
    confirmation()
        .title($t("workspace.delete_workspace"))
        .description($t("workspace.confirm_delete_workspace"))
        .destructive()
        .onConfirm((dialog) => {
            dialog.isLoading(true);

            router.delete(route(`${routePrefix}.workspaces.delete`, {workspace: props.workspace.uuid}), {
                preserveScroll: true,
                onSuccess() {
                    notify('success', $t('workspace.workspace_deleted'))
                },
                onFinish() {
                    dialog.reset();
                }
            });

        })
        .show();
}

const submit = () => {
    if (isCreate.value) {
        store();
    }

    if (isEdit.value) {
        update();
    }
}
</script>
<template>
    <Head :title="pageTitle"/>

    <div class="row-py mb-2xl w-full sm:max-w-3xl mx-auto">
        <PageHeader :title="pageTitle">
            <div v-if="isEdit" class="flex">
                <Link :href="route('mixpost.switchWorkspace', { workspace: workspace.uuid, redirect: true })"
                      method="post"
                      as="button" type="button" class="mr-xs">
                    <SecondaryButton size="sm">
                        <template #icon>
                            <ArrowTopRightOnSquare/>
                        </template>
                        {{ $t("general.open") }}
                    </SecondaryButton>
                </Link>

                <Link :href="route(`${routePrefix}.workspaces.view`, { workspace: workspace.uuid })" class="mr-xs">
                    <SecondaryButton size="sm">
                        <template #icon>
                            <Eye/>
                        </template>
                        {{ $t("general.view") }}
                    </SecondaryButton>
                </Link>

                <DangerButton @click="deleteWorkspace" size="sm">
                    <template #icon>
                        <Trash/>
                    </template>
                </DangerButton>
            </div>
        </PageHeader>

        <div class="row-px">
            <form method="post" @submit.prevent="submit">
                <Panel>
                    <template #title>{{ $t("general.details") }}</template>

                    <HorizontalGroup>
                        <template #title>
                            <label for="name">{{ $t("general.name") }}</label>
                        </template>

                        <div class="w-full">
                            <Input v-model="form.name" type="text" id="name" :placeholder="$t('workspace.workspace_name')"
                                   class="w-full"
                                   autocomplete="off" :autofocus="isCreate" required/>
                            <Error :message="form.errors.name" class="mt-1"/>
                        </div>
                    </HorizontalGroup>

                    <HorizontalGroup class="mt-lg">
                        <template #title>
                            Color
                        </template>

                        <div @click="changeColorModal = true" :style="{ 'background': form.hex_color }" role="button"
                             type="button" class="w-xl h-xl rounded-md"/>
                    </HorizontalGroup>

                    <div class="flex items-center mt-lg">
                        <PrimaryButton type="submit">{{
                                isCreate ? $t("general.create") : $t("general.update")
                            }}
                        </PrimaryButton>

                        <template v-if="isCreate">
                            <SecondaryButton @click="store(true)" type="button" class="ml-xs">
                                {{ $t("workspace.create_and_login") }}
                            </SecondaryButton>
                        </template>
                    </div>
                </Panel>
            </form>
        </div>
    </div>

    <DialogModal :show="changeColorModal" max-width="md" @close="changeColorModal = false">
        <template #header>
            {{ $t("workspace.change_workspace_color") }}
        </template>
        <template #body>
            <template v-if="changeColorModal" class="flex flex-col">
                <ColorPicker v-model="changeColorHex"/>
            </template>
        </template>
        <template #footer>
            <SecondaryButton @click="changeColorModal = false" class="mr-xs">{{
                    $t("general.cancel")
                }}
            </SecondaryButton>
            <PrimaryButton @click="selectColor">{{ $t("general.done") }}</PrimaryButton>
        </template>
    </DialogModal>
</template>
