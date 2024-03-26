<script setup>
import {ref} from "vue";
import DialogModal from "@/Components/Modal/DialogModal.vue"
import SecondaryButton from "@/Components/Button/SecondaryButton.vue"
import Preloader from "@/Components/Util/Preloader.vue";
import Hashtag from "../../Icons/Hashtag.vue";
import EditorButton from "../Button/EditorButton.vue";
import HashtagGroups from "./HashtagGroups.vue";
import HashtagGroupForm from "./HashtagGroupForm.vue";
import PrimaryButton from "../Button/PrimaryButton.vue";

const props = defineProps({
    editAllowed: {
        type: Boolean,
        default: true,
    }
})

const emit = defineEmits(['insert'])

const showManager = ref(false);
const isLoading = ref(false);
const show = ref(null);
const group = ref(null);
const groups = ref([]);

const openManager = () => {
    showManager.value = true;
    showGroups();
}

const closeManager = () => {
    showManager.value = false;
    show.value = null;
}

const showGroups = () => {
    show.value = 'groups';
}

const showForm = (groupObj = null) => {
    group.value = groupObj;
    show.value = 'form';
}

const addGroup = (group) => {
    groups.value.unshift(group);
}

const modifyGroup = (groupObj) => {
    const index = groups.value.findIndex(item => item.id === groupObj.id);

    if (index !== -1) {
        groups.value[index] = groupObj;
    }
}
</script>
<template>
    <div>
        <EditorButton @click="openManager" v-tooltip="$t('hashtag.open_hashtag')">
            <Hashtag/>
        </EditorButton>

        <DialogModal :show="showManager"
                     max-width="lg"
                     :closeable="true"
                     :scrollable-body="true"
                     @close="closeManager">
            <template #header>
                <div class="flex items-center">
                    <Hashtag class="mr-xs"/>
                    {{ $t('hashtag.hashtag_manager') }}
                </div>
            </template>

            <template #body>
                <Preloader v-if="isLoading" :opacity="50"/>

                <template v-if="show === 'groups'">
                    <HashtagGroups
                        :groups="groups"
                        @update="groups = $event"
                        @edit="showForm($event)"
                        @insert="(event)=> {
                            $emit('insert', event)
                            closeManager();
                        }"
                    />
                </template>

                <template v-if="show === 'form'">
                    <HashtagGroupForm
                        :group="group"
                        @store="(event)=> {
                            addGroup(event);
                            showGroups();
                        }"
                        @update="(event)=> {
                           modifyGroup(event);
                           showGroups();
                        }"
                    />
                </template>
            </template>

            <template #footer>
                <template v-if="show === 'groups'">
                    <PrimaryButton @click="()=> {showForm()}" class="mr-xs">
                        {{ $t('hashtag.new_hashtag_group')}}
                    </PrimaryButton>
                </template>

                <template v-if="show === 'form'">
                    <SecondaryButton @click="showGroups" class="mr-xs">{{ $t('general.go_back') }}</SecondaryButton>
                </template>

                <SecondaryButton @click="closeManager">{{ $t('general.done') }}</SecondaryButton>
            </template>
        </DialogModal>
    </div>
</template>
