<template>
    <div
        id="report-content"
    >
        <div id="report-title">{{pageData.Title}}</div>
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
            <div class="field">
                <label>產出格式</label>
                <select class="ts fluid basic dropdown" v-model="input.type">
                    <option
                        v-for="(text,type) in types"
                        :key="type"
                        :value="type"
                    >{{text}}</option>
                </select>
            </div>
        </form>
        <div class="ts relaxed separated right floated buttons" id="report-buttons">
            <button
                class="ts negative button"
                @click="initFields"
            >重置</button>
            <button class="ts positive button">送出</button>
        </div>
    </div>
</template>

<script>
import CONSTANTS from '../../constants.js';
import PageUtil from '../../utils/PageUtil.js';
import DataUtil from '../../utils/DataUtil.js';

export default {
    data() {
        return {
            CONSTANTS: CONSTANTS[this.page],
            COMMON: CONSTANTS.common,
            pageData: {},
            fields: [],
            filters: {},
            input: {},
            types: {
                pdf: 'PDF',
                xlsx: 'Excel'
            },
            initInput: {
                type: 'pdf',
            },
        };
    },
    async mounted() {
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
        this.pageData = PageUtil.getPageData(this.page);

        await this.initFields();
        window.mainLayout.contentLoaded();

        this.$emit("mounted");
    },
    methods: {
        async initFields() {
            this.input = {...this.initInput};
            this.fields = [];
            for await(let field of this.pageData.fields().sort((a,b) => {return a.formOrder - b.formOrder})) {
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
            return field.Options.required
        },
        isFieldDisabled(field) {
            const options = field.Options;
            let disabled = options.readOnly

            if(!DataUtil.isEmpty(options.filterBy)) {
                const f = options.filterBy;
                if(DataUtil.isEmpty(this.input[f.foreignField])) disabled = true;
            }

            return disabled
        },
    },
    props: {
        page: String,
    },
}
</script>

<style>
#report-content {
    margin: 1.5em;
    padding: 0px 15%;
}
#report-title {
    text-align: center;
    font-size: 1.5em;
    margin-bottom: 1.5em;
}
#report-buttons {
    margin-top: 1.5em;
}
</style>
