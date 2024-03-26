<script setup>
import {computed} from "vue";
import SearchInput from "../Util/SearchInput.vue";

const props = defineProps({
    modelValue: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['update:modelValue']);

const total = computed(() => {
    return 0;
    // return props.modelValue.tags.length + props.modelValue.accounts.length;
})

const clear = () => {
    emit('update:modelValue', Object.assign(props.modelValue, {
        keyword: ''
    }))
}
</script>
<template>
    <div class="flex items-center">
        <SearchInput v-model="modelValue.keyword" :placeholder="$t('general.search')"/>
    </div>
</template>
