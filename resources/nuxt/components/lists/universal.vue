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
                        @click="theadClick(field)"
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
            <tfoot id="pagination-layout">
                <tr>
                    <td class="pagination" :colspan="fields.length+1">
                        <div
                            class="page-button"
                            :class="{disabled: pagination.now == 0}"
                            @click="changePaginationByMethod('prev')"
                        >
                            <label>&#x2190;</label>
                        </div>
                        <div class="page-button" v-for="page in pagination.showLeft" :key="'page-'+page">
                            <input
                                type="radio"
                                :id="'page-'+page"
                                v-model="pagination.now"
                                :value="page"
                                @click="changePaginationByPage(page)"
                            >
                            <label :for="'page-'+page">{{page+1}}</label>
                        </div>
                        <template v-if="pagination.showMiddle.length>0">
                            <div class="ignored-pages">...</div>
                            <div class="page-button" v-for="page in pagination.showMiddle" :key="'page-'+page">
                                <input
                                    type="radio"
                                    :id="'page-'+page"
                                    name="pagination"
                                    v-model="pagination.now"
                                    :value="page"
                                    @click="changePaginationByPage(page)"
                                >
                                <label :for="'page-'+page">{{page+1}}</label>
                            </div>
                            <div class="ignored-pages">...</div>
                        </template>
                        <template v-else-if="pagination.total > 5">
                            <div class="ignored-pages">...</div>
                        </template>
                        <div class="page-button" v-for="page in pagination.showRight" :key="'page-'+page">
                            <input
                                type="radio"
                                :id="'page-'+page"
                                name="pagination"
                                v-model="pagination.now"
                                :value="page"
                                @click="changePaginationByPage(page)"
                            >
                            <label :for="'page-'+page">{{page+1}}</label>
                        </div>
                        <div
                            class="page-button"
                            :class="{disabled: pagination.now == pagination.total-1 || pagination.total==0}"
                            @click="changePaginationByMethod('next')"
                        >
                            <label>&#x2192;</label>
                        </div>
                    </td>
                </tr>
            </tfoot>
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
                    class="clickable"
                    @click="theadClick(field)"
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
                v-for="(data) in pagination.datas[pagination.now]"
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
        <tfoot id="pagination-layout">
            <tr>
                <td class="pagination" :colspan="fields.length+1">
                    <div
                        class="page-button"
                        :class="{disabled: pagination.now == 0}"
                        @click="changePaginationByMethod('prev')"
                    >
                        <label>&#x2190;</label>
                    </div>
                    <div class="page-button" v-for="page in pagination.showLeft" :key="'page-'+page">
                        <input
                            type="radio"
                            :id="'page-'+page"
                            v-model="pagination.now"
                            :value="page"
                            @click="changePaginationByPage(page)"
                        >
                        <label :for="'page-'+page">{{page+1}}</label>
                    </div>
                    <template v-if="pagination.showMiddle.length>0">
                        <div class="ignored-pages">...</div>
                        <div class="page-button" v-for="page in pagination.showMiddle" :key="'page-'+page">
                            <input
                                type="radio"
                                :id="'page-'+page"
                                name="pagination"
                                v-model="pagination.now"
                                :value="page"
                                @click="changePaginationByPage(page)"
                            >
                            <label :for="'page-'+page">{{page+1}}</label>
                        </div>
                        <div class="ignored-pages">...</div>
                    </template>
                    <template v-else-if="pagination.total > 5">
                        <div class="ignored-pages">...</div>
                    </template>
                    <div class="page-button" v-for="page in pagination.showRight" :key="'page-'+page">
                        <input
                            type="radio"
                            :id="'page-'+page"
                            name="pagination"
                            v-model="pagination.now"
                            :value="page"
                            @click="changePaginationByPage(page)"
                        >
                        <label :for="'page-'+page">{{page+1}}</label>
                    </div>
                    <div
                        class="page-button"
                        :class="{disabled: pagination.now == pagination.total-1 || pagination.total==0}"
                        @click="changePaginationByMethod('next')"
                    >
                        <label>&#x2192;</label>
                    </div>
                </td>
            </tr>
        </tfoot>
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
            listData: [],
            pageData: {
                Title: '',
                fields() {
                    return [];
                },
            },
            selects: {},
            filters: {},
            orders: [],
            pagination: {
                per: 10,
                now: 0,
                total: 0,
                datas: [],
                showLeft: [],
                showMiddle: [],
                showRight: [],
            },
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
        },
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
        async getListDatas(options = {}) {
            const URL = "/api/data/"+this.page;
            let params = DataUtil.deepClone(this.getParams);
            params.filters = this.filters;
            params.orders = this.orders;
            params.pagination = true;

            await API.sendRequest(URL, 'get', params).then(response => {
                this.listData = response.data.datas;
            }).catch(e => {});
            await this.computePagination();

            if((!this.hasHeader && window.contentLoaded === true) || options.unload === true) {
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
            window.globalLoading.loading();
            const name = field.Name;
            const index = this.orders.findIndex(x => x.name == name);
            if(index >= 0) {
                let order = this.orders[index];
                if(order.method == 'ASC') this.orders[index].method = 'DESC';
                else if(order.method == 'DESC') delete this.orders[index];

                this.orders = this.orders.filter(() => true);
            } else {
                const order = {
                    name,
                    method: 'ASC'
                };
                if(field.Options.order !== undefined) {
                    const o = field.Options.order;
                    if(o.name !== undefined) order.name = o.name;
                    if(o.from !== undefined) order.from = o.from
                }
                this.orders.push(order);
            }

            this.getListDatas({unload: true});
        },
        async download(id) {},
        isEmpty(x) {
            return DataUtil.isEmpty(x);
        },
        computePagination() {
            const per = this.pagination.per;
            const datas = this.listData;
            let result = [];
            let point = 0;
            for(let i of this.listData) {
                if(result[point] === undefined) result[point] = [];
                result[point].push(i);
                if(result[point].length === per) point++;
            }

            this.pagination.datas = result;
            this.pagination.total = result.length;
            this.changePaginationByPage(0, true);
        },
        changePaginationByPage(page, forceExecute = false) {
            let pagination = this.pagination;
            if((page != pagination.now && !forceExecute) || forceExecute) {
                let toPut = {
                    showLeft: {
                        start: 0,
                        end: 0
                    },
                    showMiddle: {
                        start: 0,
                        end: -1
                    },
                    showRight: {
                        start: 0,
                        end: -1
                    }
                };

                pagination.now = page;
                if(pagination.total <= 5) {
                    toPut.showLeft.start = 0;
                    toPut.showLeft.end = pagination.total-1;
                } else if(page < 4) {
                    toPut.showLeft.start = 0;
                    toPut.showLeft.end = 4;
                }

                if(page > pagination.total - 5 && pagination.total > 5) {
                    toPut.showRight.start = pagination.total - 5;
                    toPut.showRight.end = pagination.total-1;
                } else if (pagination.total > 5) {
                    toPut.showRight.start = pagination.total-1;
                    toPut.showRight.end = pagination.total-1;
                }

                if(page >= 4 && pagination.total > 5 && page <= pagination.total - 5) {
                    toPut.showMiddle.start = page - 2;
                    toPut.showMiddle.end = page + 2;
                } else if (pagination.total > 5) {
                    let middle = Math.ceil(pagination.total/2);
                    toPut.showMiddle.start = middle - 1;
                    toPut.showMiddle.end = middle + 1;
                }

                for(let i in toPut) {
                    const p = toPut[i];
                    let temp = [];
                    for(let j = p.start; j <= p.end; j++) {
                        temp.push(j);
                    }
                    pagination[i] = temp;
                }
            }
        },
        changePaginationByMethod(method) {
            let to = this.pagination.now;
            if(method === "prev" && to > 0) {
                to--;
            } else if (method === "next" && to < this.pagination.total-1) {
                to++;
            }
            this.changePaginationByPage(to);
        }
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
tfoot#pagination-layout {
    text-align: center;
}
tfoot .pagination div {
    display: inline-block;
}
.page-button.disabled::after {
    content: "";
    width: 100%;
    height: 100%;
    display: block;
    position: absolute;
    top: 0px;
    left: 0px;
    z-index: 2;
    background-color: rgba(255, 255, 255, 0.589);
}
.page-button {
    display: inline-block;
    font-size: 1rem;
    line-height: 17px;
    margin-right: .15em;
}
.page-button input[type='radio'] {
    display: none;
}
.page-button label {
    font-size: 1em;
    width: 1.75em;
    height: 1.75em;
    padding: 0.28em;
    border: 1px solid #ccc;
    border-radius: 20px;
    background-color: #f1f1f1;
}
.page-button input:checked ~ label {
    background-color: #70eec4 !important;
}
@media(hover: hover) and (pointer: fine) {
    .page-button label:hover {
        cursor: pointer;
        background-color: #d9d9d9;
    }
    .page-button input:checked ~ label:hover {
        background-color: #4bdbab !important;
    }
}
/* checkbox and radio :not(checked) active */
.page-button label:active {
    background-color: #92fcd9 !important;
}
/* checkbox, toggle and radio checked active */
.page-button input:checked ~ label:active {
    background-color: #40b890 !important;
}
</style>
