<template>
<MainLayout
    :has-form="false"
>
    <div
        class="ts inverted negative segment"
        slot="content"
    >
        您沒有權限探訪此頁面，請確認是否已登入，或登入之用戶組是否正確。<br>
        將於 {{second}} 秒後跳轉回首頁，或<a id="to-index" @click="redirectToIndex">點擊此直接跳轉</a>
    </div>
</MainLayout>
</template>

<script>
import MainLayout from '../../layouts/main.vue';
import Funtions from '../../functions.js';

export default {
    data() {
        return {
            second: 5,
        }
    },
    async mounted() {
        window.mainLayout.contentLoaded();
        while (this.second > 0) {
            await Funtions.delay(1000);
            this.second--;
        }
        this.redirectToIndex();
    },
    components: {
        MainLayout,
    },
    methods: {
        redirectToIndex() {
            this.$router.push('/');
            // return redirect('/')
        },
    },
}
</script>

<style>
    .ts.inverted.negative.segment {
        margin: 2em;
        border-left: solid .35em rgb(146, 52, 52);
    }
    #to-index {
        color: white;
    }
    @media(hover: hover) and (pointer: fine) {
        #to-index:hover {
            color: rgb(250, 250, 250);
            cursor: pointer;
            text-decoration: underline;
        }
    }
    #to-index:active {
        color: rgb(212, 212, 212);
    }
</style>
