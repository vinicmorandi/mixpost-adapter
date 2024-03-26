import {computed} from "vue";
import {usePage} from "@inertiajs/vue3";

const useAuth = () => {
    const user = computed(() => {
        return usePage().props.auth.user;
    })

    const impersonating = computed(() => {
        return usePage().props.auth.impersonating;
    })

    const workspaces = computed(() => {
        return user.value.workspaces;
    })

    return {
        user,
        impersonating,
        workspaces
    }
};

export default useAuth;
