<script setup>
import {inject, ref} from "vue";
import NProgress from "nprogress";
import Input from "../Form/Input.vue";
import VerticalGroup from "../Layout/VerticalGroup.vue";
import Label from "../Form/Label.vue";
import PrimaryButton from "../Button/PrimaryButton.vue";
import Alert from "../Util/Alert.vue";
import LabelSuffix from "../Form/LabelSuffix.vue";

const workspaceCtx = inject('workspaceCtx');

const props = defineProps({
    template: {
        type: [Object, null],
        default: null,
    },
    content: {
        type: Array,
        required: true
    },
})

const emit = defineEmits(['store', 'update']);

const isLoading = ref(false);
const form = ref(props.template ? props.template : {
    name: ''
})

const store = (data) => {
    NProgress.start();
    isLoading.value = true;

    axios.post(route('mixpost.templates.api.store', {workspace: workspaceCtx.id}), data).then((response) => {
        emit('store', response.data);
    }).finally(() => {
        NProgress.done();
        isLoading.value = false;
    });
}

const update = (data) => {
    NProgress.start();
    isLoading.value = true;

    axios.put(route('mixpost.templates.api.update', {
        workspace: workspaceCtx.id,
        template: props.template.id
    }), data).then(() => {
        emit('update', form.value);
        NProgress.done();
        isLoading.value = false;
    });
}

const save = () => {
    const data = {
        ...form.value,
        ...{
            content: props.content.map((item) => {
                return {
                    body: item.body,
                    media: item.media.map(itemMedia => itemMedia.id)
                }
            })
        }
    };

    if (props.template) {
        update(data);
        return;
    }

    store(data);
}
</script>
<template>
    <form @submit.prevent="save">
        <VerticalGroup>
            <template #title>
                <label for="template_name">{{ $t('template.template_name') }}
                    <LabelSuffix :danger="true">*</LabelSuffix>
                </label>
            </template>
            <Input type="text" v-model="form.name" id="template_name" required/>
        </VerticalGroup>

        <Alert variant="info" :closeable="false" class="mt-xs">
            {{ $t('template.save_content_current_version') }}
        </Alert>

        <PrimaryButton type="submit" :isLoading="isLoading" class="mt-sm">{{ $t('template.save_template') }}</PrimaryButton>
    </form>
</template>
