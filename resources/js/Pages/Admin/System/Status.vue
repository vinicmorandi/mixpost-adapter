<script setup>
import {Head} from '@inertiajs/vue3';
import {useI18n} from "vue-i18n";
import useNotifications from "../../../Composables/useNotifications";
import AdminLayout from "@/Layouts/Admin.vue";
import PageHeader from "../../../Components/DataDisplay/PageHeader.vue";
import Panel from "../../../Components/Surface/Panel.vue";
import Table from "../../../Components/DataDisplay/Table.vue";
import TableRow from "../../../Components/DataDisplay/TableRow.vue";
import TableCell from "../../../Components/DataDisplay/TableCell.vue";
import Badge from "../../../Components/DataDisplay/Badge.vue";
import PrimaryButton from "../../../Components/Button/PrimaryButton.vue";
import Clipboard from "../../../Icons/Clipboard.vue";

defineOptions({layout: AdminLayout});

const {t: $t} = useI18n()

const props = defineProps({
    env: String,
    debug: Boolean,
    horizon_status: String,
    has_queue_connection: Boolean,
    last_scheduled_run: Object,
    // scheduled_tasks: Array,
    base_path: String,
    disk: String,
    log_channel: String,
    user_agent: String,
    versions: Object
})

const {notify} = useNotifications();

const pageTitle = $t("system.system_status");

const getBody = () => {
    let body = `## ${$t("system.describe_your_issue")}\n\n--- \n`;

    body += `## ${$t("system.health")}\n\n`;
    body += `**${$t("system.environment")}**: ${props.env} \n`;
    body += `**${$t("system.debug_mode")}**: ${props.debug ? $t("general.enabled") : $t("general.disabled")} \n`
    body += `**Horizon**: ${$t('system.' + props.horizon_status)} \n`
    body += `**${$t("system.queue_connection")}**: ${props.has_queue_connection ? $t("general.ok") : $t("system.not_ok")} \n`
    body += `**${$t("system.schedule")}**: ${props.last_scheduled_run.message} \n`

    body += `\n`;

    body += `## ${$t("system.technical_details")}:\n\n`;
    body += `**${$t("system.app_directory")}**: ${props.base_path} \n`;
    body += `**${$t("system.upload_media_disk")}**: ${props.disk} \n`;
    body += `**${$t("system.log_channel")}**: ${props.log_channel} \n`;
    body += `**${$t("system.user_agent")}**: ${props.user_agent} \n`;
    body += `**PHP**: ${props.versions.php} \n`;
    body += `**Laravel**: ${props.versions.laravel} \n`;
    body += `**Horizon**: ${props.versions.horizon} \n`;
    body += `**Mixpost**: ${props.versions.mixpost} \n`;

    if (props.versions.mixpost_enterprise) {
        body += `**Mixpost Enterprise**: ${props.versions.mixpost_enterprise} \n`;
    }

    return body;
}

const copyToClipboard = () => {
    navigator.clipboard.writeText(getBody())
        .then(() => {
            notify('success', $t('system.info_copied'))
        })
        .catch(() => {
            notify('error', $t('system.error_copy_info'));
        });
}
</script>
<template>
    <Head :title="pageTitle"/>

    <div class="w-full max-w-6xl mx-auto row-py">
        <PageHeader :title="pageTitle">
            <PrimaryButton @click="copyToClipboard" size="md">
                <Clipboard class="mr-xs"/>
                {{ $t("system.copy") }}
            </PrimaryButton>
        </PageHeader>

        <div class="mt-lg row-px w-full">
            <Panel>
                <template #title> {{ $t("system.health") }}</template>

                <Table>
                    <template #body>
                        <TableRow :hoverable="true">
                            <TableCell>
                                <Badge :variant="env === 'production' ? 'success' : 'warning'">Environment</Badge>
                            </TableCell>
                            <TableCell>
                                {{ env }}
                            </TableCell>
                        </TableRow>
                        <TableRow :hoverable="true">
                            <TableCell>
                                <Badge :variant="debug ? 'warning' : 'success'">Debug Mode</Badge>
                            </TableCell>
                            <TableCell>
                                {{ debug ? $t("general.enabled") : $t("general.disabled") }}
                            </TableCell>
                        </TableRow>
                        <TableRow :hoverable="true">
                            <TableCell>
                                <Badge
                                    :variant="horizon_status === 'active' ? 'success' : (horizon_status === 'paused' ? 'warning' : 'error')">
                                    Horizon
                                </Badge>
                            </TableCell>
                            <TableCell>
                                 <span v-if="horizon_status === 'active'">
                                    <span class="block"> {{ $t("system.active") }}</span>
                                </span>

                                <span v-if="horizon_status === 'paused'">
                                    <span class="block">{{ $t("system.paused") }}</span>
                                </span>

                                <span v-if="horizon_status === 'inactive'">
                                    <span class="block">{{ $t("system.inactive") }}</span>
                                     <span
                                         v-html="$t('util.read_doc', {'href' : `${$page.props.mixpost.docs_link}/books/mixpost-pro-team/page/installation-as-a-package-in-an-existing-laravel-app#bkmrk-install-horizon`})"></span>
                                </span>
                            </TableCell>
                        </TableRow>
                        <TableRow :hoverable="true">
                            <TableCell>
                                <Badge :variant="has_queue_connection ? 'success'  : 'error'">
                                    {{ $t("system.queue_connection") }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <span
                                    v-if="has_queue_connection">
                                    {{ $t("system.connection_settings_redis_exist") }}</span>
                                <span v-else>
                                     <span class="block">{{ $t('system.no_queue_connection') }}</span>
                                   <span class="block">{{ $t("system.config_connection") }}</span>
                                   <span
                                       v-html="$t('util.read_doc', {'href':`${$page.props.mixpost.docs_link}/books/mixpost-pro-team/page/installation-as-a-package-in-an-existing-laravel-app#bkmrk-install-horizon` })">
                                   </span>
                               </span>
                            </TableCell>
                        </TableRow>
                        <TableRow :hoverable="true">
                            <TableCell>
                                <Badge :variant="last_scheduled_run.variant">{{ $t("system.schedule") }}</Badge>
                            </TableCell>
                            <TableCell>
                                {{ last_scheduled_run.message }}
                            </TableCell>
                        </TableRow>
                    </template>
                </Table>
            </Panel>

            <!--            <Panel class="mt-lg">-->
            <!--                <template #title>Schedule Commands</template>-->

            <!--                <Table>-->
            <!--                    <template #head>-->
            <!--                        <TableRow>-->
            <!--                            <TableCell component="th" scope="col">Schedule</TableCell>-->
            <!--                            <TableCell component="th" scope="col">Command</TableCell>-->
            <!--                        </TableRow>-->
            <!--                    </template>-->
            <!--                    <template #body>-->
            <!--                        <TableRow :hoverable="true">-->
            <!--                            <TableCell>-->
            <!--                                * * * * *-->
            <!--                            </TableCell>-->
            <!--                            <TableCell>-->
            <!--                                mixpost:run-scheduled-posts-->
            <!--                            </TableCell>-->
            <!--                        </TableRow>-->
            <!--                    </template>-->
            <!--                </Table>-->
            <!--            </Panel>-->

            <Panel class="mt-lg">
                <template #title>{{ $t("system.technical_details") }}</template>

                <Table>
                    <template #body>
                        <TableRow :hoverable="true">
                            <TableCell class="font-semibold">
                                {{ $t("system.app_directory") }}
                            </TableCell>
                            <TableCell>
                                {{ base_path }}
                            </TableCell>
                        </TableRow>
                        <TableRow :hoverable="true">
                            <TableCell class="font-semibold">
                                {{ $t("system.upload_media_disk") }}
                            </TableCell>
                            <TableCell>
                                {{ disk }}
                            </TableCell>
                        </TableRow>
                        <TableRow :hoverable="true">
                            <TableCell class="font-semibold">
                                {{ $t("system.log_channel") }}
                            </TableCell>
                            <TableCell>
                                {{ log_channel }}
                            </TableCell>
                        </TableRow>
                        <TableRow :hoverable="true">
                            <TableCell class="font-semibold">
                                {{ $t("system.user_agent") }}
                            </TableCell>
                            <TableCell>
                                {{ user_agent }}
                            </TableCell>
                        </TableRow>
                        <TableRow :hoverable="true">
                            <TableCell class="font-semibold">
                                PHP
                            </TableCell>
                            <TableCell>
                                {{ versions.php }}
                            </TableCell>
                        </TableRow>
                        <TableRow :hoverable="true">
                            <TableCell class="font-semibold">
                                Laravel
                            </TableCell>
                            <TableCell>
                                {{ versions.laravel }}
                            </TableCell>
                        </TableRow>
                        <TableRow :hoverable="true">
                            <TableCell class="font-semibold">
                                Horizon
                            </TableCell>
                            <TableCell>
                                {{ versions.horizon }}
                            </TableCell>
                        </TableRow>
                        <TableRow :hoverable="true">
                            <TableCell class="font-semibold">
                                Mixpost
                            </TableCell>
                            <TableCell>
                                {{ versions.mixpost }}
                            </TableCell>
                        </TableRow>

                        <template v-if="versions.mixpost_enterprise">
                            <TableRow :hoverable="true">
                                <TableCell class="font-semibold">
                                    Mixpost Enterprise
                                </TableCell>
                                <TableCell>
                                    {{ versions.mixpost_enterprise }}
                                </TableCell>
                            </TableRow>
                        </template>
                    </template>
                </Table>
            </Panel>
        </div>
    </div>
</template>
