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
                <div>{{ $t('util.coming_soon') }}</div>
                <div>{{ $t('service.youtube.analytics_under_development') }}</div>
            </Alert>
        </div>
    </div>
</template>
