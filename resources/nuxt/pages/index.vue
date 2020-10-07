<template>
<MainLayout
    :has-form="true"
>
    <Calendar
        slot="content"
    ></Calendar>
</MainLayout>
</template>

<script>
import MainLayout from '../layouts/main.vue'
import Calendar from '../components/calendar/calendar.vue'

export default {
    data() {
        return {
        };
    },
    mounted() {
        console.log(this);
    },
    components: {
        MainLayout,
        Calendar,
    },
    methods: {

    }
}
</script>

<style>

</style>
