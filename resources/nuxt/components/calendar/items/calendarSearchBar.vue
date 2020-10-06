<template>
    <div class="ts separated stackable dropdowns">
        <template
            v-for="(items, key) in datas"
        >
            <div class="divider" :key="`${key}-divider`">{{CONSTANT['select-name'][key]}}：</div>
            <select class="ts basic tiny dropdown" :key="`${key}-dropdown`">
                <option
                    v-for="(item, itemIndex) in items"
                    :value="item.value"
                    :key="`${itemIndex}-option`"
                >{{item.text}}</option>
            </select>
        </template>
    </div>
</template>

<script>
export default {
    data(){
        return {
            CONSTANT:{
                'select-name':{
                    type: '借用型態',
                    util: '承辦單位',
                    user: '承辦人'
                },
            },
        };
    },
    props:{
        datas: {
            type: Object,
            default() {
                return {
                    type:[],
                    util:[],
                    user:[]
                }
            }
        }
    },
}
</script>
