<template>
    <table
        class="ts celled fixed compact table mode"
        :class="tableClass"
    >
        <thead>
            <tr class="list-header">
                <th></th>
                <th
                    v-for="field in pageData.fields()"
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
                            @click="deleteData(data[dataKey])"
                        ><i class="trash icon"></i></button>
                    </div>
                </td>
                <td
                    v-for="field in pageData.fields()"
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

export default {
    data() {
        return {
            listData: {},
            pageData: {
                fields() {
                    return [];
                },
            },
            selects: {},
            filters: {},
        };
    },
    computed: {
        dataKey() {
            return this.page+"_id";
        },
        form() {
            return window.mainLayout.$refs.form;
        }
    },
    mounted() {
        this.pageData = PageUtil.getPageData(this.page);
        this.selects = window.globalSelects;
        this.getListDatas();
        // console.log(this.pageData.fields());
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
    },
    methods: {
        async getListDatas() {
            const URL = "/api/data/"+this.page;
            let params = DataUtil.deepClone(this.params);

            await API.sendRequest(URL, 'get', params).then(response => {
                this.listData = response.data.datas;
            }).catch(e => {});

            window.globalLoading.unloading();
        },
        async viewData(data) {
            this.form.openModal('view',data);
        },
        async editData(data) {
            this.form.openModal('edit',data);
        },
        async deleteData(id) {
            window.globalLoading.loading();
            await API.sendRequest(`/api/data/${this.page}/${id}`,'delete').then(async (response) => {
                /* function deleteSchedule(source, id) {
                    const toDelete = source.findIndex(x => x.schedule_id == id);
                    if(toDelete != -1) delete source[toDelete];
                    return source.filter(() => true);
                }; */
                window.mainLayout.showSnackbar("success", response.data.messages);
                await this.getListDatas();
            }).catch(e => {
                if(!DataUtil.isEmpty(e.response.data.messages)) {
                    window.mainLayout.showSnackbar("error", e.response.data.messages);
                }
            });
            window.globalLoading.unloading();
        },
        isEmpty(x) {
            return DataUtil.isEmpty(x);
        },
    },
}
</script>

<style>
.list-header {
}
.list-header th {
    background-color: rgb(8, 138, 120) !important;
    color: #fff !important;
    font-size: 1.2em;
    text-align: center !important;
}
</style>
