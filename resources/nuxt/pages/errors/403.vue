<template>
<MainLayout
    :has-form="false"
>
    <div
        class="ts inverted negative segment"
        slot="content"
    >
        您沒有權限探訪此頁面，請確認是否已登入，或登入之用戶組是否正確。
    </div>
</MainLayout>
</template>

<script>
import MainLayout from '../../layouts/main.vue';

export default {
    mounted() {
        window.mainLayout.contentLoaded();
    },
    components: {
        MainLayout,
    },
}
</script>

<style>
    .ts.inverted.negative.segment {
        margin: 2em;
        border-left: solid .35em rgb(146, 52, 52);
    }
</style>
