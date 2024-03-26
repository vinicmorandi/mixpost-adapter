import {format} from "date-fns";
import {useI18n} from "vue-i18n";
import buildLocalizeFn from "date-fns/locale/_lib/buildLocalizeFn";
import {Trans} from "../Services/Internationalization";
import enUs from "date-fns/locale/en-US";

const useDateLocalize = () => {
    const {t: $t} = useI18n()

    const ordinalNumber = (dirtyNumber, dirtyOptions) => {
        return Number(dirtyNumber);
    }

    const month = (dirtyIndex, options) => {
        const full = [
            $t('calendar.months.january.full'),
            $t('calendar.months.february.full'),
            $t('calendar.months.march.full'),
            $t('calendar.months.april.full'),
            $t('calendar.months.may.full'),
            $t('calendar.months.june.full'),
            $t('calendar.months.july.full'),
            $t('calendar.months.august.full'),
            $t('calendar.months.september.full'),
            $t('calendar.months.october.full'),
            $t('calendar.months.november.full'),
            $t('calendar.months.december.full'),
        ];

        const values = {
            narrow: full.map(m => m[0]),
            abbreviated: [
                $t('calendar.months.january.short'),
                $t('calendar.months.february.short'),
                $t('calendar.months.march.short'),
                $t('calendar.months.april.short'),
                $t('calendar.months.may.short'),
                $t('calendar.months.june.short'),
                $t('calendar.months.july.short'),
                $t('calendar.months.august.short'),
                $t('calendar.months.september.short'),
                $t('calendar.months.october.short'),
                $t('calendar.months.november.short'),
                $t('calendar.months.december.short'),
            ],
            wide: full,
        };

        return buildLocalizeFn({
            values,
            defaultWidth: "wide"
        });
    }

    const day = (dirtyIndex, options) => {
        const values = {
            narrow: [
                $t('calendar.weekdays.sunday.shortest'),
                $t('calendar.weekdays.monday.shortest'),
                $t('calendar.weekdays.tuesday.shortest'),
                $t('calendar.weekdays.wednesday.shortest'),
                $t('calendar.weekdays.thursday.shortest'),
                $t('calendar.weekdays.friday.shortest'),
                $t('calendar.weekdays.saturday.shortest'),
            ],
            short: [
                $t('calendar.weekdays.sunday.short'),
                $t('calendar.weekdays.monday.short'),
                $t('calendar.weekdays.tuesday.short'),
                $t('calendar.weekdays.wednesday.short'),
                $t('calendar.weekdays.thursday.short'),
                $t('calendar.weekdays.friday.short'),
                $t('calendar.weekdays.saturday.short'),
            ],
            abbreviated: [
                $t('calendar.weekdays.sunday.short'),
                $t('calendar.weekdays.monday.short'),
                $t('calendar.weekdays.tuesday.short'),
                $t('calendar.weekdays.wednesday.short'),
                $t('calendar.weekdays.thursday.short'),
                $t('calendar.weekdays.friday.short'),
                $t('calendar.weekdays.saturday.short'),
            ],
            wide: [
                $t('calendar.weekdays.sunday.full'),
                $t('calendar.weekdays.monday.full'),
                $t('calendar.weekdays.tuesday.full'),
                $t('calendar.weekdays.wednesday.full'),
                $t('calendar.weekdays.thursday.full'),
                $t('calendar.weekdays.friday.full'),
                $t('calendar.weekdays.saturday.full'),
            ],
        };

        return buildLocalizeFn({
            values,
            defaultWidth: "wide",
        })
    }

    const dayPeriodValues = (dirtyIndex, options) => {
        const values = {
            am: $t('calendar.dayperiod.am'),
            pm: $t('calendar.dayperiod.pm'),
        }

        return buildLocalizeFn({
            values: {
                narrow: values,
                abbreviated: values,
                wide: values
            },
            defaultWidth: "wide",
        })
    }

    const translatedFormat = (date, formatString, options = {}) => {
        const _options = {
            locale: {
                code: Trans.locale,
                localize: {
                    ordinalNumber,
                    month: month(),
                    day: day(),
                    dayPeriod: dayPeriodValues(),
                },
                formatDistance: enUs.formatDistance,
                formatLong: enUs.formatLong,
                formatRelative: enUs.formatRelative,
                match: enUs.match,
            }
        };

        return format(date, formatString, Object.assign(_options, options));
    }

    return {
        translatedFormat
    }
}

export default useDateLocalize;
