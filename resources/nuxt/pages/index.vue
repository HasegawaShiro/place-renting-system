<template>
<MainLayout
    page="schedule"
    :has-form="true"
    form-mode="schedule"
    :form-data="dataForForm"
    ref="layout"
>
    <ScheduleEvents
        ref="schedule-events"
        slot="other"
    ></ScheduleEvents>
    <Calendar
        slot="content"
        ref="content"
        @mounted="calendarMounted"
        @change="calendarChange"
    ></Calendar>
</MainLayout>
</template>

<script>
import MainLayout from '../layouts/main.vue';
import Calendar from '../components/calendar/calendar.vue';
import ScheduleEvents from '../components/calendar/items/schedule-events.vue';

export default {
    middleware: 'csrf',
    data() {
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
        ScheduleEvents,
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
