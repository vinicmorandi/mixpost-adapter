import {inject} from "vue";
import {router} from "@inertiajs/vue3";
import usePostVersions from "@/Composables/usePostVersions";
import NProgress from "nprogress";

const useTemplate = () => {
    const workspaceCtx = inject('workspaceCtx');
    const {versionObject} = usePostVersions();
    const createPost = (content) => {
        router.post(route('mixpost.posts.store', {workspace: workspaceCtx.id}), {
            versions: [
                {...versionObject(0, true), ...{content}}
            ],
        });
    }

    const formatTemplateContent = (content) => {
        return content.map((item) => {
            return {
                body: item.body,
                media: item.media.map(itemMedia => itemMedia.id)
            }
        })
    }

    const deleteTemplate = (template) => {
        NProgress.start();

        axios.delete(route('mixpost.templates.api.delete', {
            workspace: workspaceCtx.id,
            template: template.id
        })).then(() => {
            router.get(route('mixpost.templates.index', {workspace: workspaceCtx.id}), {}, {
                preserveScroll: true,
            });
        }).finally(() => {
            NProgress.done();
        });
    }

    return {
        createPost,
        formatTemplateContent,
        deleteTemplate
    }
}

export default useTemplate;
