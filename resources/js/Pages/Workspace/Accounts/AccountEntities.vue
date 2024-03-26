<script setup>
import { inject, ref } from "vue";
import { useI18n } from "vue-i18n";
import { router } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import PageHeader from "@/Components/DataDisplay/PageHeader.vue";
import Panel from "@/Components/Surface/Panel.vue";
import Checkbox from "@/Components/Form/Checkbox.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import NoResult from "../../../Components/Util/NoResult.vue";

const { t: $t } = useI18n()

const title = $t("account.account_entities");

const props = defineProps({
    provider: {
        required: true,
        type: String,
    },
    entities: {
        required: true,
        type: Array,
    },
});

const workspaceCtx = inject("workspaceCtx");

const form = ref({
    selected: [],
});

const save = () => {
    if (!form.value.selected.length) {
        return;
    }

    router.post(
        route("mixpost.accounts.entities.store", {
            workspace: workspaceCtx.id,
            provider: props.provider,
        }),
        {
            items: form.value.selected,
        }
    );
};
</script>
<template>
    <Head :title="title" />

    <div class="w-full max-w-6xl mx-auto row-py">
        <PageHeader :title="$t('account.choose_entity')">
            <template #description>
                {{ $t("account.select_the_social_entities") }}
            </template>
        </PageHeader>

        <div class="mt-lg row-px">
            <Panel>
                <div v-for="entity in entities" class="mb-sm last:mb-0">
                    <label class="flex items-center cursor-pointer">
                        <Checkbox
                            v-model:checked="form.selected"
                            :value="entity.id"
                            class="mr-md"
                        />

                        <span class="flex items-center">
                            <img
                                :src="entity.image"
                                class="rounded-full w-8 h-8 object-cover mr-xs border border-gray-200"
                                alt="Image"
                            />
                            <span>
                                <span class="font-semibold">
                                    <span>{{ entity.name }}</span>
                                    <span
                                        v-if="
                                            entity.data &&
                                            entity.data.hasOwnProperty(
                                                'suffix'
                                            ) &&
                                            entity.data.suffix.value
                                        "
                                    >
                                        ({{ entity.data.suffix.value }})
                                    </span>
                                </span>
                                <span
                                    v-if="entity.connected"
                                    class="block text-green-500 text-sm font-medium"
                                >
                                    {{ $t("account.connected") }}</span
                                >
                            </span>
                        </span>
                    </label>
                </div>

                <template v-if="!entities.length">
                    <NoResult />
                </template>
            </Panel>

            <div v-if="entities.length">
                <PrimaryButton
                    @click="save"
                    class="mt-lg"
                    :disabled="!form.selected.length"
                    > {{ $t("account.choose") }}</PrimaryButton
                >
            </div>
        </div>
    </div>
</template>
