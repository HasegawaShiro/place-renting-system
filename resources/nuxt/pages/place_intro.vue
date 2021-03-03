<template>
    <MainLayout
        :has-form="false"
        ref="layout"
        @mounted="mainLayoutMounted()"
    >
        <div slot="content">
            <div class="ts modals dimmer">
                <dialog id="modal" class="ts closable basic modal">
                    <i class="close icon"></i>
                    <div class="content" style="width:100%">
                        <img :src="imageURL(modalImage)">
                    </div>
                </dialog>
            </div>
            <div class="list-content">
                <div class="list-title">場地教室介紹</div>
                <table
                    class="ts fixed table"
                >
                    <thead>
                        <tr class="list-header">
                            <th
                                v-for="th in thead"
                                :key="'column-head-'+th"
                            >
                                {{th}}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(data, index) in listData"
                            :key="'data-'+index"
                        >
                            <td>
                                {{data.name}}
                            </td>
                            <td style="overflow-wrap: anywhere;">
                                {{data.intro}}
                            </td>
                            <td class="center aligned ">
                                <div
                                    class="imgDiv"
                                    v-for="(img, imgInd) in data.images"
                                    :key="`data-${index}-img${imgInd}}`"
                                    @click="imageClick(img)"
                                >
                                    <div class="imageMask"></div>
                                    <img :src="imageURL(img)"><br>
                                </div>

                            </td>
                            <td class="center aligned">
                                <button
                                    class="ts button"
                                    data-tooltip="展開樓層圖"
                                    @click="imageClick(data.floor)"
                                >{{data.floor}}</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </MainLayout>
</template>

<script>
import MainLayout from '../layouts/main.vue';

export default {
    data() {
        return {
            thead: ['場地名稱','介紹','圖片','樓層'],
            listData: [
                {name:"", intro:"", images:[], floor:""}
            ],
            modalImage: "",
        };
    },
    async mounted() {
        fetch("/place_intro.json")
        .then(response => {
            console.log(response);
            response.json().then(data => {
                this.listData = data;
            });
        }).catch(error => {});
        window.mainLayout.contentLoaded();

        this.$emit("mounted");
    },
    methods: {
        mainLayoutMounted() {
        },
        imageURL(path) {
            return `/images/place_intro/${path}.jpg`
        },
        imageClick(img) {
            this.modalImage = img;
            ts('#modal').modal("show");
        }
    },
    components: {
        MainLayout,
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
img {
    width: 100%;
}
.imageMask {
    width: 100%;
    height: 100%;
    z-index: 5;
    background-color: rgba(255, 255, 255, 0);
    display: block;
    position: absolute;
    left: 0px;
    top: 0px;
    font-size: 2.5em;
}

@media(hover: hover) and (pointer: fine) {
    .imageMask:hover::before {
        cursor: pointer;
        content: '\5c55\958b\5716\7247';
        text-align: center;
        line-height: 800%;
        background-color: rgba(255, 255, 255, 0.562);
        width: 100%;
        height: 100%;
        display: block;
    }
}
</style>

