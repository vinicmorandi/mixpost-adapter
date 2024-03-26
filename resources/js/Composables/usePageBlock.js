import Editor from "../Components/BlockModules/Editor.vue";
import Html from "../Components/BlockModules/Html.vue";

const usePageBlock = () => {
    const modules = {
        Editor: {
            content: {
                body: ''
            },
            component: Editor
        },
        Html: {
            content: {
                body: ''
            },
            component: Html
        }
    }
    return {
        modules,
    }
}

export default usePageBlock;
