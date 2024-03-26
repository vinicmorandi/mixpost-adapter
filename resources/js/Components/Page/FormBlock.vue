<script setup>
import LabelSuffix from "../Form/LabelSuffix.vue";
import Input from "../Form/Input.vue";
import Select from "../Form/Select.vue";
import VerticalGroup from "../Layout/VerticalGroup.vue";
import Error from "../Form/Error.vue";

const props = defineProps({
    form: {
        required: true,
    },
    moduleComponent: {
        required: true,
    }
})
</script>
<template>
    <div class="flex items-center mb-lg gap-lg">
        <div class="w-full md:w-1/2">
            <VerticalGroup>
                <template #title>
                    <label for="m_name">{{ $t('general.name') }}
                        <LabelSuffix :danger="true">*</LabelSuffix>
                    </label>
                </template>

                <Input v-model="form.name"
                       type="text"
                       id="m_name"
                       class="w-full"
                       :autofocus="true"
                       :error="form.errors.name !== undefined"
                       required/>

                <template #footer>
                    <Error :message="form.errors.name"/>
                </template>
            </VerticalGroup>
        </div>

        <div class="w-full md:w-1/2">
            <VerticalGroup>
                <template #title>
                    <label for="m_status">{{ $t('general.status') }}</label>
                </template>

                <Select v-model="form.status" :error="form.errors.status !== undefined" id="m_status">
                    <option :value="1">{{ $t('general.enabled') }}</option>
                    <option :value="0">{{ $t('general.disabled') }}</option>
                </Select>

                <template #footer>
                    <Error :message="form.errors.status"/>
                </template>
            </VerticalGroup>
        </div>
    </div>

    <component :is="moduleComponent" :content="form.content"/>
</template>
