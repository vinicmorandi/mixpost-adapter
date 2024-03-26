<script setup>
import { inject, ref } from "vue";
import { Head, useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import PageHeader from "../../Components/DataDisplay/PageHeader.vue";
import PrimaryButton from "../../Components/Button/PrimaryButton.vue";
import Panel from "../../Components/Surface/Panel.vue";
import AddPostingTime from "../../Components/PostingSchedule/AddPostingTime.vue";
import PostingTimes from "../../Components/PostingSchedule/PostingTimes.vue";
import useNotifications from "../../Composables/useNotifications";
import SecondaryButton from "../../Components/Button/SecondaryButton.vue";
import ConfirmationModal from "../../Components/Modal/ConfirmationModal.vue";
import DangerButton from "../../Components/Button/DangerButton.vue";

const { t: $t } = useI18n()

const workspaceCtx = inject('workspaceCtx');

const props = defineProps({
    times: {
        required: true,
        type: Array,
    }
})

const { notify } = useNotifications();

const form = useForm({
    times: props.times,
})

const confirmationClear = ref(false);

const clear = () => {
    form.times.forEach(day => {
        day.times = [];
    });

    confirmationClear.value = false;
}

const update = () => {
    form.put(route('mixpost.postingSchedule.update', { workspace: workspaceCtx.id }), {
        preserveScroll: true,
        onSuccess() {
            notify('success', $t('posting_schedule.posting_times_updated'))
        }
    })
}
</script>
<template>
    <Head :title="$t('posting_schedule.posting_schedule')" />

    <div class="w-full max-w-6xl mx-auto row-py">
        <PageHeader :title="$t('posting_schedule.posting_schedule')">
            <template #description>
                {{ $t("posting_schedule.send_posting_schedule") }}
            </template>
        </PageHeader>

        <div class="w-full row-px">
            <Panel>
                <template #title> {{ $t("posting_schedule.add_new_posting_time") }}</template>
                <AddPostingTime v-model="form.times" />
            </Panel>

            <Panel class="mt-lg">
                <template #title> {{ $t("posting_schedule.posting_times") }}</template>

                <template #description> {{ $t("posting_schedule.send_posting_schedule") }}
                </template>

                <template #action>
                    <SecondaryButton @click="confirmationClear = true"> {{ $t("posting_schedule.clear_all_posting_times") }}
                    </SecondaryButton>
                </template>

                <PostingTimes :times="form.times" />
            </Panel>

            <PrimaryButton @click="update" :disabled="form.processing" class="mt-md">{{ $t("posting_schedule.update_posting_times") }}
            </PrimaryButton>
        </div>
    </div>

    <ConfirmationModal :show="confirmationClear" variant="danger" @close="confirmationClear = false">
        <template #header>
            {{ $t("posting_schedule.clear_all_posting_times") }}
        </template>
        <template #body>
            {{ $t("posting_schedule.confirmation_posting_times") }}
        </template>
        <template #footer>
            <SecondaryButton @click="confirmationClear = false" class="mr-xs"> {{ $t("general.cancel") }}
            </SecondaryButton>
            <DangerButton @click="clear">{{ $t("general.delete") }}</DangerButton>
        </template>
    </ConfirmationModal>
</template>
