import {onBeforeMount, ref} from "vue";
import {useI18n} from "vue-i18n";
import {Trans} from '@/Services/Internationalization'

const useBootstrap = () => {
    const I18n = useI18n();
    const complete = ref(false);

    onBeforeMount(async () => {
        await Trans.bootstrap(I18n).finally(() => {
            setTimeout(() => {
                complete.value = true;
            }, 400)
        });
    });

    return {
        bootstrapComplete: complete
    }
};

export default useBootstrap;
