<template>
  <MainLayout
    page="announcement"
    form-mode="universal"
    :has-form="true"
    :show-buttons="showButtons"
    ref="layout"
    @mounted="mainLayoutMounted"
  >
    <List
        ref="content"
        slot="content"
        page="announcement"
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
            user: {},
        };
    },
    mounted() {
        window.$page = this;
    },
    computed: {
        showButtons() {
            let result = ['view', 'edit', 'delete'];
            if(this.user.id !== 1) result = ['view'];

            return result;
        },
    },
    methods: {
        mainLayoutMounted() {
            this.user = this.$store.state.userStore.user;
        },
    },
    components: {
        MainLayout,
        List,
    },
}
</script>

<style>

</style>
