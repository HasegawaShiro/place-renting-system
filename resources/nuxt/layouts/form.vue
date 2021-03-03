<template>
<div class="nchu form">
    <i
        class="circular add icon"
        :hidden.prop="!isLogin || !showAddMutation"
        @click="openModal()"
    ></i>
    <div class="ts modals dimmer">
        <Schedule
            v-if="formMode == 'schedule'"
            :form-data="formData"
            ref="form-modal"
            @save="onModalSave($event)"
        ></Schedule>
        <Universal
            v-else
            :page="page"
            :form-name="formName"
            :warning="warning"
            ref="form-modal"
            @save="onModalSave($event)"
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
    },
    components: {
        Universal,
        Schedule,
    },
    computed: {
        isLogin() {
            return this.$parent.isLogin;
        },
    },
    methods: {
        async openModal(mode = 'add', defaultData = {}) {
            window.globalLoading.loading();
            this.dataChanged = false;
            await this.$refs['form-modal'][mode](defaultData);
            window.globalLoading.unloading();
            let formName = DataUtil.isEmpty(this.formName) ? this.page : this.formName;
            ts(`dialog.form#form-${formName}`).modal({
                onDeny() {
                    return confirm(CONSTANTS.messages["cancel-confirmation"]);
                },
                onApprove() {
                    return false;
                },
            }).modal("show");
        },
        closeModal() {
            let formName = DataUtil.isEmpty(this.formName) ? this.page : this.formName;
            ts(`dialog.form#form-${formName}`).modal("hide");
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
            let URL = `/api/data/${event.name}`;
            let pass = true;
            let before = {
                pass: true,
                data: {},
                message: null,
            };
            if(event.method == 'put') URL += `/${event.id}`;

            if(typeof window.$page.$refs.content.beforeSave == "function"){
                before = await window.$page.$refs.content.beforeSave(event.input, method);
                if(before.pass === false) {
                    pass = false;
                    window.mainLayout.showSnackbar('error', before.message);
                } else {
                    event.input.options = before.data;
                };
            }

            if(pass){
                this.$refs["form-modal"].config.saving = true;
                let options = {};
                if(typeof event.options === "object") options = event.options;
                options.onlyData = true;
                await API.sendRequest(URL, event.method, event.input, options).then(async response => {
                    this.$parent.showSnackbar("success", response.messages);
                    await window.$page.$refs['content'].getListDatas();
                    this.closeModal();
                    this.$emit("saved");
                }).catch(e => {
                    try {
                        this.$parent.showSnackbar("error", e.response.data.messages);
                    } catch (error) {
                        let msg = DataUtil.getMessage('unknown-error')+DataUtil.getMessage('contact-maintenance');
                        this.$parent.showSnackbar("error", msg);
                    }
                });
            }

            this.$refs["form-modal"].config.saving = false;
        },
    },
    props: {
        'page': {
            type: String,
        },
        'form-name': {
            type: String,
            default() {
                return null;
            }
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
        "warning": {
            type: String,
            default() {
                return "";
            }
        }
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
