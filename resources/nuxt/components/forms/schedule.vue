<template>
    <dialog
        class="ts tiny modal form schedule"
        :class="{closable: config.mode == 'view'}"
        id="form-schedule"
    >
        <div class="header">
            {{CONSTANTS.FORM_TEXT.title[config.mode]}}
        </div>
        <div class="content">
            <form class="ts horizontal form">
                <div class="field">
                    <label><sup class="required">*</sup>{{CONSTANTS.FORM_TEXT.schedule_title}}</label>
                    <input
                        type="text"
                        v-model="input.schedule_title"
                        v-bind="{disabled: config.mode == 'view'}"
                    >
                </div>
                <div class="field">
                    <label><sup class="required">*</sup>{{CONSTANTS.FORM_TEXT.schedule_date}}</label>
                    <input
                        type="date"
                        v-model.lazy="input.schedule_date"
                        v-bind="{disabled: config.mode == 'view'}"
                    >
                </div>
                <div class="field">
                    <label>{{CONSTANTS.FORM_TEXT.fullday}}</label>
                    <div class="ts toggle checkbox" :class="{disabled: config.mode == 'view'}">
                        <input type="checkbox" id="fullday" v-model="config.fullday">
                        <label for="fullday"></label>
                    </div>
                </div>
                <template v-if="!config.fullday">
                    <div class="field">
                        <label>{{CONSTANTS.FORM_TEXT.schedule_from}}</label>
                        <input
                            type="time"
                            v-model="input.schedule_from"
                            v-bind="{disabled: config.mode == 'view'}"
                        >
                    </div>
                    <div class="field">
                        <label>{{CONSTANTS.FORM_TEXT.schedule_to}}</label>
                        <input
                            type="time"
                            v-model="input.schedule_to"
                            v-bind="{disabled: config.mode == 'view'}"
                        >
                    </div>
                </template>
                <div class="field">
                    <label>{{CONSTANTS.FORM_TEXT.schedule_repeat}}</label>
                    <div class="ts toggle checkbox" :class="{disabled: config.mode == 'view'}">
                        <input type="checkbox" id="repeat" v-model="input.schedule_repeat">
                        <label for="repeat"></label>
                    </div>
                </div>
                <template v-if="input.schedule_repeat">
                    <div class="field">
                        <label>{{CONSTANTS.FORM_TEXT.schedule_repeat_method}}</label>
                        <div class="ts checkboxes" :class="{disabled: config.mode == 'view'}">
                            <div class="ts radio checkbox">
                                <input
                                    type="radio"
                                    name="repeat-method"
                                    id="keep"
                                    value="keep"
                                    v-model="config.schedule_repeat_method"
                                >
                                <label for="keep">{{CONSTANTS.FORM_TEXT.keep}}</label>
                            </div>
                            <div class="ts radio checkbox">
                                <input
                                    type="radio"
                                    name="repeat-method"
                                    id="cycle"
                                    value="cycle"
                                    v-model="config.schedule_repeat_method"
                                >
                                <label for="cycle">{{CONSTANTS.FORM_TEXT.cycle}}</label>
                            </div>
                        </div>
                        <div
                            class="week-selector"
                            :class="{
                                disabled:
                                    config.schedule_repeat_method!='cycle' ||
                                    config.mode === 'view'
                            }"
                        >
                            <div
                                class="week-button"
                                v-for="(day, key) in DAY_TEXT"
                                :key="'week'+key"
                            >
                                <input
                                    type="checkbox"
                                    :id="'week'+key"
                                    v-model="config.schedule_repeat_days[key]"
                                    true-value="1"
                                    false-value="0"
                                >
                                <label :for="'week'+key">{{day}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label>{{CONSTANTS.FORM_TEXT.schedule_end}}</label>
                        <div class="ts checkboxes" :class="{disabled: config.mode == 'view'}">
                            <div class="ts radio checkbox">
                                <input
                                    type="radio"
                                    id="at"
                                    name="schedule_end"
                                    value="at"
                                    v-model="input.schedule_end"
                                    v-bind="{disabled: config.mode == 'view'}"
                                >
                                <label for="at">{{CONSTANTS.FORM_TEXT.at}}</label>
                                <input
                                    type="date"
                                    class="ts input after checkbox"
                                    v-model="input.schedule_end_at"
                                    v-bind="{disabled: config.mode == 'view'}"
                                >
                            </div>
                            <div class="ts radio checkbox">
                                <input
                                    type="radio"
                                    id="times"
                                    name="schedule_end"
                                    value="times"
                                    v-model="input.schedule_end"
                                    v-bind="{disabled: config.mode == 'view'}"
                                >
                                <label for="times">{{CONSTANTS.FORM_TEXT.repeat}}</label>
                                <input
                                    type="number"
                                    class="ts input after checkbox back word"
                                    v-model="input.schedule_end_times"
                                    v-bind="{disabled: config.mode == 'view'}"
                                >{{CONSTANTS.FORM_TEXT.times}}
                            </div>
                        </div>
                    </div>
                </template>
                <template
                    v-if="config.mode === 'edit' && orginData.schedule_repeat && orginData.repeat_id != null"
                >
                    <div class="field">
                        <label>{{CONSTANTS.messages['repeat-edit']}}</label>
                        <div class="ts checkboxes">
                            <div class="ts radio checkbox">
                                <input
                                    type="radio"
                                    name="repeat-edit"
                                    id="repeat-one"
                                    value="one"
                                    v-model="input.repeat_edit"
                                >
                                <label for="repeat-one">{{CONSTANTS.messages['repeat-one']}}</label>
                            </div>
                            <div class="ts radio checkbox">
                                <input
                                    type="radio"
                                    name="repeat-edit"
                                    id="repeat-all"
                                    value="all"
                                    v-model="input.repeat_edit"
                                >
                                <label for="repeat-all">{{CONSTANTS.messages['repeat-all']}}</label>
                            </div>
                        </div>
                    </div>
                </template>
                <div class="field">
                    <label>
                        <sup class="required">*</sup>
                        {{CONSTANTS.FORM_TEXT.place_id}}
                    </label>
                    <select
                        class="ts basic dropdown"
                        v-model="input.place_id"
                        v-bind="{disabled: config.mode == 'view'}"
                    >
                        <option
                            v-for="(text, value) in selects.places"
                            :key="'place-'+value"
                            :value="value"
                        >{{text}}</option>
                    </select>
                </div>
                <div class="field">
                    <label>
                        <sup class="required">*</sup>
                        {{CONSTANTS.FORM_TEXT.schedule_registrant}}
                    </label>
                    <input
                        type="text"
                        v-model="input.schedule_registrant"
                        v-bind="{disabled: config.mode == 'view'}"
                    >
                </div>
                <div class="field">
                    <label>
                        <sup class="required">*</sup>
                        {{CONSTANTS.FORM_TEXT.schedule_type}}
                    </label>
                    <select
                        class="ts basic dropdown"
                        v-model="input.schedule_type"
                        v-bind="{disabled: config.mode == 'view'}"
                    >
                        <option
                            v-for="(text, value) in selects.types"
                            :key="'place-'+value"
                            :value="value"
                        >{{text}}</option>
                    </select>
                </div>
                <div class="field">
                    <label>{{CONSTANTS.FORM_TEXT.schedule_content}}</label>
                    <textarea
                        rows="5"
                        v-model="input.schedule_content"
                        v-bind="{disabled: config.mode == 'view'}"
                    ></textarea>
                </div>
                <div class="field">
                    <label>{{CONSTANTS.FORM_TEXT.user_id}}</label>
                    <select
                        class="ts disabled basic dropdown"
                        disabled
                        v-model="input.user_id"
                        v-bind="{disabled: config.mode == 'view'}"
                    >
                        <option
                            v-for="(text, value) in selects.users"
                            :key="'user-'+value"
                            :value="value"
                        >{{text}}</option>
                    </select>
                </div>
                <div class="field">
                    <label>{{CONSTANTS.FORM_TEXT.util}}</label>
                    <input class="disabled" type="text" disabled v-model="user.util.util_name">
                </div>
                <div class="field">
                    <label>{{CONSTANTS.FORM_TEXT.phone}}</label>
                    <input class="disabled" type="text" disabled v-model="user.phone">
                </div>
                <div class="field">
                    <label>{{CONSTANTS.FORM_TEXT.mail}}</label>
                    <input class="disabled" type="text" disabled v-model="user.email">
                </div>
                <div class="field">
                    <label>
                        <sup class="required">*</sup>
                        {{CONSTANTS.FORM_TEXT.schedule_contact}}
                    </label>
                    <input
                        type="text"
                        v-model="input.schedule_contact"
                        v-bind="{disabled: config.mode == 'view'}"
                    >
                </div>
                <div class="field">
                    <label>{{CONSTANTS.FORM_TEXT.schedule_url}}</label>
                    <input
                        type="text"
                        v-model="input.schedule_url"
                        v-bind="{disabled: config.mode == 'view'}"
                    >
                </div>
            </form>
        </div>
        <div class="actions" v-if="config.mode != 'view'">
            <button
                class="ts deny button"
                :class="{disabled: config.saving}"
                @click="cancelClick()"
            >
                {{COMMON.TEXT.cancel}}
            </button>
            <button
                class="ts positive button"
                :class="{disabled: config.saving, loading: config.saving}"
                @click="saveClick()"
            >
                {{COMMON.TEXT.save}}
            </button>
        </div>
    </dialog>
</template>

<script>
import CONSTANTS from "../../constants.js";
import DataUtil from '../../utils/DataUtil.js';
import {PageUtil, Form} from '../../utils/PageUtil.js';
import API from '../../api.js';

export default {
    data() {
        return {
            CONSTANTS: CONSTANTS.schedule,
            COMMON: CONSTANTS.common,
            DAY_TEXT: CONSTANTS.calendar.DAY_TEXT,
            config: {
                mode: 'add',
                id: 0,
                saving: false,
            },
            selects: {
                places: {},
                users: {},
                types: CONSTANTS.schedule.selects.types,
            },
            user: {
                util: {},
            },
            input: {},
            orginData: {},
            defaultInput() {
                return {
                    schedule_title: null,
                    schedule_date: null,
                    schedule_from: '00:00',
                    schedule_to: '00:00',
                    schedule_repeat: false,
                    schedule_repeat_days: 127,
                    schedule_end: 'at',
                    schedule_end_at: null,
                    schedule_end_times: 0,
                    place_id: null,
                    schedule_registrant: null,
                    schedule_type: null,
                    schedule_content: null,
                    user_id: null,
                    schedule_contact: null,
                    schedule_url: null,
                    repeat_id: null,
                }
            },
            defaultConfig() {
                return {
                    fullday: false,
                    schedule_repeat_method: 'keep',
                    schedule_repeat_days: ['0', '0', '0', '0', '0', '0', '0'],
                };
            }
        };
    },
    async mounted() {
    },
    props: {
        'form-data': {
            type: Object,
        },
    },
    computed: {
    },
    methods: {
        async add(defaultData = {}) {
            let dateToPut = new Date();

            this.user = this.$store.state.userStore.user;
            this.config.mode = 'add';
            this.config.id = 0;
            this.selects.places = await API.getReferenceSelect("place");
            this.selects.users = await API.getReferenceSelect("user");

            this.input = {};
            for(let k in this.defaultInput()) {
                if(DataUtil.isEmpty(defaultData[k])){
                    this.$set(this.input, k, this.defaultInput()[k]);
                }else{
                    this.$set(this.input, k, defaultData[k]);
                }
            }
            for(let k in this.defaultConfig()) {
                this.$set(this.config, k, this.defaultConfig()[k]);
            }

            this.input.user_id = this.user.id;
        },
        async edit(data) {
            this.config.mode = 'edit';
            this.config.id = data.schedule_id;
            if(data.repeat_id != null && data.schedule_repeat) {}
            await this.parseOriginData(data);
        },
        async view(data) {
            this.config.mode = 'view';
            this.config.id = data.schedule_id;
            await this.parseOriginData(data);
        },
        async parseOriginData(data) {
            this.selects.places = await API.getReferenceSelect("place");
            this.selects.users = await API.getReferenceSelect("user");
            this.user = {
                user_name: data.user_name,
                phone: data.phone,
                email: data.email,
                util: {
                    util_name: data.util_name
                },
            };

            this.input = {};
            for(let k in this.defaultInput()) {
                if(DataUtil.isEmpty(data[k])){
                    this.$set(this.orginData, k, this.defaultInput()[k]);
                    this.$set(this.input, k, this.defaultInput()[k]);
                }else{
                    this.$set(this.orginData, k, data[k]);
                    this.$set(this.input, k, data[k]);
                }
            }
            for(let k in this.defaultConfig()) {
                this.$set(this.config, k, this.defaultConfig()[k]);
            }

            this.config.schedule_repeat_days = DataUtil.decimalToBinary(this.input.schedule_repeat_days, 7).split("");
            if(this.config.schedule_repeat_days.includes("0")) {
                this.config.schedule_repeat_method = 'cycle';
            }
            if(this.input.schedule_from == "00:00" && this.input.schedule_to == "23:59") {
                this.config.fullday = true;
            }
            if(this.config.mode === 'edit' && data.schedule_repeat && data.repeat_id != null) {
                this.input.repeat_edit = "one";
            }
        },
        saveClick() {
            if(['add', 'edit'].includes(this.config.mode)){
                let toSave = DataUtil.deepClone(this.input);
                if(this.config.fullday){
                    toSave.schedule_from = "00:00";
                    toSave.schedule_to = "23:59";
                }

                if(this.config.schedule_repeat_method == "keep"){
                    toSave.schedule_repeat_days = 127;
                }else{
                    let days = "";
                    for(let i = 0; i < 7; i++){
                        days += this.config.schedule_repeat_days[i];
                    }
                    toSave.schedule_repeat_days = parseInt(days,2);
                }

                this.$emit("save", {
                    name: 'schedule',
                    input: toSave,
                    method: this.config.mode == 'add' ? 'post' : 'put',
                    id: this.config.id,
                });
            }
        },
        cancelClick() {
            this.$emit("cancel");
        },
    },
};
</script>

<style>
dialog.form .disabled::before {
    content: "";
    width: 100%;
    height: 100%;
    display: block;
    position: absolute;
    top: 0px;
    left: 0px;
    z-index: 12;
    background-color: rgba(255, 255, 255, 0.5);
}
label .required {
    top: -0.2em;
    font-size: 1.7em;
    color: red;
}
.week-selector {
    position: absolute;
    bottom: -0.15em;
    left: 15em;
}
.week-selector .week-button {
    display: inline-block;
    font-size: 1rem;
    line-height: 17px;
    margin-right: .15em;
}
.week-selector .week-button input[type='checkbox'] {
    display: none;
}
.week-selector .week-button label {
    padding: 0.28em;
    border: 1px solid #ccc;
    border-radius: 20px;
    background-color: #f1f1f1;
}

.week-selector .week-button input:checked ~ label {
    background-color: #70eec4 !important;
}

.input.after.checkbox {
    line-height: 1em;
    padding: .2em;
    top: -0.3em;
    left: .2em;
}
.input.after.checkbox.back.word {
    margin-right: .3em;
}
@media(hover: hover) and (pointer: fine) {
    .week-selector .week-button label:hover {
        cursor: pointer;
        background-color: #d9d9d9;
    }
    .week-selector .week-button input:checked ~ label:hover {
        background-color: #4bdbab !important;
    }
}
/* checkbox and radio :not(checked) active */
.week-selector .week-button label:active {
    background-color: #92fcd9 !important;
}
/* checkbox, toggle and radio checked active */
.week-selector .week-button input:checked ~ label:active {
    background-color: #40b890 !important;
}
@media only screen and (max-width:767px){
    .week-selector {
        left: 5em;
    }
}
</style>
