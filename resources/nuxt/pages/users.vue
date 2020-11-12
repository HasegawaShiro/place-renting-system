<template>
  <MainLayout
    page="user"
    form-mode="universal"
    :has-form="true"
    ref="layout"
  >
    <List
        ref="content"
        slot="content"
        page="user"
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
    components: {
        MainLayout,
        List,
    },
    middleware: 'admin',
}
</script>

<style>

</style>
