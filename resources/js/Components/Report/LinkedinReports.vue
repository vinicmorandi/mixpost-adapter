<script setup>
import Alert from "../Util/Alert.vue";

const props = defineProps({
    data: {
        type: Object,
        required: true
    },
    isLoading: {
        type: Boolean,
        required: true,
    }
});
</script>
<template>
    <div class="row-px mt-2xl">
        <div class="flex">
            <Alert variant="warning" :closeable="false">
                {{ $t('service.linkedin.not_support_accounts') }}
            </Alert>
        </div>
    </div>
</template>
