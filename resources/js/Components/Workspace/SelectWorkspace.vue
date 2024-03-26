<script setup>
import {inject, ref} from "vue";
import {throttle} from "lodash";

const props = defineProps({
    modelValue: {
        default: null
    },
    workspaces: {
        type: Array,
        default: []
    },
    exclude: {
        type: Array,
        default: []
    },
    disabled: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:modelValue']);

const routePrefix = inject('routePrefix');
const options = ref(props.workspaces);

const onSearch = (search, loading) => {
    if (!search) {
        return;
    }

    loading(true);
    fetch(loading, search);
}

const fetch = throttle((loading, search) => {
    axios.get(route(`${routePrefix}.workspaces.resources.items`), {
        params: {
            keyword: search,
            exclude: props.exclude
        }
    }).then((response) => {
        loading(false);

        options.value = response.data.data.map((item) => {
            return {
                key: item.uuid,
                label: item.name
            }
        });
    });
}, 350)
</script>
<template>
    <div class="relative w-full">
        <v-select
            :modelValue="modelValue"
            @update:modelValue="$emit('update:modelValue', $event)"
            :options="options"
            :filterable="false"
            :close-on-select="true"
            :disabled="disabled"
            :placeholder="$t('workspace.type_search_workspace')"
            @search="onSearch"
        >
            <template #no-options="{ search, searching, loading }">
                {{ $t('general.list_empty') }}
            </template>
        </v-select>
    </div>
</template>
