<script setup>
import {computed, ref} from "vue";
import SecondaryButton from "../Button/SecondaryButton.vue";
import DialogModal from "../Modal/DialogModal.vue";
import PrimaryButton from "../Button/PrimaryButton.vue";
import Link from "../../Icons/Link.vue";
import {cloneDeep} from "lodash";
import {nanoid} from 'nanoid'
import NoResult from "../Util/NoResult.vue";

const modal = ref(false);
const selected = ref(null);

const props = defineProps({
    modelValue: {
        required: true,
        type: Array,
    },
    blocks: {
        required: true,
        type: Object,
        default: []
    }
});

const emit = defineEmits(['update:modelValue']);

const blocks = computed(() => {
    return props.blocks.data.map((block) => {
        return {
            key: block.id,
            label: block.name
        }
    })
});

const getSelectedBlock = () => {
    const find = props.blocks.data.find(block => block.id === selected.value.key);

    return find ? cloneDeep(find) : null;
}

const add = () => {
    const block = getSelectedBlock();

    if (!block) {
        return;
    }

    emit('update:modelValue', [
        ...props.modelValue,
        ...[Object.assign(block, {random_key: nanoid()})]
    ]);

    close();
}

const open = () => {
    modal.value = true;
}

const close = () => {
    modal.value = false;
    selected.value = null;
}
</script>

<template>
    <SecondaryButton @click="open" size="sm" class="mr-xs">
        <template #icon>
            <Link/>
        </template>
        {{ $t('page.add_existing_block') }}
    </SecondaryButton>

    <DialogModal :show="modal"
                 max-width="sm"
                 :scrollable-body="true"
                 :closeable="true"
                 @close="close">
        <template #header>
            {{ $t('page.add_existing_block') }}
        </template>

        <template #body>
            <div class="relative">
                <template v-if="blocks.length">
                    <template v-if="modal">
                        <v-select
                            v-model="selected"
                            :options="blocks"
                            :filterable="false"
                            :clearable="false"
                            :close-on-select="true"
                            :placeholder="$t('page.search_blocks')"
                            append-to-body
                        >
                            <template #no-options="{ search, searching, loading }">
                                {{ $t('general.list_empty') }}
                            </template>
                        </v-select>
                    </template>
                </template>

                <template v-else>
                    <NoResult>
                        {{ $t('page.no_blocks_created') }}
                    </NoResult>
                </template>
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="close" class="mr-xs">{{ $t('general.cancel') }}</SecondaryButton>

            <template v-if="blocks.length">
                <PrimaryButton @click="add" :disabled="!selected">{{ $t('page.add_to_page') }}</PrimaryButton>
            </template>
        </template>
    </DialogModal>
</template>
