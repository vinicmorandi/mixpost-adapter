<script setup>
import {computed} from "vue";
import {addDays, getDate, startOfWeek} from "date-fns";
import {clone} from "lodash";
import {utcToZonedTime} from "date-fns-tz";
import {WEEKDAYS} from "../constants";

const props = defineProps({
    timeZone: {
        required: false,
        type: String,
        default: 'UTC'
    },
    weekStartsOn: {
        required: false,
        type: Number,
        default: 0
    },
    selectedDate: {
        type: Date,
        required: true,
    },
    scrolled: {
        type: Boolean,
        required: false,
        default: false
    }
});

const start = computed(() => {
    return startOfWeek(props.selectedDate, {
        weekStartsOn: props.weekStartsOn,
    })
});

const today = computed(() => {
    return getDate(utcToZonedTime(new Date().toISOString(), props.timeZone));
});

const items = computed(() => {
    const days = clone(WEEKDAYS);

    const items = days.splice(props.weekStartsOn ? 0 : 6).concat(days);

    return items.map((item, index) => {
        const date = index === 0 ? start.value : addDays(start.value, index);
        const monthDay = getDate(date);

        return Object.assign(item, {
            date: monthDay,
            isToday: today.value === monthDay
        })
    })
})
</script>
<template>
    <div class="flex flex-row sticky top-0 bg-white z-10">
        <div class="w-full grid grid-cols-week-time-sm md:grid-cols-week-time">
            <div></div>
            <div v-for="(item, index) in items"
                 :key="index"
                 :class="{'text-primary-500': item.isToday, 'border-b-gray-200': scrolled, 'border-b-white': !scrolled}"
                 class="p-xs border-t border-b border-l border-gray-200 text-center font-semibold">
                <div class="text-base md:text-xl">{{ item.date }}</div>

                <span :class="{'text-gray-500': !item.isToday}">
                     <span class="hidden sm:block">{{ $t(`calendar.weekdays.${item.key}.short`) }}</span>
                     <span class="block sm:hidden">{{ $t(`calendar.weekdays.${item.key}.shortest`) }}</span>
                </span>
            </div>
        </div>
    </div>
</template>
