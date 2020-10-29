<template>
<div class="nchu form">
    <i
        class="circular add icon"
        :hidden.prop="!showAddMutation"
        @click="openModal()"
    ></i>
    <div class="ts modals dimmer">
        <Schedule
            v-if="formMode == 'schedule'"
            :form-data="formData"
            :ref="'form-modal'"
            @save="onModalSave($event)"
        ></Schedule>
        <Universal
            v-else
        ></Universal>

    </div>
</div>
</template>

<script>
import API from '../api.js';
import CONSTANTS from '../constants.js';
import DataUtil from '../utils/DataUtil.js';
import Schedule from '../components/forms/schedule.vue';
import Universal from '../components/forms/universal.vue';

export default {
    data() {
        return {
            showAddMutation: true,
        };
    },
    mounted() {
        this.showAddMutation = this.showAdd;
        console.log(this)
    },
    components: {
        Universal,
        Schedule,
    },
    computed: {
    },
    methods: {
        async openModal(defaultData = {}) {
            console.log(window.globalLoading);
            window.globalLoading.loading();
            this.dataChanged = false;
            await this.$refs['form-modal'].add(defaultData);
            window.globalLoading.unloading();
            ts('dialog.new').modal({
                onDeny() {
                    return confirm(CONSTANTS.messages["cancel-confirmation"]);
                },
                onApprove() {
                    return false;
                },
            }).modal("show");
            // this.toHideAdd();
        },
        closeModal() {
            ts('dialog.new').modal("hide");
            // this.toShowAdd();
        },
        toShowAdd() {
            this.showAddMutation = true;
        },
        toHideAdd() {
            this.showAddMutation = false;
        },
        onModalCancel() {
            confirm(CONSTANTS.messages["cancel-confirmation"]);
        },
        async onModalSave(event) {
            API.sendRequest(`/api/post/${event.name}`, 'post', event.input, {onlyData: true}).then(async response => {
                this.$parent.showSnackbar("success", response.messages);
                await window.mainLayout.$parent.$refs['content'].getListDatas();
                this.closeModal();
            }).catch(e => {
                try {
                    this.$parent.showSnackbar("error", e.response.data.messages);
                } catch (error) {
                    let msg = DataUtil.getMessage('unknown-error')+DataUtil.getMessage('contact-maintenance');
                    this.$parent.showSnackbar("error", msg);
                }
            });
        },
    },
    props: {
        'page': {
            type: String,
        },
        'form-mode': {
            type: String,
            default() {
                return "universal";
            },
        },
        'show-add': {
            type: Boolean,
            default() {
                return true;
            }
        },
        'form-data': {
            type: Object
        },
    },
    watch: {
        formData(newVal, oldVal){
            this.formData = newVal;
        }
    },
}
</script>

<style>
    .nchu.form .add {
        position: fixed;
        right: 1.5em;
        bottom: 1.5em;
        font-size: 2.5em;
        z-index: 5;
    }
    .nchu.form .add {
        background-color: rgb(8, 188, 135) !important;
        box-shadow: rgba(0, 0, 0, 0.2) 0;
    }
    .nchu.form .add:before {
        color:  #fff;
    }

    /*  */
    .nchu.form .modals dialog {
        border: 1px solid rgb(8, 138, 120);
    }
    .nchu.form .modals .new.form .header {
        color: #fff;
        background-color: rgb(8, 138, 120);
    }

    @media only screen and (max-width:767px){
        .nchu.form .add {
            position: fixed;
            right: 0.5em;
            bottom: 0.5em;
        }
    }
    @media(hover: hover) and (pointer: fine) {
        .nchu.form .add:hover {
            background-color:  rgb(27, 158, 141) !important;
            cursor: pointer;
        }
    }
    .nchu.form .add:active {
        background-color:  rgb(7, 99, 86) !important;
        cursor: pointer;
    }
</style>
