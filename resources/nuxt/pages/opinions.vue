<template>
  <MainLayout
    page="opinion"
    form-mode="universal"
    :has-form="true"
    :show-buttons="showButtons"
    :show-add="false"
    ref="layout"
    @mounted="mainLayoutMounted()"
  >
    <List
        ref="content"
        slot="content"
        page="opinion"
    ></List>
  </MainLayout>
</template>

<script>
import API from '../api.js';
import CONSTANTS from '../constants.js';
import MainLayout from '../layouts/main.vue';
import List from '../components/lists/universal.vue';

export default {
    data() {
        return {
        };
    },
    mounted() {
        window.$page = this;
    },
    computed: {
    },
    methods: {
        mainLayoutMounted() {
            this.$refs.layout.$refs.form.showAddMutation = false;
        },
    },
    components: {
        MainLayout,
        List,
    },
    middleware: 'admin',
}
</script>

<style>

</style>
