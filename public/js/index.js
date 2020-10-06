import calendar from './vue/components/calendar/calendar.vue';

// console.log(calendarComponent);
document.querySelector("#menu-button").addEventListener("click", function(){
    ts('.left.sidebar').sidebar('toggle');
});

let indexVue = new Vue({
    el: '#main',
    data() {
        return {};
    },
    components: {
        calendar,
    },
});
