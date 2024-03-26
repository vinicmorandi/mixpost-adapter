<script setup>
import {inject, ref} from "vue";
import {throttle} from "lodash";

const props = defineProps({
    modelValue: {
        default: null
    },
    users: {
        type: Array,
        default: []
    },
    exclude: {
        type: Array,
        default: []
    },
    filter: {
        type: Object,
        default: {}
    }
})

const emit = defineEmits(['update:modelValue']);

const routePrefix = inject('routePrefix');
const options = ref(props.users);

const onSearch = (search, loading) => {
    if (!search) {
        return;
    }

    loading(true);
    fetch(loading, search);
}

const fetch = throttle((loading, search) => {
    axios.get(route(`${routePrefix}.users.resources.items`), {
        params: Object.assign({
            keyword: search,
            exclude: props.exclude
        }, props.filter)
    }).then((response) => {
        loading(false);

        options.value = response.data.data.map((item) => {
            return {
                key: item.id,
                label: item.name
            }
        });
    });
}, 350)
</script>
<template>
    <div class="relative">
        <v-select
            :modelValue="modelValue"
            @update:modelValue="$emit('update:modelValue', $event)"
            :options="options"
            :filterable="false"
            :close-on-select="true"
            :placeholder="$t('user.type_search_user')"
            @search="onSearch"
        >
            <template #no-options="{ search, searching, loading }">
                {{ $t('general.list_empty') }}
            </template>
        </v-select>
    </div>
</template>
