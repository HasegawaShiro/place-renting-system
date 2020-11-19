<template>
    <div class="nchu calendar">
        <!-- Delete confirmation -->
        <div class="ts modals dimmer">
            <dialog class="ts mini modal delete-confirmation">
                <div class="header">
                    {{CONSTANTS.messages['repeat-delete']}}
                </div>
                <div class="content">
                    <div class="ts checkboxes">
                        <div class="ts radio checkbox">
                            <input
                                type="radio"
                                name="repeat-mode"
                                id="repeat-one"
                                value="one"
                                v-model="config['repeat-mode']"
                            >
                            <label for="repeat-one">{{CONSTANTS.messages['repeat-one']}}</label>
                        </div>
                        <div class="ts radio checkbox">
                            <input
                                type="radio"
                                name="repeat-mode"
                                id="repeat-all"
                                value="all"
                                v-model="config['repeat-mode']"
                            >
                            <label for="repeat-all">{{CONSTANTS.messages['repeat-all']}}</label>
                        </div>
                    </div>
                </div>
                <div class="actions">
                    <button class="ts deny button">
                        取消
                    </button>
                    <button class="ts positive button">
                        確定
                    </button>
                </div>
            </dialog>
        </div>
        <!-- From to selector -->
        <div class="ts modals dimmer" v-if="">
            <dialog class="ts mini modal calendar-seletor from-to-selector">
                <div class="header">
                    {{CONSTANTS.messages['from-to-selector']}}
                </div>
                <div class="content">
                    {{CONSTANTS.messages['from']}}
                    <input type="date" v-model="selectors.from"><br>
                    {{CONSTANTS.messages['to']}}
                    <input type="date" v-model="selectors.to">
                </div>
                <div class="actions">
                    <button class="ts deny button">
                        取消
                    </button>
                    <button class="ts positive button">
                        確定
                    </button>
                </div>
            </dialog>
        </div>
        <div class="ts grid">
            <div class="main-header">
                <span class="title">{{CONSTANTS.HEADER_TEXT.title}}</span>
                <span class="mode buttons">
                    <div class="ts buttons">
                        <button
                            v-for="md in ['month', 'week', 'list']"
                            :key="'mode-button-'+md"
                            class="ts tiny button"
                            id="month-button"
                            :class="{active:config.mode === md}"
                            @click="changeMode(md);"
                        >{{CONSTANTS['MODE_BUTTON'][md]}}</button>
                    </div>
                </span>
            </div>
            <div class="main">
                <table
                    class="ts fixed table mode"
                    :class="[
                        config.mode,
                        {top: config.mode == 'list', attached: config.mode == 'list'}
                    ]"
                >
                    <thead>
                        <tr>
                            <td
                                class="colorful date"
                                :colspan="headerColspan[config.mode]"
                            >
                                <template v-if="config.mode !== 'list'">
                                    <i
                                        class="prev button angle double left icon"
                                        :class="config.mode === 'month' ? 'year' : 'week'"
                                        @click="doubleLeft()"
                                    ></i>
                                    <i
                                        v-if="config.mode === 'month'"
                                        class="prev month button angle left icon"
                                        @click="calendarChange('prevMonth')"
                                    ></i>
                                </template>
                                <template v-if="config.mode === 'month'">
                                    <i
                                        class="month day button"
                                        @click="monthSelectorClick()"
                                    >
                                        {{calendar.Year}}
                                        {{calendar.Month}}
                                    </i>
                                    <div class="ts modals dimmer">
                                        <dialog class="ts mini modal month-selector">
                                            <div class="header">
                                                {{CONSTANTS.messages['month-selector']}}
                                            </div>
                                            <div class="content">
                                                <input type="month">
                                            </div>
                                            <div class="actions">
                                                <button class="ts deny button">
                                                    取消
                                                </button>
                                                <button class="ts positive button">
                                                    確定
                                                </button>
                                            </div>
                                        </dialog>
                                    </div>
                                </template>
                                <i
                                    v-else-if="config.mode === 'week'"
                                    class="week day button"
                                >
                                    {{calendar.getDateToString(calendar.getDatesOfWeek()[0])}}
                                    ~
                                    {{calendar.getDateToString(calendar.getDatesOfWeek()[6])}}
                                </i>
                                <i
                                    v-else-if="config.mode === 'list'"
                                    class="from to button"
                                    @click="fromToSelectorClick"
                                >
                                    {{selectors.from}}
                                    ~
                                    {{selectors.to}}
                                </i>
                                <template v-if="config.mode !== 'list'">
                                    <i
                                        v-if="config.mode === 'month'"
                                        class="next month button angle right icon"
                                        @click="calendarChange('nextMonth')"
                                    ></i>
                                    <i
                                        class="next button angle double right icon"
                                        :class="config.mode === 'month' ? 'year' : 'week'"
                                        @click="doubleRight()"
                                    ></i>
                                </template>
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="colorful search"
                                :colspan="headerColspan[config.mode]"
                            >
                                <div class="ts separated stackable dropdowns">
                                    <template
                                        class="ts basic tiny dropdown"
                                        v-for="(options, name) in selects"
                                    >
                                        <div :key="'divider-'+name" class="divider">
                                            {{CONSTANTS.selects.TEXT[name]}}：
                                        </div>
                                        <select
                                            class="ts basic tiny dropdown"
                                            :key="'select-'+name"
                                            v-model="filters[name]"
                                            @change="getListDatas"
                                        >
                                            <option :value="null"></option>
                                            <option
                                                v-for="(text, value) in options"
                                                :key="name+'-'+value"
                                                :value="value"
                                            >{{text}}</option>
                                        </select>
                                    </template>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="config.mode === 'week'">
                            <th class="colorful day header out" rowspan="2">
                                <em class="sup">{{CONSTANTS.HEADER_TEXT.date}}</em>
                                <em class="sub">{{CONSTANTS.HEADER_TEXT.place}}</em>
                            </th>
                            <th
                                v-for="(span, year) in calendar.getYearOfWeek()"
                                :key="year"
                                class="colorful year header"
                                :colspan="span"
                            >
                                {{year}}
                            </th>
                            <!-- <th class="colorful year header" colspan="4">
                                2020
                            </th>
                            <th class="colorful year header" colspan="3">
                                2021
                            </th> -->
                        </tr>
                        <tr v-if="config.mode === 'month' || config.mode === 'week'">
                            <th
                                class="colorful day header"
                                v-for="(dateObj, day) in calendar.getDatesOfWeek()"
                                :key="'key-'+day"
                            >
                                <div class="date" v-if="config.mode==='week'">
                                    {{dateObj.date.getMonth()+1}}-{{dateObj.date.getDate()}}
                                </div>
                                <div class="day">{{CONSTANTS.DAY_TEXT[day]}}</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody v-if="config.mode === 'month'">
                        <tr
                            v-for="(dates, week) in calendar.Dates"
                            :key="'week-'+week"
                        >
                            <td
                                v-for="dateObj in dates"
                                class="day cell"
                                :class="{
                                    different: dateObj.different,
                                    holiday: dateObj.holiday,
                                    clickable: schedulesByDay()[dateObj.dateText].length <= 0 && isLogin
                                }"
                                :key="dateObj.dateText"
                                :id="dateObj.dateText"
                            >
                                <h2
                                    :class="{holiday: dateObj.holiday}"
                                    @click="dayCellClick(dateObj.dateText)"
                                >{{dateObj.date.getDate()}}</h2>
                                <div
                                    class="schedules"
                                    v-if="schedulesByDay()[dateObj.dateText].length > 0"
                                >
                                    <div
                                        v-for="i in countDayCellSchedule(schedulesByDay()[dateObj.dateText])"
                                        :key="dateObj.dateText+'-'+i"
                                        :class="schedulesByDay()[dateObj.dateText][i-1].schedule_type"
                                        @click="scheduleClick(dateObj.dateText, schedulesByDay()[dateObj.dateText][i-1].schedule_id)"
                                    >
                                        <div class="tablet or large device only text">
                                            <i>
                                                {{schedulesByDay()[dateObj.dateText][i-1].schedule_from}}
                                                -
                                                {{schedulesByDay()[dateObj.dateText][i-1].schedule_to}}
                                            </i><br>
                                            <i>
                                                {{selects.place[schedulesByDay()[dateObj.dateText][i-1].place_id]}}
                                            </i>
                                        </div>
                                        <div class="mobile only text">
                                            <i>{{schedulesByDay()[dateObj.dateText][i-1].schedule_from}}</i>
                                        </div>
                                    </div>
                                    <div
                                        v-if="schedulesByDay()[dateObj.dateText].length > 2"
                                        class="schedule overflow"
                                        @click="scheduleClick(dateObj.dateText)"
                                    >
                                        <i class="ellipsis vertical icon"></i>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else-if="config.mode === 'week'">
                        <tr
                            v-for="(place, id) in placeOfWeek"
                            :key="'week-place-'+id"
                        >
                            <td class="colorful place cell">{{place.text}}</td>
                            <td
                                class="day cell"
                                v-for="(schedules, day) in place.schedules"
                                :key="'week-day-'+day"
                                :class="{clickable: schedules.length <= 0 && isLogin}"
                            >
                                <div class="schedules" v-if="schedules.length > 0">
                                    <template
                                        v-for="index in countDayCellSchedule(schedules)"
                                    >
                                        <div
                                            :class="schedules[index-1].schedule_type"
                                            :key="schedules[index-1].schedule_date+'-'+index"
                                            @click="scheduleClick(schedules[index-1].schedule_date, schedules[index-1].schedule_id)"
                                        >
                                            <div class="tablet or large device only text">
                                                <i>
                                                    {{schedules[index-1].schedule_from}}
                                                    -
                                                    {{schedules[index-1].schedule_to}}
                                                </i>
                                                <br>
                                                <i>{{selects.user[schedules[index-1].user_id]}}</i>
                                            </div>
                                            <div class="mobile only text">
                                                <i>{{schedules[index-1].schedule_from}}</i>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <div class="schedule overflow" v-if="schedules.length > 2">
                                    <i class="ellipsis vertical icon" @click="schedules[index-1].schedule_date"></i>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <List
                    v-if="config.mode === 'list'"
                    :table-class="['bottom','attached']"
                    :has-header="false"
                    :show-buttons="listShowButtons"
                    page="schedule"
                    ref="list"
                ></List>
            </div>
            <div class="sixteen wide column footer"></div>
        </div>
    </div>
</template>

<script>
import CONSTANTS from '../../constants.js';
import Calendar from '../../classes/calendar.js';
import List from '../lists/universal.vue';
import API from '../../api.js';
import functions from '../../functions.js';
import DataUtil from '../../utils/DataUtil.js';

export default {
    data(){
        return {
            CONSTANTS: CONSTANTS.calendar,
            config: {
                mode: 'month',
                'repeat-mode': 'one',
            },
            headerColspan: {
                month: 7,
                week: 8,
                list: 7
            },
            selects: {
                type: CONSTANTS.common.selects.type,
                util: [],
                user: [],
                place: [],
            },
            filters: {
                user: null,
                place: null,
                util: null,
                type : null
            },
            selectors: {
                month: '',
                week: '',
                from: '',
                to: '',
            },
            calendar: new Calendar(),
            schedules: [],
            monthSelectorOpen: false,
        };
    },
    async mounted() {
        this.selects.user = await API.getReferenceSelect("user", {showDisabled: true});
        this.selects.place = await API.getReferenceSelect("place", {showDisabled: true});
        this.selects.util = await API.getReferenceSelect("util", {showDisabled: true});

        this.filters.schedule_date_from = DataUtil.formatDateInput(this.calendar.getStartOfCalendar());
        this.filters.schedule_date_to = DataUtil.formatDateInput(this.calendar.getEndOfCalendar());
        this.selectors.from = this.filters.schedule_date_from;
        this.selectors.to = this.filters.schedule_date_to;
        await this.getListDatas();

        const that = this;
        let screen = window.matchMedia('(max-width: 767px)');
        screen.addEventListener("change", (e) => {
            if(e.matches && that.config.mode == 'week') {
                that.config.mode = 'month';
            }
        });


        window.mainLayout.contentLoaded();
    },
    props:{},
    computed:{
        placeOfWeek() {
            let result = {};
            if(DataUtil.isEmpty(this.filters.place)){
                if(!DataUtil.isEmpty(this.selects.place)){
                    result = DataUtil.deepClone(this.selects.place);
                }
            } else {
                result[this.filters.place] = DataUtil.deepClone(this.selects.place[this.filters.place]);
            }

            for(let id in result) {
                const schedules = this.schedulesByWeekWithPlace(id);
                /* let isEmpty = DataUtil.isEmpty(...schedules);
                if(!isEmpty) {
                    result[id] = {
                        text: result[id],
                        schedules
                    }
                } else {
                    delete result[id];
                } */
                result[id] = {
                    text: result[id],
                    schedules
                }
            }

            return result;
        },
        isLogin() {
            return this.$parent.isLogin;
        },
        listShowButtons() {
            let user = this.$store.state.userStore.user;
            let result = ['view'];
            if(!DataUtil.isEmpty(user) && user.id !== 0) result = ['view', 'edit', 'delete'];

            return result;
        },
    },
    methods:{
        schedulesByWeek() {
            let result = [[], [], [], [], [], [], []];
            for(let day of this.calendar.Dates[this.calendar.Week]){
                let dateText = DataUtil.formatDateInput(day.date);
                result[day.date.getDay()] = this.schedules.filter(x => {
                    let toShow = true;
                    if(x.schedule_date != dateText) toShow = false;

                    return toShow;
                });
            }

            const temp = result.shift();
            result.push(temp);
            return result;
        },
        schedulesByDay() {
            let result = {};

            for(let week of this.calendar.Dates) {
                for(let day of week) {
                    let dateText = DataUtil.formatDateInput(day.date);
                    result[dateText] = this.schedules.filter(x => {
                        return x.schedule_date == dateText;
                    });
                }
            }

            return result;
        },
        schedulesByWeekWithPlace(place_id) {
            let byWeek = this.schedulesByWeek();
            let result = [[], [], [], [], [], [], []];
            for(let day in byWeek) {
                for(let sd of byWeek[day]) {
                    if(sd.place_id == place_id) result[day].push(sd);
                }
            }
            return result;
        },
        async calendarChange(method = null, ...params) {
            if(method !== null) await this.calendar[method](...params);

            this.filters.schedule_date_from = DataUtil.formatDateInput(this.calendar.getStartOfCalendar());
            this.filters.schedule_date_to = DataUtil.formatDateInput(this.calendar.getEndOfCalendar());
            this.selectors.from = this.filters.schedule_date_from;
            this.selectors.to = this.filters.schedule_date_to;
            this.getListDatas();
            this.$forceUpdate();
            this.$emit("change", this.calendar);
        },
        doubleLeft() {
            if(this.config.mode === 'month'){
                this.calendar.prevYear();
            }else if(this.config.mode === 'week'){
                this.calendar.prevWeek();
            }
            this.calendarChange();
        },
        doubleRight() {
            if(this.config.mode === 'month'){
                this.calendar.nextYear();
            }else if(this.config.mode === 'week'){
                this.calendar.nextWeek();
            }
            this.calendarChange();
        },
        async getListDatas() {
            if(window.globalLoading != undefined) window.globalLoading.loading();
            let filters = {};
            for(let f in this.filters) {
                if(!['type', 'schedule_date_from', 'schedule_date_to'].includes(f)) {
                    if(!DataUtil.isEmpty(this.filters[f])) filters[`${f}_id`] = this.filters[f];
                } else if(f == 'type' && !DataUtil.isEmpty(this.filters[f])) {
                    filters.schedule_type = this.filters[f];
                } else {
                    filters[f] = this.filters[f];
                }
            }

            if(!DataUtil.isEmpty(this.$refs["list"])) this.$refs.list.filters = filters;

            await API.sendRequest('/api/data/schedule', 'get', {filters}).then(async response => {
                let temp = DataUtil.deepClone(response.data.datas);
                for(let sd in temp) {
                    let dateSplit = temp[sd].schedule_date.split("-");
                    let date = new Date(dateSplit[0], parseInt(dateSplit[1])-1, dateSplit[2]);
                    temp[sd].date = date;
                }

                this.schedules = temp;
                if(!DataUtil.isEmpty(this.$refs["list"])) await this.$refs["list"].getListDatas();
                return temp;
            }).catch(e => {});
            if(window.globalLoading != undefined) window.globalLoading.unloading();
        },
        countDayCellSchedule(schedules) {
            if(schedules.length >= 2) {
                return 2;
            }else{
                return 1;
            }
        },
        dayCellClick(dateText) {
            if(this.$parent.isLogin) {
                this.$parent.$refs["form"].openModal('add',{schedule_date: dateText});
            }
        },
        scheduleClick(dateText, id = 0) {
            let schedule = this.schedulesByDay()[dateText];
            const date = DataUtil.dateTextToDateObject(dateText);
            window.$page.$refs["schedule-events"].openModal({
                schedules: schedule,
                showDate: date,
                active_id: id,
            });
        },
        monthSelectorClick() {

        },
        fromToSelectorClick() {
            const that = this;
            let oldVal = DataUtil.deepClone(that.selectors);
            const fromToSelector = new Promise((resolve, reject) => {
                ts('.from-to-selector').modal({
                    onApprove: function() {
                        resolve();
                    },
                    onDeny: function() {
                        reject();
                    }
                }).modal('show');
            });

            fromToSelector.then(() => {
                const newVal = DataUtil.deepClone(that.selectors);
                if(newVal.from != oldVal.from || newVal.to != oldVal.to) {
                    that.filters.schedule_date_from = newVal.from;
                    that.filters.schedule_date_to = newVal.to;
                    that.getListDatas();
                }
            }).catch(() => {
                that.selectors = oldVal;
            });
        },
        async changeMode(mode) {
            if(this.config.mode != mode){
                window.globalLoading.loading();
                this.config.mode = mode
                if(mode != 'list'){
                    await functions.delay(500);
                    window.globalLoading.unloading();
                }
            }
        },
        async beforeDelete(data) {
            let result = {
                pass: true,
                data: {},
                message: null,
            };
            this.config['repeat-mode'] = 'one';

            if(data.schedule_repeat) {
                const deleteComfirmation = new Promise((resolve, reject) => {
                    ts('.delete-confirmation').modal({
                        onDeny: function() {
                            reject();
                        },
                        onApprove: function() {
                            resolve();
                        }
                    }).modal("show");
                });
                await deleteComfirmation.then(x => {
                    result.data['repeat-mode'] = this.config['repeat-mode'];
                }).catch(x => {
                    result.pass = false;
                });
            } else {
                result.pass = await confirm(CONSTANTS.messages["delete-confirmation"]);
            }

            return result;
        },
    },
    components:{
        List,
    },
}
</script>

<style>
/* public */
.nchu.calendar {
    user-select: none;
}
.nchu.calendar, .nchu.calendar>div {
    margin-top: .5em;
}
.nchu.calendar table {
    border-collapse:collapse;
    border: 1px solid rgba(204, 204, 204, 0.24) !important;
    /* margin-bottom: .5em !important; */
}
.nchu.calendar div.main-header {
    color: #000;
    text-align: center;
    margin-top: .3em;
    margin-bottom: .5em;
    font-size: 1.5em;
    padding-bottom: .25em;
}
.nchu.calendar div.main-header .mode.buttons {
    position: absolute;
    top: -0.5em;
    right: .5em;
    font-size: 10px;
}
.nchu.calendar div.main,  .nchu.calendar div.main-header{
    width: 100%;
    margin-left: 1.5em;
    margin-right: 1.5em;
}
.nchu.calendar div.main td.colorful, .nchu.calendar div.main th.colorful {
    background-color: rgb(8, 138, 120);
    color: #fff;
    text-align: center;
    font-size: 22px;
}
.nchu.calendar .calendar-seletor .content {
    text-align: center;
}
.nchu.calendar div.main td.search {
    font-size: .8em;
}
.nchu.calendar div.main td.search .divider {
    font-size: 1.5em;
}
.nchu.calendar div.main .day.header, .nchu.calendar div.main .year.header {
    border: 1px solid rgba(255, 255, 255, 0.315) !important;
}
.nchu.calendar div.main .month .prev.button, .nchu.calendar div.main .month .next.button, .nchu.calendar div.main .date .prev.button, .nchu.calendar div.main .date .next.button {
    vertical-align: middle;
    font-size: 23px;
    margin-left: .1em;
    margin-right: .1em;
}
.nchu.calendar div.main .month.mode thead .colorful.day.header {
    line-height: .25em;
}
.nchu.calendar div.main .month.mode .day.cell, .nchu.calendar div.main .week.mode .day.cell {
    padding: .7em;
    border: 1px solid #ccc !important;
    height: 10em;
    vertical-align: top;
}
/* disabled prev and next month */
.nchu.calendar div.main .month.mode .different.day.cell::before {
    content: "";
    width: 100%;
    height: 100%;
    display: block;
    position: absolute;
    top: 0px;
    left: 0px;
    z-index: 2;
    background-color: rgba(255, 255, 255, 0.589);
}
.nchu.calendar div.main .month.mode .day.cell h2 {
    margin-bottom: .02em;
    padding-left: .2em;
    border-radius: 3px;
}
.nchu.calendar div.main .month.mode .day.cell h2.holiday {
    color: red;
}
.nchu.calendar div.main .month.mode .day.cell .schedules>div:not(.overflow), .nchu.calendar div.main .week.mode .day.cell .schedules>div:not(.overflow){
    padding-left: .05em;
    border-left: 3px solid;
    border-radius: 3px;
    overflow: hidden;
    text-overflow: ellipsis;
    height: 3em;
    white-space: nowrap;
    margin-bottom: 0.5em;
    width: 100%;
}
.nchu.calendar div.main .month.mode .day.cell .schedules div .text, .nchu.calendar div.main .week.mode .day.cell .schedules div .text {
    border: none;
}
.nchu.calendar div.main .month.mode .day.cell .schedule.overflow, .nchu.calendar div.main .week.mode .day.cell .schedule.overflow {
    padding: 2px;
    width: 100%;
    text-align: center;
    font-size: 18px;
}
.nchu.calendar div.main .month.mode .day.cell .schedules .conference, .nchu.calendar div.main .week.mode .day.cell .schedules .conference {
    border-color: rgb(190, 81, 7) !important;
    background-color: rgba(240, 132, 61, 0.692);
}
.nchu.calendar div.main .month.mode .day.cell .schedules .activity, .nchu.calendar div.main .week.mode .day.cell .schedules .activity {
    border-color: rgb(212, 194, 32) !important;
    background-color: rgba(255, 233, 110, 0.863);
}
.nchu.calendar div.main .month.mode .day.cell .schedules .lesson, .nchu.calendar div.main .week.mode .day.cell .schedules .lesson {
    border-color: rgb(25, 57, 117) !important;
    background-color: rgba(54, 99, 182, 0.466);
}
.nchu.calendar div.main .month.mode .day.cell .schedules .exam, .nchu.calendar div.main .week.mode .day.cell .schedules .exam {
    border-color: rgb(150, 21, 155) !important;
    background-color: rgba(217, 41, 223, 0.534);
}
.nchu.calendar div.main .month.mode .day.cell .schedules .other, .nchu.calendar div.main .week.mode .day.cell .schedules .other {
    border-color: rgb(45, 171, 202) !important;
    background-color: rgba(61, 216, 255, 0.589);
}

/* week mode */
.nchu.calendar div.main .week.mode thead .colorful.year.header {
    line-height: 1.8em;
    font-size: 1.8em;
    padding-top: .15em;
    padding-bottom: .15em;
}
.nchu.calendar div.main .week.mode thead .colorful.day.header {
    line-height: normal;
    padding-top: .25em;
    padding-bottom: .25em;
}
.nchu.calendar div.main .week.mode thead .colorful.day.out{
    border-top:40px #D6D3D6 solid;
    width:0px;
    height:0px;
    border-left:80px #BDBABD solid;
    position:relative;
}
.nchu.calendar div.main .week.mode thead .colorful.day em{
    font-style:normal;
    display:block;
    position:absolute;
    width:50%;
    font-size: 1.1em;
}
.nchu.calendar div.main .week.mode thead .colorful.day em.sup{
    top: .3em;
    right: .3em;
}
.nchu.calendar div.main .week.mode thead .colorful.day em.sub{
    bottom: .3em;
    left: .3em;
}

.nchu.calendar div.footer {
    line-height: 2.5em;
    margin-top: 2em;
    vertical-align: middle;
    text-align: center;
}

@media only screen and (max-width:767px){
    /* public */
    .nchu.calendar div.header {
        text-align: left;
        font-size: 1.2em;
    }
    .nchu.calendar div.main .colorful {
        font-size: 1.2em;
    }
    .nchu.calendar div.main thead .colorful.day.header {
        font-size: 0.8em;
    }
    .nchu.calendar div.header .mode.buttons #week-button{
        display: none;
    }
    .nchu.calendar div.main .month .prev.button, .nchu.calendar div.main .month .next.button, .nchu.calendar div.main .date .prev.button, .nchu.calendar div.main .date .next.button {
        font-size: 1.3em;
        margin-left: .35em;
        margin-right: .35em;
    }
    .nchu.calendar div.main td.search {
        font-size: .5em;
    }
    .nchu.calendar div.main td.search .stackable.dropdowns .divider {
        font-size: 1.5em;
        display: flex;
    }
    .nchu.calendar div.main td.search .stackable.dropdowns .dropdown {
        width: 100%;
        display: block;
        font-size: 1.5em;
    }
    .nchu.calendar div.main td.search select{
        font-size: .7em;
        line-height: normal;
        padding-top: .3em;
        padding-bottom: .3em;
    }
    /* month mode */
    .nchu.calendar div.main .month.mode .day.cell {
        padding: .3em;
        height: 5em;
    }
    .nchu.calendar div.main .month.mode .day.cell .schedules>div:not(.overflow), .nchu.calendar div.main .week.mode .day.cell .schedules>div:not(.overflow){
        vertical-align: middle;
        height: 1.5em;
        margin-bottom: 0.3em;
    }
    .nchu.calendar div.main .month.mode .day.cell h2 {
        font-size: 1em;
        display: block;
    }
    /* week mode */
    .nchu.calendar div.main .week.mode thead .colorful.day em{
        font-style:normal;
        display:block;
        position:absolute;
        width:50%;
        font-size: 1em !important;
    }
    .nchu.calendar div.main .week.mode thead .colorful.year.header {
        line-height: .8em;
        font-size: .8em;
        padding-top: .5em;
        padding-bottom: .5em;
    }
    .nchu.calendar div.main .week.mode thead .colorful.day.header {
        font-size: .8em;
        line-height: normal;
        padding-top: .15em;
        padding-bottom: .15em;
    }
}

@media(hover: hover) and (pointer: fine) {
    .nchu.calendar div.main .month i.button:hover, .nchu.calendar div.main .date i.button:hover {
        background-color: rgba(255, 255, 255, 0.322);
        cursor: pointer;
    }
    .nchu.calendar div.main .day.cell:not(.different) h2:hover{
        cursor: pointer;
        background-color: rgba(85, 255, 161, 0.452)
    }
    .nchu.calendar div.main .month.mode .day.cell .schedules div:hover::after, .nchu.calendar div.main .week.mode .day.cell .schedules div:hover::after {
        content: "";
        position: absolute;
        left: 0px;
        top: 0px;
        z-index: 1;
        width: 100%;
        height: 100%;
        cursor: pointer;
        background-color: rgba(255, 255, 255, 0.295)
    }
    .nchu.calendar div.main .month.mode .day.cell .schedule.overflow:hover, .nchu.calendar div.main .week.mode .day.cell .schedule.overflow:hover {
        background-color: rgba(146, 146, 146, 0.466);
        cursor: pointer;
        border-radius: 3px;
    }
}
.nchu.calendar div.main .month i.button:active, .nchu.calendar div.main .date i.button:active {
    background-color: rgba(255, 255, 255, 0.596);
    cursor: pointer;
}
.nchu.calendar div.main .day.cell:not(.different) h2:active{
    background-color: rgba(72, 216, 137, 0.658)
}
.nchu.calendar div.main .month.mode .day.cell .schedules div:active::after, .nchu.calendar div.main .week.mode .day.cell .schedules div:active::after {
    content: "";
    position: absolute;
    left: 0px;
    top: 0px;
    z-index: 1;
    width: 100%;
    height: 100%;
    cursor: pointer;
    background-color: rgba(255, 255, 255, 0.075)
}
.nchu.calendar div.main .month.mode .day.cell .schedule.overflow:active, .nchu.calendar div.main .week.mode .day.cell .schedule.overflow:active {
    background-color: rgba(107, 107, 107, 0.582);
    cursor: pointer;
    border-radius: 3px;
}

</style>
