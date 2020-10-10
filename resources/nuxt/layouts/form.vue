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
        ></Schedule>
        <Universal
            v-else
        ></Universal>

    </div>
</div>
</template>

<script>
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
    methods: {
        openModal() {
            ts('dialog.new').modal("show");
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
    },
    props: {
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
        z-index: 2;
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
