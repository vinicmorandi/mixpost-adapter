<script setup>
import { cloneDeep } from "lodash";
import { computed, inject, provide, ref } from "vue";
import { Head, router, useForm, Link } from "@inertiajs/vue3";
import NProgress from "nprogress";
import { useI18n } from "vue-i18n";
import usePostVersions from "@/Composables/usePostVersions";
import useEditor from "@/Composables/useEditor";
import useNotifications from "@/Composables/useNotifications";
import useTemplate from "../../../Composables/useTemplate";
import PageHeader from "../../../Components/DataDisplay/PageHeader.vue";
import Panel from "../../../Components/Surface/Panel.vue";
import Label from "../../../Components/Form/Label.vue";
import Input from "../../../Components/Form/Input.vue";
import LabelSuffix from "../../../Components/Form/LabelSuffix.vue";
import VerticalGroup from "../../../Components/Layout/VerticalGroup.vue";
import VariableManager from "../../../Components/VariableManager/VariableManager.vue";
import HashtagManager from "../../../Components/HashtagManager/HashtagManager.vue";
import Editor from "../../../Components/Package/Editor.vue";
import PostMedia from "../../../Components/Post/PostMedia.vue";
import EmojiPicker from "../../../Components/Package/EmojiPicker.vue";
import PostAddMedia from "../../../Components/Post/PostAddMedia.vue";
import PrimaryButton from "../../../Components/Button/PrimaryButton.vue";
import SecondaryButton from "../../../Components/Button/SecondaryButton.vue";
import Trash from "../../../Icons/Trash.vue";
import DangerButton from "../../../Components/Button/DangerButton.vue";

const { t: $t } = useI18n()

const props = defineProps(['template']);

const template = props.template ? cloneDeep(props.template) : null;

provide('postCtx', {});
const workspaceCtx = inject('workspaceCtx');

const { notify } = useNotifications();
const { versionContentObject } = usePostVersions();
const { insertEmoji, insertContent, focusEditor } = useEditor();
const { createPost, formatTemplateContent, deleteTemplate } = useTemplate();

const isLoading = ref(false);
const form = useForm({
    name: template ? template.name : '',
    content: template ? template.content : [versionContentObject()]
});

const title = computed(() => {
    if (props.template) {
        return $t('template.edit_template');
    }

    return $t('template.create_new_template')
})

const getFormattedData = () => {
    return {
        name: form.name,
        content: formatTemplateContent(form.content)
    };
}

const save = () => {
    if (!props.template) {
        store(getFormattedData());
    }

    if (props.template) {
        update(getFormattedData());
    }
}

const store = (data, redirectToCreateRoute = false) => {
    NProgress.start();
    isLoading.value = true;

    axios.post(route('mixpost.templates.api.store', { workspace: workspaceCtx.id }), data).then((response) => {
        notify('success', $t('template.template_created'));

        if (!redirectToCreateRoute) {
            router.get(route('mixpost.templates.edit', { workspace: workspaceCtx.id, template: response.data.id }), {}, {
                preserveScroll: true,
            });

            return;
        }

        router.get(route('mixpost.templates.create', { workspace: workspaceCtx.id }));
    }).finally(() => {
        NProgress.done();
        isLoading.value = false;
    });
}

const update = (data) => {
    NProgress.start();
    isLoading.value = true;

    axios.put(route('mixpost.templates.api.update', { workspace: workspaceCtx.id, template: props.template.id }), data)
        .then(() => {
            notify('success', $t('template.template_updated'));
        }).catch((error) => {
            if (error.response.status !== 422) {
                notify('error', error.response.data.message);
                return;
            }

            notify('error', $t('template.error_saving_template'));
        }).finally(() => {
            NProgress.done();
            isLoading.value = false;
        });
}
</script>
<template>
    <Head :title="title" />

    <div class="w-full max-w-5xl mx-auto row-py">
        <PageHeader :title="title">
            <div class="flex">
                <template v-if="template">
                    <PrimaryButton @click="() => {
                        createPost(getFormattedData().content)
                    }" class="mr-xs">
                        {{ $t('template.use_template') }}
                    </PrimaryButton>
                </template>

                <Link v-if="template" :href="route('mixpost.templates.create', { workspace: workspaceCtx.id })">
                <SecondaryButton class="mr-xs"> {{ $t('template.create_new') }}</SecondaryButton>
                </Link>

                <template v-if="template">
                    <DangerButton @click="() => { deleteTemplate(template) }" size="sm">
                        <Trash />
                    </DangerButton>
                </template>
            </div>
        </PageHeader>

        <div class="mt-lg row-px w-full">
            <form @submit.prevent="save">
                <Panel>
                    <VerticalGroup class="mb-md">
                        <template #title>
                            <label for="template_name">{{ $t('template.template_name') }}
                                <LabelSuffix :danger="true">*</LabelSuffix>
                            </label>
                        </template>
                        <Input type="text" v-model="form.name" id="template_name" required />
                    </VerticalGroup>


                    <template v-for="(item, index) in form.content" :key="index">
                        <Editor id="templateEditor" :value="item.body" :editable="true" @update="item.body = $event"
                            :placeholder="$t('post.type_something')">
                            <template #default="props">
                                <div
                                    class="relative flex items-center justify-between border-t border-gray-200 pt-md mt-md">
                                    <div class="flex items-center space-x-xs">
                                        <EmojiPicker @selected="insertEmoji({ editorId: 'templateEditor', emoji: $event })"
                                            @close="focusEditor({ editorId: 'templateEditor' })" />

                                        <PostAddMedia @insert="item.media = [...item.media, ...$event]"
                                            :selectedAccounts="[]" :activeVersion="0" :versions="[]" :media="item.media" />

                                        <HashtagManager :editAllowed="true"
                                            @insert="insertContent({ editorId: 'templateEditor', text: $event })" />

                                        <VariableManager :editAllowed="true"
                                            @insert="insertContent({ editorId: 'templateEditor', text: $event })" />
                                    </div>
                                </div>
                            </template>
                        </Editor>

                        <PostMedia :media="item.media" />
                    </template>
                </Panel>

                <div class="mt-md flex items-center">
                    <PrimaryButton :disabled="isLoading" type="submit" class="mr-xs">
                        {{ $t('template.save_template') }}
                    </PrimaryButton>

                    <template v-if="!template">
                        <PrimaryButton @click="() => { store(getFormattedData(), true) }" :disabled="isLoading" type="button">
                            {{ $t('template.save_create_new') }}
                        </PrimaryButton>
                    </template>
                </div>
            </form>
        </div>
    </div>
</template>
