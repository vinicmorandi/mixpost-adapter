<script setup>
import {ref} from "vue";
import DialogModal from "@/Components/Modal/DialogModal.vue"
import SecondaryButton from "@/Components/Button/SecondaryButton.vue"
import Preloader from "@/Components/Util/Preloader.vue";
import EditorButton from "../Button/EditorButton.vue";
import RectangleGroup from "../../Icons/RectangleGroup.vue";
import PrimaryButton from "../Button/PrimaryButton.vue";
import TemplateList from "./TemplateList.vue";
import TemplateForm from "./TemplateForm.vue";

const props = defineProps({
    postContent: {
        type: Array,
        default: []
    },
});

const emit = defineEmits(['insert'])

const showManager = ref(false);
const isLoading = ref(false);
const show = ref(null);
const template = ref(null);
const templates = ref([]);

const openManager = () => {
    showManager.value = true;
    showList();
}

const closeManager = () => {
    showManager.value = false;
    show.value = null;
}

const showList = () => {
    show.value = 'list';
}

const showForm = (templateObj = null) => {
    template.value = templateObj;
    show.value = 'form';
}

const addTemplate = (templateOb) => {
    templates.value.unshift(templateOb);
}
</script>
<template>
    <div>
        <EditorButton @click="openManager" v-tooltip="$t('template.open_template_manager')">
            <RectangleGroup/>
        </EditorButton>

        <DialogModal :show="showManager"
                     max-width="5xl"
                     :closeable="true"
                     :scrollable-body="true"
                     @close="closeManager">
            <template #header>
                <div class="flex items-center">
                    <RectangleGroup class="mr-xs"/>
                    {{ $t('template.template_manager') }}
                </div>
            </template>

            <template #body>
                <Preloader v-if="isLoading" :opacity="50"/>

                <template v-if="show === 'list'">
                    <TemplateList
                        :templates="templates"
                        @update="templates = $event"
                        @edit="showForm($event)"
                        @select="(event)=> {
                            $emit('insert', event)
                            closeManager();
                        }"
                    />
                </template>

                <template v-if="show === 'form'">
                    <div class="w-full max-w-xl">
                        <TemplateForm
                            :template="template"
                            :content="postContent"
                            @store="(event)=> {
                            addTemplate(event);
                            showList();
                        }"
                            @update="showList"
                        />
                    </div>
                </template>
            </template>

            <template #footer>
                <template v-if="show === 'list'">
                    <PrimaryButton @click="()=> showForm(null)" class="mr-xs">
                        {{ $t('template.save_as_template') }}
                    </PrimaryButton>
                </template>

                <template v-if="show === 'form'">
                    <SecondaryButton @click="showList" class="mr-xs">{{ $t('general.go_back') }}</SecondaryButton>
                </template>

                <SecondaryButton @click="closeManager">{{ $t('general.done') }}</SecondaryButton>
            </template>
        </DialogModal>
    </div>
</template>


