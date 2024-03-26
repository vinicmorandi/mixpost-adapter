import ConfirmationComponent from "./Confirmation.vue";
import {ref} from "vue";
import {clone} from "lodash";

const Confirmation = {
    install(app) {
        app.component('Confirmation', ConfirmationComponent);

        const initialData = {
            show: false,
            variant: 'warning',
            title: '',
            description: '',
            btnConfirmName: '',
            btnCancelName: '',
            isLoading: false,
            onConfirm: null,
            onCancel: null,
        };

        const data = ref(clone(initialData));

        app.provide('confirmation', () => {
            return {
                data,
                title(value) {
                    data.value.title = value;
                    return this;
                },
                description(value) {
                    data.value.description = value;
                    return this;
                },
                btnConfirmName(value) {
                    data.value.btnConfirmName = value;
                    return this;
                },
                btnCancelName(value) {
                    data.value.btnCancelName = value;
                    return this;
                },
                isLoading(value) {
                    data.value.isLoading = value;
                    return this;
                },
                variant(value) {
                    data.value.variant = value;
                    return this;
                },
                destructive() {
                    data.value.variant = 'danger';
                    data.value.btnConfirmName = ''
                    return this;
                },
                warning() {
                    data.value.variant = 'warning';
                    data.value.btnConfirmName = ''
                    return this;
                },
                reset() {
                    data.value = clone(initialData);
                    return this;
                },
                show() {
                    data.value.show = true;
                },
                close() {
                    data.value.show = false;
                },
                onConfirm(fnc) {
                    data.value.onConfirm = fnc;
                    return this;
                },
                onCancel(fnc) {
                    data.value.onCancel = fnc;
                    return this;
                },
            };
        });
    },
};

export default Confirmation;
