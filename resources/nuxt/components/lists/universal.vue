<template>
    <div
        v-if="hasHeader"
        class="list-content"
    >
        <div class="list-title">{{pageData.Title}}</div>
        <table
            class="ts celled fixed compact table mode"
            :class="tableClass"
        >
            <thead>
                <tr class="list-header">
                    <th></th>
                    <th
                        v-for="field in fields"
                        :key="'column-head-'+field.Name"
                        class="clickable"
                        @click="theadClick(field.Name)"
                    >
                        {{field.Text}}
                        <template v-if="orders.findIndex(x => x.name == field.Name) >= 0">
                            <i
                                class="icon caret up"
                                v-if="orders.find(x => x.name == field.Name).method == 'ASC'"
                            ></i>
                            <i
                                class="icon caret down"
                                v-else-if="orders.find(x => x.name == field.Name).method == 'DESC'"
                            ></i>
                            {{orders.findIndex(x => x.name == field.Name)+1}}
                        </template>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(data) in listData"
                    :key="'data-'+data[dataKey]"
                >
                    <td>
                        <div class="ts tiny stackable icon buttons">
                            <button
                                v-if="showButtonsMutation.includes('view')"
                                class="ts button"
                                @click="viewData(data)"
                            ><i class="eye icon"></i></button>
                            <button
                                v-if="showButtonsMutation.includes('edit')"
                                class="ts info button"
                                :class="{disabled:data.editable === false}"
                                @click="editData(data)"
                            ><i class="write icon"></i></button>
                            <button
                                v-if="showButtonsMutation.includes('delete')"
                                class="ts negative button"
                                :class="{disabled: data.deletable === false}"
                                @click="deleteData(data[dataKey], data)"
                            ><i class="trash icon"></i></button>
                        </div>
                    </td>
                    <td
                        v-for="field in fields"
                        :key="'column-'+field.Name"
                    >
                        <template
                            v-if="field.Type == 'boolean'"
                        >
                            <template
                                v-if="data[field.Name] && !isEmpty(field.Options.trueText)"
                            >
                                {{field.Options.trueText}}
                            </template>
                            <template
                                v-else-if="!data[field.Name] && !isEmpty(field.Options.falseText)"
                            >
                                {{field.Options.falseText}}
                            </template>
                        </template>
                        <template
                            v-else-if="field.Type == 'select'"
                        >
                            <template
                                v-if="typeof field.Options.selectOptions == 'string'"
                            >
                                {{selects[field.Options.selectOptions][data[field.Name]]}}
                            </template>
                            <template
                                v-else-if="typeof field.Options.selectOptions == 'object'"
                            >
                                {{field.Options.selectOptions[data[field.Name]]}}
                            </template>
                        </template>
                        <template
                            v-else-if="field.Type == 'file'"
                        >
                            <i class="large download icon" @click="download(data[dataKey])"></i>
                        </template>
                        <template
                            v-else
                        >{{data[field.Name]}}</template>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <table
        class="ts celled fixed compact table mode"
        :class="tableClass"
        v-else
    >
        <thead>
            <tr class="list-header">
                <th></th>
                <th
                    v-for="field in fields"
                    :key="'column-'+field.Name"
                >
                    {{field.Text}}
                </th>
            </tr>
        </thead>
        <tbody>
            <tr
                v-for="(data) in listData"
                :key="'data-'+data[dataKey]"
            >
                <td>
                    <div class="ts tiny stackable icon buttons">
                        <button
                            class="ts button"
                            @click="viewData(data)"
                        ><i class="eye icon"></i></button>
                        <button
                            class="ts info button"
                            :class="{disabled:data.editable === false}"
                            @click="editData(data)"
                        ><i class="write icon"></i></button>
                        <button
                            class="ts negative button"
                            :class="{disabled: data.deletable === false}"
                            @click="deleteData(data[dataKey], data)"
                        ><i class="trash icon"></i></button>
                    </div>
                </td>
                <td
                    v-for="field in fields"
                    :key="'column-'+field.Name"
                >
                    <template
                        v-if="field.Type == 'boolean'"
                    >
                        <template
                            v-if="data[field.Name] && !isEmpty(field.Options.trueText)"
                        >
                            {{field.Options.trueText}}
                        </template>
                        <template
                            v-else-if="!data[field.Name] && !isEmpty(field.Options.falseText)"
                        >
                            {{field.Options.falseText}}
                        </template>
                    </template>
                    <template
                        v-else-if="field.Type == 'select'"
                    >
                        <template
                            v-if="typeof field.Options.selectOptions == 'string'"
                        >
                            {{selects[field.Options.selectOptions][data[field.Name]]}}
                        </template>
                        <template
                            v-else-if="typeof field.Options.selectOptions == 'object'"
                        >
                            {{field.Options.selectOptions[data[field.Name]]}}
                        </template>
                    </template>
                    <template
                        v-else
                    >{{data[field.Name]}}</template>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script>
import API from '../../api.js';
import DataUtil from '../../utils/DataUtil.js';
import PageUtil from '../../utils/PageUtil.js';
import CONSTANTS from '../../constants.js';

export default {
    data() {
        return {
            listData: {},
            pageData: {
                Title: '',
                fields() {
                    return [];
                },
            },
            selects: {},
            filters: {},
            orders: [],
            showButtonsMutation: ['view', 'edit', 'delete'],
        };
    },
    computed: {
        dataKey() {
            return this.page+"_id";
        },
        form() {
            return window.mainLayout.$refs.form;
        },
        fields() {
            let result = [];
            for(let field of this.pageData.fields().sort((a,b) => {return a.listOrder - b.listOrder})) {
                if(field.Options.showOnList) {
                    result.push(field);
                }
            }

            return result;
        }
    },
    async mounted() {
        this.pageData = await PageUtil.getPageData(this.page);
        for(let field of this.pageData.fields().filter(x => x.Type == 'select')) {
            if(typeof field.Options.selectOptions == 'string'){
                this.selects[field.Options.selectOptions] = await API.getReferenceSelect(field.Options.selectOptions, {showDisabled: true});
            }
        }

        if(window.$page.$refs.content.filters != undefined) {
            this.filters = window.$page.$refs.content.filters;
        }
        if(window.$page.$refs.content.orders != undefined) {
            this.orders = window.$page.$refs.content.orders;
        }
        await this.getListDatas();
        if(this.hasHeader) {
            window.mainLayout.contentLoaded();
        }

        this.$emit("mounted");
        this.showButtonsMutation = this.showButtons;
        if(!window.mainLayout.isLogin) this.showButtonsMutation = ['view'];
    },
    props: {
        "table-class": {
            type: [Object, Array],
            default() {
                return [];
            },
        },
        "page": {
            type: String
        },
        "get-params": {
            type: Object,
            default() {
                return {};
            }
        },
        "has-header": {
            type: Boolean,
            default() {
                return true;
            }
        },
        "show-buttons": {
            type: Array,
            default() {
                return ['view', 'edit', 'delete'];
            }
        },
    },
    methods: {
        async getListDatas() {
            const URL = "/api/data/"+this.page;
            let params = DataUtil.deepClone(this.getParams);
            params.filters = this.filters;
            params.orders = this.orders;

            await API.sendRequest(URL, 'get', params).then(response => {
                this.listData = response.data.datas;
            }).catch(e => {});

            if(!this.hasHeader && window.contentLoaded === true) {
                window.globalLoading.unloading();
            }
        },
        async viewData(data) {
            this.form.openModal('view',data);
        },
        async editData(data) {
            this.form.openModal('edit',data);
        },
        async deleteData(id, data) {
            let requestData = {};
            let before = {
                pass: true,
                data: {},
                message: null,
            };

            if(typeof window.$page.$refs.content.beforeDelete == 'function') {
                before = await window.$page.$refs.content.beforeDelete(data);
                if(before.pass === false) {
                    window.mainLayout.showSnackbar("error", before.message);
                } else {
                    requestData = before.data;
                };
            } else {
                before.pass = confirm(CONSTANTS.messages["delete-confirmation"]);
            }

            if(before.pass){
                window.globalLoading.loading();
                await API.sendRequest(`/api/data/${this.page}/${id}`,'delete', requestData).then(async (response) => {
                    window.mainLayout.showSnackbar("success", response.data.messages);
                    await this.getListDatas();
                }).catch(e => {
                    try {
                        if(!DataUtil.isEmpty(e.response.data.messages)) {
                            window.mainLayout.showSnackbar("error", e.response.data.messages);
                        }
                    } catch (error) {
                        window.mainLayout.showSnackbar("error",['unknown-error', 'contact-maintenance']);
                    }

                });
            } else {
                if(!DataUtil.isEmpty(before.message)) window.mainLayout.showSnackbar("error", before.message);
            }

            window.globalLoading.unloading();
        },
        theadClick(field) {
            console.log(field)
            const index = this.orders.findIndex(x => x.name == field);
            if(index >= 0) {
                let order = this.orders[index];
                console.log(order)
                if(order.method == 'ASC') this.orders[index].method = 'DESC';
                else if(order.method == 'DESC') delete this.orders[index];

                this.orders = this.orders.filter(() => true);
            } else {
                const order = {
                    name: field,
                    method: 'ASC'
                };
                this.orders.push(order);
            }
        },
        async download(id) {},
        isEmpty(x) {
            return DataUtil.isEmpty(x);
        },
    },
}
</script>

<style>
.list-content {
    margin: 1.5em;
}
.list-title {
    text-align: center;
    font-size: 1.5em;
}
thead .list-header th {
    background-color: rgb(8, 138, 120) !important;
    color: #fff !important;
    font-size: 1.2em;
    text-align: center !important;
}

@media(hover: hover) and (pointer: fine) {
    i.download:hover {
        color:  rgb(8, 138, 120);
        cursor: pointer;
    }
    thead .list-header th.clickable:hover {
        cursor: pointer;
        background-color: rgb(31, 153, 137) !important;
    }
}
i.download:active {
    color:  rgb(9, 114, 100);
}
thead .list-header th.clickable:active {
    background-color: rgb(12, 112, 99) !important;
}
</style>
