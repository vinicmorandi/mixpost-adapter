import {computed} from "vue";

const useProviderClassesColor = (provider) => {
    const textClasses = computed(() => {
        return {
            'facebook': 'text-facebook',
            'facebook_page': 'text-facebook',
            'facebook_group': 'text-facebook',
            'instagram': 'text-instagram',
            'mastodon': 'text-mastodon',
            'youtube': 'text-youtube',
            'pinterest': 'text-pinterest',
            'twitter': 'text-twitter',
            'linkedin': 'text-linkedin',
            'linkedin_page': 'text-linkedin',
            'tiktok': 'text-tiktok',
        }[provider];
    });

    const borderClasses = computed(() => {
        return {
            'facebook': 'border-facebook',
            'facebook_page': 'border-facebook',
            'facebook_group': 'border-facebook',
            'instagram': 'border-instagram',
            'mastodon': 'border-mastodon',
            'youtube': 'border-youtube',
            'pinterest': 'border-pinterest',
            'twitter': 'border-twitter',
            'linkedin': 'border-linkedin',
            'linkedin_page': 'border-linkedin',
            'tiktok': 'border-tiktok',
        }[provider];
    });

    const activeBgClasses = computed(() => {
        return {
            'facebook': 'bg-facebook',
            'facebook_page': 'bg-facebook',
            'facebook_group': 'bg-facebook',
            'instagram': 'bg-instagram',
            'mastodon': 'bg-mastodon',
            'youtube': 'bg-youtube',
            'pinterest': 'bg-pinterest',
            'twitter': 'bg-twitter',
            'linkedin': 'bg-linkedin',
            'linkedin_page': 'bg-linkedin',
            'tiktok': 'bg-tiktok',
        }[provider];
    });

    return {
        textClasses,
        borderClasses,
        activeBgClasses
    }
}

export default useProviderClassesColor;
