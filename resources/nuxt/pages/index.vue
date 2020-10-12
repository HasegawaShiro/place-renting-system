<template>
<MainLayout
    :has-form="true"
    form-mode="schedule"
    :form-data="dataForForm"
>
    <Calendar
        slot="content"
        @mounted="calendarMounted"
        @change="calendarChange"
    ></Calendar>
</MainLayout>
</template>

<script>
import MainLayout from '../layouts/main.vue'
import Calendar from '../components/calendar/calendar.vue'

export default {
    middleware: 'csrf',
    data() {
        console.log(this)
        return {
            calendarObj: {},
        };
    },
    mounted() {
        // console.log(this);
    },
    components: {
        MainLayout,
        Calendar,
    },
    methods: {
        calendarMounted(cal) {
            this.calendarObj = cal;
        },
        calendarChange(cal) {
            this.calendarObj = cal;
        },
    },
    computed: {
        dataForForm() {
            return {
                selected_date: this.calendarObj.SelectedDate,
            };
        },
    },
}
</script>

<style>

</style>
