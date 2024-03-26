<script setup>
import { useI18n } from "vue-i18n";
import {WEEKDAYS} from "../Calendar/constants";
import 'flatpickr/dist/flatpickr.css';
import '@css/overrideFlatPickr.css'
import useSettings from "../../Composables/useSettings";
import {convertTime24to12} from "../../helpers";
import {sortBy} from "lodash";
import Switch from "../Form/Switch.vue";
import X from "../../Icons/X.vue";
import PureDangerButton from "../Button/PureDangerButton.vue";

const { t: $t } = useI18n()

const props = defineProps({
    times: {
        type: Array,
        default: [],
    }
});

const emit = defineEmits(['update']);

const {timeZone, timeFormat} = useSettings();

const getWeekName = (id) => {
    const find = WEEKDAYS.find(day => day.id === id);

    if (!find) {
        return null;
    }

    return $t(`calendar.weekdays.${find.key}.full`);
}

const getDayTimes = (day) => {
    const sorted = sortBy(day.times, [(item) => item.value]);

    return sorted.map((item) => {
        return {
            value: getTimeFormat(item.value)
        };
    });
}

const getTimeFormat = (value) => {
    if (timeFormat === 12) {
        return convertTime24to12(value, 'hh:mm aaa');
    }

    return value;
}

const remove = (dayIndex, timeIndex) => {
    props.times[dayIndex].times.splice(timeIndex, 1);
}
</script>
<template>
    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-7 gap-xs">
        <template v-for="(day, dayIndex) in times" :key="day.id">
            <div class="flex flex-col border border-gray-300 rounded-lg p-xs">
                <div class="font-medium text-center">{{ getWeekName(day.id) }}</div>

                <div class="flex justify-center mt-xs">
                    <Switch v-model="day.status">
                        <span class="mr-xs">
                            <span v-if="day.status">{{ $t('general.on') }}</span>
                            <span v-if="!day.status">{{ $t('general.off') }}</span>
                        </span>
                    </Switch>
                </div>

                <div class="flex flex-col justify-center mt-xs">
                    <template v-if="!day.times.length">
                        <span class="text-gray-500 text-sm text-center">{{ $t('general.empty') }}</span>
                    </template>

                    <template v-if="day.times.length">
                        <div v-for="(item, timeIndex) in getDayTimes(day)" :key="item.value"
                             class="relative flex items-center justify-between px-lg py-xs group text-gray-500 text-sm">
                            <span>{{ item.value.split(':')[0] }}</span>
                            <span>:</span>
                            <span>{{ item.value.split(':')[1] }}</span>

                            <span class="uppercase">{{ item.format }}</span>

                            <span
                                class="absolute right-0 opacity-0 group-hover:opacity-100 transition-opacity ease-in-out duration-200">
                                <PureDangerButton @click="remove(dayIndex, timeIndex)">
                                    <X class="!w-5 !-5"/>
                                </PureDangerButton>
                            </span>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>
</template>
