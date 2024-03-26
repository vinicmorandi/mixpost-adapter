<script setup>
import {computed, watch} from "vue";
import { useI18n } from "vue-i18n";
import {clone, get} from "lodash";
import Select from "@/Components/Form/Select.vue";
import ProviderOptionWrap from "@/Components/ProviderVersionOptions/ProviderOptionWrap.vue";
import Label from "@/Components/Form/Label.vue";
import HorizontalGroup from "../Layout/HorizontalGroup.vue";
import Checkbox from "../Form/Checkbox.vue";
import Collapse from "../Surface/Collapse.vue";
import OptionGroup from "./OptionGroup.vue";

const { t: $t } = useI18n()

const props = defineProps(['options', 'accounts', 'activeVersion'])

const availableAccounts = computed(() => {
    if (props.activeVersion === 0) {
        return props.accounts;
    }

    return props.accounts.filter(account => account.id === props.activeVersion);
})

watch(() => props.accounts, () => {
    setDefaultValues();
})

const setDefaultValues = () => {
    availableAccounts.value.forEach((account) => {
        const value = get(props.options.privacy_level, `account-${account.id}`);

        if (!value) {
            // props.options.privacy_level[`account-${account.id}`] = account.data.is_private ? 'FOLLOWER_OF_CREATOR' : 'PUBLIC_TO_EVERYONE';

            props.options.allow_comments[`account-${account.id}`] = false;
            props.options.allow_duet[`account-${account.id}`] = false;
            props.options.allow_stitch[`account-${account.id}`] = false;
        }
    })
}

const accountPrivacyLevelOptions = (account) => {
    if (!Array.isArray(account.data.privacy_levels)) {
        return [];
    }

    const privacyLevels = clone(account.data.privacy_levels);

    return privacyLevels.map((option) => {
        const names = {
            'PUBLIC_TO_EVERYONE': $t('service.tiktok.everyone'),
            'MUTUAL_FOLLOW_FRIENDS': $t('service.tiktok.friends'),
            'SELF_ONLY': $t('service.tiktok.only_you'),
            'FOLLOWER_OF_CREATOR': $t('service.tiktok.followers')
        }

        return {
            key: option,
            name: names.hasOwnProperty(option) ? names[option] : option,
        }
    })
}
</script>
<template>
    <ProviderOptionWrap :title="$t('service.provider_options', {provider: 'TikTok'})" provider="tiktok">
        <template v-for="account in availableAccounts" :key="account.id">
            <OptionGroup>
                <template #title>{{ account.name }}</template>

                <div class="w-full">
                    <Label :for="`privacy_level-${account.id}`">{{ $t('service.tiktok.who_watch_video') }}</Label>
                    <Select v-model="options.privacy_level[`account-${account.id}`]"
                            :id="`privacy_level-${account.id}`">
                        <option v-for="level in accountPrivacyLevelOptions(account)"
                                :value="level.key"
                                :key="level.key">
                            {{ level.name }}
                        </option>
                    </Select>
                </div>
                <div class="w-full mt-xs">
                    <Collapse>
                        <template #title>{{ $t('service.tiktok.privacy_settings') }}</template>

                        <div class="w-full sm:max-w-xs mt-xs">
                            <HorizontalGroup :flexColMobile="false">
                                <template #title><label
                                    :for="`allow_comments-${account.id}`">{{ $t('service.tiktok.allow_comments') }}</label>
                                </template>
                                <Checkbox v-model:checked="options.allow_comments[`account-${account.id}`]"
                                          :disabled="account.data.comment_disabled"
                                          :id="`allow_comments-${account.id}`"/>
                            </HorizontalGroup>


                            <HorizontalGroup :flexColMobile="false" class="mt-xs">
                                <template #title><label
                                    :for="`allow_duet-${account.id}`">{{ $t('service.tiktok.allow_duet') }}</label>
                                </template>
                                <Checkbox v-model:checked="options.allow_duet[`account-${account.id}`]"
                                          :disabled="account.data.duet_disabled"
                                          :id="`allow_duet-${account.id}`"/>
                            </HorizontalGroup>

                            <HorizontalGroup :flexColMobile="false" class="mt-xs">
                                <template #title><label
                                    :for="`allow_stitch-${account.id}`">{{ $t('service.tiktok.allow_stitch') }}</label>
                                </template>
                                <Checkbox v-model:checked="options.allow_stitch[`account-${account.id}`]"
                                          :disabled="account.data.stitch_disabled"
                                          :id="`allow_stitch-${account.id}`"/>
                            </HorizontalGroup>
                        </div>
                    </Collapse>

                    <div class="mt-xs">
                        <Collapse>
                            <template #title>{{ $t('service.tiktok.content_disclosure_setting') }}</template>

                            <div class="w-full sm:max-w-lg mt-xs">
                                <HorizontalGroup :flexColMobile="false">
                                    <template #title><label :for="`brand_content_toggle-${account.id}`">
                                        {{ $t('service.tiktok.promote_brand') }}</label>
                                    </template>
                                    <Checkbox v-model:checked="options.brand_content_toggle"
                                              :id="`brand_content_toggle-${account.id}`"/>
                                </HorizontalGroup>

                                <HorizontalGroup :flexColMobile="false" class="mt-xs">
                                    <template #title><label :for="`brand_organic_toggle-${account.id}`">
                                        {{ $t('service.tiktok.promote_third_brand') }}
                                    </label>
                                    </template>
                                    <Checkbox v-model:checked="options.brand_organic_toggle"
                                              :id="`brand_organic_toggle-${account.id}`"/>
                                </HorizontalGroup>
                            </div>
                        </Collapse>
                    </div>
                </div>
            </OptionGroup>
        </template>
    </ProviderOptionWrap>
</template>
