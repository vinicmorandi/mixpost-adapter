import {usePage} from "@inertiajs/vue3";

const useTheme = () => {
    const getLogo = () => {
        return usePage().props.mixpost.theme.logo;
    }
    const getColors = () => {
        return usePage().props.mixpost.theme.colors;
    }

    const getDefaultChartDataSetColors = () => {
        return {
            borderColor: getColors().primary_colors[500],
            pointBackgroundColor: getColors().primary_colors[500],
            pointBorderColor: getColors().primary_colors[500],
        }
    }

    return {
        getLogo,
        getColors,
        getDefaultChartDataSetColors,
    }
};

export default useTheme;
