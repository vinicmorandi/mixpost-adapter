import {computed} from "vue";
import {usePage} from "@inertiajs/vue3";

const useEnterpriseConsole = () => {
    const enterpriseConsole = computed(() => {
        return usePage().props.mixpost.enterpriseConsole;
    })

    return {
        enterpriseConsole
    }
}

export default useEnterpriseConsole;
