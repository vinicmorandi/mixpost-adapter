<script setup>
import {ref} from "vue";
import DialogModal from "@/Components/Modal/DialogModal.vue"
import SecondaryButton from "@/Components/Button/SecondaryButton.vue"
import Preloader from "@/Components/Util/Preloader.vue";
import EditorButton from "../Button/EditorButton.vue";
import PrimaryButton from "../Button/PrimaryButton.vue";
import VariableList from "./VariableList.vue";
import VariableForm from "./VariableForm.vue";
import Variable from "../../Icons/Variable.vue";

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
const variable = ref(null);
const variables = ref([]);

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

const showForm = (variableOb = null) => {
    variable.value = variableOb;
    show.value = 'form';
}

const addVariable = (variableOb) => {
    variables.value.unshift(variableOb);
}

const modifyVariable = (variableOb) => {
    const index = variables.value.findIndex(item => item.id === variableOb.id);

    if (index !== -1) {
        variables.value[index] = variableOb;
    }
}
</script>
<template>
    <div>
        <EditorButton @click="openManager" v-tooltip="$t('variable.open_variable_manager')">
            <Variable/>
        </EditorButton>

        <DialogModal :show="showManager"
                     max-width="2xl"
                     :closeable="true"
                     :scrollable-body="true"
                     @close="closeManager">
            <template #header>
                <div class="flex items-center">
                    <Variable class="mr-xs"/>
                    {{ $t('variable.variable_manager') }}
                </div>
            </template>

            <template #body>
                <Preloader v-if="isLoading" :opacity="50"/>

                <template v-if="show === 'list'">
                    <VariableList
                        :variables="variables"
                        @update="variables = $event"
                        @edit="showForm($event)"
                        @insert="(event)=> {
                            $emit('insert', event)
                            closeManager();
                        }"
                    />
                </template>

                <template v-if="show === 'form'">
                    <VariableForm
                        :variable="variable"
                        @store="(event)=> {
                            addVariable(event);
                            showList();
                        }"
                        @update="(event)=> {
                           modifyVariable(event);
                           showList();
                        }"
                    />
                </template>
            </template>

            <template #footer>
                <template v-if="show === 'list'">
                    <PrimaryButton @click="()=> {showForm()}" class="mr-xs">{{
                            $t('variable.new_variable')
                        }}
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
