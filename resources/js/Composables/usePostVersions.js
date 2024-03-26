const usePostVersions = () => {
    const versionContentObject = (body = '', media = []) => {
        return {
            body,
            media
        };
    }

    const versionObject = (accountId = 0, isOriginal = false, body = '', title = '', link = '') => {
        return {
            account_id: accountId,
            is_original: isOriginal,
            content: [versionContentObject(body)],
            options: {
                mastodon: {
                    sensitive: false,
                },
                facebook_page: {
                    type: 'post' // post, reel...etc.
                },
                instagram: {
                    type: 'post' // post, reel...etc.
                },
                youtube: {
                    title,
                    status: 'public'
                },
                pinterest: {
                    title,
                    link,
                    boards: {
                        'account-0': null
                    }
                },
                linkedin: {
                    visibility: 'PUBLIC'
                },
                tiktok: {
                    privacy_level: {
                        'account-0': ''
                    },
                    allow_comments: {
                        'account-0': false
                    },
                    allow_duet: {
                        'account-0': false
                    },
                    allow_stitch: {
                        'account-0': false
                    },
                    brand_content_toggle: false,
                    brand_organic_toggle: false
                },
            }
        }
    }

    const getOriginalVersion = (versions) => {
        const find = versions.find((version) => {
            return version.is_original && version.account_id === 0;
        })

        return find ? find : null;
    }

    const getAccountVersion = (versions, accountId) => {
        const find = versions.find((version) => {
            return version.account_id === accountId;
        })

        return find ? find : null;
    }

    const getIndexAccountVersion = (versions, accountId) => {
        return versions.findIndex(version => version.account_id === accountId);
    }

    const accountHasVersion = (versions, accountId) => {
        return versions.some(version => version.account_id === accountId);
    }

    return {
        versionContentObject,
        versionObject,
        getOriginalVersion,
        getAccountVersion,
        getIndexAccountVersion,
        accountHasVersion
    }
}

export default usePostVersions;
