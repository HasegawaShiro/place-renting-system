<template>
    <MainLayout
        page="announcement"
        form-mode="universal"
        :has-form="true"
        :show-buttons="showButtons"
        :show-add="showAdd"
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
        showAdd() {
            let result = false;
            if(this.user.id === 1) result = true;

            return result;
        }
    },
    methods: {
        mainLayoutMounted() {
            this.user = this.$store.state.userStore.user;
            this.$refs.content.showButtonsMutation = this.showButtons;
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
