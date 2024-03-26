<script setup>
import {inject, ref} from "vue";
import NProgress from 'nprogress';
import useNotifications from "../../Composables/useNotifications";
import Preloader from "../Util/Preloader.vue";

defineProps({
    withPreloader: {
        type: Boolean,
        default: true
    }
})

const emit = defineEmits(['upload']);

const routePrefix = inject('routePrefix');
const {notify} = useNotifications();

const input = ref(null);
const isLoading = ref(false);

const mimeTypes = [
    'image/jpg',
    'image/jpeg',
    'image/gif',
    'image/png'
];

const filterFiles = (files) => {
    return Array.from(files).filter((file) => {
        return mimeTypes.includes(file.type);
    });
}

const browse = () => {
    input.value.click();
}

const upload = (file) => {
    const formData = new FormData();
    formData.append("file", file);

    NProgress.start();
    isLoading.value = true;

    axios.post(route(`${routePrefix}.files.upload`), formData)
        .then(function (response) {
            emit('upload', response.data);
        })
        .catch(function (error) {
            notify('error', error.response.data.message);
        }).finally(() => {
        NProgress.done();
        isLoading.value = false;
    });
}

const onBrowse = (e) => {
    const files = filterFiles(e.target.files);

    if (files.length) {
        input.value.value = null;
        upload(files[0]);
    }
}
</script>
<template>
    <div>
        <div @click="browse" role="button" class="inline-flex relative">
            <slot/>
            <Preloader v-if="withPreloader && isLoading" :opacity="75"/>
        </div>

        <input
            ref="input"
            type="file"
            :accept="mimeTypes.join(',')"
            class="hidden"
            @change="onBrowse"
        />
    </div>
</template>
