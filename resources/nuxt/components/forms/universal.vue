<template>
    <dialog
        class="ts tiny modal form"
        :id="parseFormName"
        :class="{closable: config.mode == 'view'}"
    >
        <div class="header">
            {{CONSTANTS.FORM_TEXT.title[config.mode]}}
        </div>
        <div class="content">
            <form class="ts horizontal form">
                <template v-for="field in fields" >
                    <div
                        :key="`field-${field.Name}`"
                        class="field"
                    >
                        <label>
                            <sup v-if="isFieldRequired(field)" class="required">*</sup>
                            {{field.Text}}
                        </label>
                        <input
                            v-if="field.Type === 'file'"
                            v-bind="{disabled: isFieldDisabled(field)}"
                            :type="field.Type"
                            @change="fileOnChange($event, field)"
                        />
                        <input
                            v-else-if="isInputField(field)"
                            v-model="input[field.Name]"
                            v-bind="{disabled: isFieldDisabled(field)}"
                            :type="field.Type"
                        />
                        <select
                            v-else-if="field.Type === 'select'"
                            v-bind="{disabled: isFieldDisabled(field)}"
                            v-model="input[field.Name]"
                            @change="inputOnChange(field)"
                        >
                            <template
                                v-if="!isEmpty(field.Options.filterBy)"
                            >
                                <option
                                    v-for="(opt, value) in filters[field.Name]"
                                    :key="`opt-${value}`"
                                    :value="value"
                                >{{opt}}</option>
                            </template>
                            <template
                                v-else-if="typeof field.Options.selectOptions == 'string'"
                            >
                                <option
                                    v-for="(opt, value) in selects[field.Options.selectOptions]"
                                    :key="`opt-${value}`"
                                    :value="value"
                                >
                                    {{opt}}
                                </option>
                            </template>
                            <template
                                v-else-if="typeof field.Options.selectOptions == 'object'"
                            >
                                <option
                                    v-for="(opt, value) in field.Options.selectOptions"
                                    :key="`opt-${value}`"
                                    :value="value"
                                >
                                    {{opt}}
                                </option>
                            </template>
                        </select>
                        <div
                            class="ts toggle checkbox"
                            :class="{disabled: config.mode == 'view'}"
                            v-else-if="field.Type === 'boolean'"
                        >
                            <input
                                type="checkbox"
                                :id="`chk-${field.Name}`"
                                v-model="input[field.Name]"
                                v-bind="{disabled: isFieldDisabled(field)}"
                            >
                            <label :for="`chk-${field.Name}`"></label>
                        </div>
                        <textarea
                            rows="5"
                            v-model="input[field.Name]"
                            v-else-if="field.Type === 'textarea'"
                            v-bind="{disabled: isFieldDisabled(field)}"
                        ></textarea>
                    </div>
                    <div
                        v-if="field.Options.confirmation && config.mode !== 'view'"
                        class="field"
                        :key="`confirmation-${field.Name}`"
                    >
                        <label>
                            <sup v-if="isFieldRequired(field)" class="required">*</sup>
                            確認{{field.Text}}
                        </label>
                        <input
                            v-if="isInputField(field)"
                            v-model="input[`${field.Name}_confirmation`]"
                            :type="field.Type"
                        />
                        <select
                            v-else-if="field.Type === 'select'"
                            v-model="input[`${field.Name}_confirmation`]"
                        >
                            <option
                                v-for="(opt, value) in selects[field.Options.selectOptions]"
                                :key="`opt-${value}`"
                                :value="value"
                            >
                                {{opt}}
                            </option>
                        </select>
                        <div
                            class="ts toggle checkbox"
                            :class="{disabled: config.mode == 'view'}"
                            v-else-if="field.Type === 'boolean'"
                        >
                            <input
                                type="checkbox"
                                :id="`chk-${field.Name}`"
                                v-model="input[`${field.Name}_confirmation`]"
                            >
                            <label :for="`chk-${field.Name}`"></label>
                        </div>

                    </div>
                </template>
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
import CONSTANTS from '../../constants.js';
import PageUtil from '../../utils/PageUtil.js';
import DataUtil from '../../utils/DataUtil.js';
import API from '../../api.js';

export default {
    data() {
        return {
            CONSTANTS: CONSTANTS[this.page],
            COMMON: CONSTANTS.common,
            config: {
                mode: 'add',
                id: 0,
                saving: false,
            },
            fields: [],
            pageData: {},
            input: {},
            orginData: {},
            selects: {},
            filters: {},
            requestOptions: {},
            inputFormData: new FormData(),
        }
    },
    mounted() {
        this.pageData = PageUtil.getPageData(this.page);
        for(let field of this.pageData.fields().filter(x => x.Type == 'select')) {
            if(!DataUtil.isEmpty(field.Options.filter)) {
                this.$set(this.filters, field.Options.filter, {});
                // this.filters[field.Options.filter] = {};
            } else if(typeof field.Options.selectOptions == 'string') {
                this.$set(this.selects, field.Options.selectOptions, {});
                // this.selects[field.Options.selectOptions] = {};
            }
        }
    },
    props: {
        page: String,
        "form-name": String,
    },
    computed: {
        parseFormName() {
            if(DataUtil.isEmpty(this.formName)){
                return `form-${this.page}`;
            } else {
                return `form-${this.formName}`;
            }
        },
    },
    methods: {
        async add(defaultData = {}) {
            let dateToPut = new Date();

            this.pageData = PageUtil.getPageData(this.page);
            this.config.mode = 'add';
            this.config.id = 0;

            this.input = {};
            this.fields = [];
            for(let field of this.pageData.fields().sort((a,b) => {return a.formOrder - b.formOrder})) {
                let toShow = field.Options.showOnForm === true;
                if(field.Options.hideOnAdd === true) toShow = false;

                if(toShow) {
                    this.fields.push(field);
                    if(field.Type === 'file') {
                        this.requestOptions.hasFile = true;
                    } else {
                        this.$set(this.input, field.Name, field.Options.default);
                    }
                    if(field.Type === 'select') {
                        if(typeof field.Options.selectOptions === 'string') {
                            const table = field.Options.selectOptions;
                            this.selects[table] = await API.getReferenceSelect(table);
                        }
                    }
                    if(field.Options.confirmation) {
                        this.$set(this.input, `${field.Name}_confirmation`, null);
                    }
                }
            }
        },
        async edit(data) {
            this.config.mode = 'edit';
            this.config.id = data[`${this.page}_id`];

            await this.parseOriginData(data);
        },
        async view(data) {
            this.config.mode = 'view';
            this.config.id = data[`${this.page}_id`];
            await this.parseOriginData(data);
        },
        async parseOriginData(data) {
            this.pageData = PageUtil.getPageData(this.page);

            this.input = {};
            this.fields = [];
            for(let field of this.pageData.fields().sort((a,b) => {return a.formOrder - b.formOrder})) {
                let toShow = field.Options.showOnForm === true;
                if((this.config.mode == 'view' && field.Options.hideOnView === true) || (this.config.mode == 'edit' && field.Options.hideOnEdit === true)) toShow = false;
                if(toShow) {
                    this.fields.push(field);
                    if(field.Type === 'file') {
                        this.requestOptions.hasFile = true;
                    } else {
                        if(DataUtil.isEmpty(data[field.Name])) {
                            this.$set(this.input, field.Name, field.Options.default);
                        } else {
                            this.$set(this.input, field.Name, data[field.Name]);
                        }
                    }

                    if(field.Type === 'select') {
                        if(typeof field.Options.selectOptions === 'string') {
                            const table = field.Options.selectOptions;
                            this.selects[table] = await API.getReferenceSelect(table);
                        }
                    }
                    if(field.Options.confirmation) {
                        this.$set(this.input, `${field.Name}_confirmation`, null);
                    }
                }
            }
        },
        async getOptionsByFilter(field) {
            const filterBy = field.Options.filterBy;
            let result = {};
            let options = {
                filter: {},
            };
            options.filter[field.Options.filterBy.foreignField] = this.input[field.Options.filterBy.foreignField]
            await API.getReferenceSelect(filterBy.from, options).then(response => {
                result = response;
            }).catch(e => {});

            // this.$forceUpdate();
            return result;
        },
        isInputField(field) {
            return [
                'text',
                'password',
                'number',
                'date',
                'time',
                'datetime',
                'file'
            ].includes(field.Type);
        },
        isFieldRequired(field) {
            const options = field.Options;
            const add = this.config.mode === 'add';
            const edit = this.config.mode === 'edit';
            return this.config.mode != 'view' && (options.required || (options.addRequired && add) || (options.editRequired && edit))
        },
        isFieldDisabled(field) {
            const options = field.Options;
            const add = this.config.mode === 'add';
            const edit = this.config.mode === 'edit';
            let disabled = this.config.mode === 'view' || options.readOnly || (!options.editable && edit)

            if(!DataUtil.isEmpty(options.filterBy)) {
                const f = options.filterBy;
                if(DataUtil.isEmpty(this.input[f.foreignField])) disabled = true;
            }

            return disabled
        },
        saveClick() {
            if(['add', 'edit'].includes(this.config.mode)){
                let toSave = DataUtil.deepClone(this.input);
                if(this.requestOptions.hasFile === true) {
                    for(let d in toSave) {
                        this.inputFormData.append(d, toSave[d]);
                    }
                    toSave = this.inputFormData;
                }

                this.$emit("save", {
                    name: this.page,
                    input: toSave,
                    method: this.config.mode == 'add' ? 'post' : 'put',
                    id: this.config.id,
                    options: this.requestOptions,
                });
            }
        },
        cancelClick() {
            this.$emit("cancel");
        },
        isEmpty(...obj) {
            return DataUtil.isEmpty(...obj);
        },
        async inputOnChange(field) {
            if(!DataUtil.isEmpty(field.Options.filter)) {
                const target = this.pageData.fields().find(x => x.Name == field.Options.filter);
                if(!DataUtil.isEmpty(target) && !DataUtil.isEmpty(target.Options.filterBy)) {
                    this.filters[field.Options.filter] = await this.getOptionsByFilter(target);
                }
            }
        },
        fileOnChange($event, field) {
            this.inputFormData.append(field.Name, $event.target.files[0]);
        },
    },
}
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
</style>
