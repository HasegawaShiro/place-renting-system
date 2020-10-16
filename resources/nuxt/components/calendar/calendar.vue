<template>
    <div class="nchu calendar">
        <div class="ts grid">
            <div class="header">
                <span class="title">{{CONSTANTS.HEADER_TEXT.title}}</span>
                <span class="mode buttons">
                    <div class="ts buttons">
                        <button
                            class="ts tiny button"
                            id="month-button"
                            :class="{active:config.mode === 'month'}"
                            @click="config.mode = 'month'"
                        >{{CONSTANTS['MODE_BUTTON'].month}}</button>
                        <button
                            class="ts tiny button"
                            id="week-button"
                            :class="{active:config.mode === 'week'}"
                            @click="config.mode = 'week'"
                        >{{CONSTANTS['MODE_BUTTON'].week}}</button>
                        <button
                            class="ts tiny button"
                            id="list-button"
                            :class="{active:config.mode === 'list'}"
                            @click="config.mode = 'list'"
                        >{{CONSTANTS['MODE_BUTTON'].list}}</button>
                    </div>
                </span>
            </div>
            <div class="main">
                <table
                    class="ts fixed eight column table mode"
                    :class="config.mode"
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
                                <i
                                    v-if="config.mode === 'month'"
                                    class="month day button"
                                    @click="toggleMonthSelector()"
                                >
                                    {{calendar.Year}}
                                    {{calendar.Month}}
                                </i>
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
                                >
                                    2020-01-01
                                    ~
                                    2020-12-31
                                </i>
                                <MonthSelector v-if="config.mode === 'month'" :modal-open="monthSelectorOpen"></MonthSelector>
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
                                @click="dayCellClick(dateObj.dateText)"
                            >
                                <h2
                                    :class="{holiday: dateObj.holiday}"
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
                                        @click=""
                                    >
                                        <i class="ellipsis vertical icon"></i>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else-if="config.mode === 'week'">
                        <tr
                            v-for="(text, id) in placeOfWeek"
                            :key="'week-place-'+id"
                        >
                            <td class="colorful place cell">{{text}}</td>
                            <td
                                class="day cell"
                                v-for="(schedules, day) in schedulesByWeekWithPlace(id)"
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
                                    <i class="ellipsis vertical icon"></i>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
// import calendarMonthMode from "./items/calendarMonthMode.vue";
import CONSTANTS from '../../constants.js';
import Calendar from '../../classes/calendar.js';
import MonthSelector from './items/month-selector.vue';
import API from '../../api.js';
import DataUtil from '../../utils/DataUtil.js';

export default {
    data(){
        return {
            CONSTANTS: CONSTANTS.calendar,
            config: {
                mode: 'month',
            },
            headerColspan: {
                month: 7,
                week: 8,
                list: 7
            },
            selects: {
                type: CONSTANTS.calendar.selects.types,
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
            calendar: new Calendar(),
            schedules: [],
            monthSelectorOpen: false,
        };
    },
    async mounted() {
        this.selects.user = await API.getReferenceSelect("user");
        this.selects.place = await API.getReferenceSelect("place");
        this.selects.util = await API.getReferenceSelect("util");
        this.getListDatas();
    },
    props:{},
    computed:{
        placeOfWeek() {
            if(DataUtil.isEmpty(this.filters.place)){
                return this.selects.place;
            }else{
                let result = {};
                result[this.filters.place] = this.selects.place[this.filters.place];
                return result;
            }
        },
        isLogin() {
            return this.$parent.isLogin;
        }
    },
    methods:{
        schedulesByWeek() {
            let result = [[], [], [], [], [], [], []];
            for(let day of this.calendar.Dates[this.calendar.Week]){
                let dateText = DataUtil.formatDateInput(day.date);
                result[day.date.getDay()] = this.schedules.filter(x => {
                    let toShow = true;
                    if(x.schedule_date != dateText) toShow = false;
                    for(let f in this.filters){
                        if(f != 'type' && !DataUtil.isEmpty(this.filters[f]) && x[`${f}_id`] != this.filters[f]){
                            toShow = false;
                        }else if(f == 'type' && !DataUtil.isEmpty(this.filters[f]) && x.schedule_type != this.filters[f]){
                            toShow = false;
                        }
                    }
                    return toShow;
                });
            }
            return result;
        },
        schedulesByDay() {
            let result = {};
            for(let week of this.calendar.Dates) {
                for(let day of week) {
                    let dateText = DataUtil.formatDateInput(day.date);
                    result[dateText] = this.schedules.filter(x => {
                        let toShow = true;
                        if(x.schedule_date != dateText) toShow = false;
                        for(let f in this.filters) {
                            if(f != 'type' && !DataUtil.isEmpty(this.filters[f]) && x[`${f}_id`] != this.filters[f]) {
                                toShow = false;
                            }else if(f == 'type' && !DataUtil.isEmpty(this.filters[f]) && x.schedule_type != this.filters[f]){
                                toShow = false;
                            }
                        }
                        return toShow;
                    }).sort((a,b) => {
                        let dateA = a.date;
                        let dateB = b.date;
                        let timeA = a.schedule_from.split(":");
                        let timeB = b.schedule_from.split(":");
                        dateA.setHours(timeA[0], timeA[1]);
                        dateB.setHours(timeB[0], timeB[1]);
                        if(dateA.getTime() < dateB.getTime()) {
                            return -1;
                        }else if(dateA.getTime() == dateB.getTime()){
                            if(a.title < b.title) {
                                return -1;
                            }else if(a.title == b.title) {
                                return 0;
                            }else {
                                return 1;
                            }
                        }else{
                            return 1;
                        }
                    });
                }
            }

            return result;
        },
        schedulesByWeekWithPlace(place_id) {
            let byWeek = this.schedulesByWeek();
            console.log(place_id,byWeek)
            let result = [[], [], [], [], [], [], []];
            for(let day in byWeek) {
                for(let sd of byWeek[day]) {
                    if(sd.place_id == place_id) result[day].push(sd);
                }
            }
            console.log(result)
            return result;
        },
        calendarChange(method = null, ...params) {
            if(method !== null) this.calendar[method](...params);

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
        toggleMonthSelector() {
            this.monthSelectorOpen = !this.monthSelectorOpen;
        },
        getListDatas() {
            API.sendRequest('/api/get/schedule').then(response => {
                let temp = DataUtil.deepClone(response.data.datas);
                for(let sd in temp){
                    let dateSplit = temp[sd].schedule_date.split("-");
                    let date = new Date(dateSplit[0], parseInt(dateSplit[1])-1, dateSplit[2]);
                    temp[sd].date = date;
                }
                this.schedules = temp;
            }).catch(e => {});
        },
        countDayCellSchedule(schedules) {
            if(schedules.length >= 2) {
                return 2;
            }else{
                return 1;
            }
        },
        dayCellClick(dateText) {
            if(this.schedulesByDay()[dateText].length <= 0 && this.$parent.isLogin) {
                this.$parent.$refs["form"].openModal({schedule_date: dateText});
            }
        },
        scheduleClick(dateText, id = 0) {
            let schedule = this.schedulesByDay()[dateText];
            this.$parent.$parent.$refs["schedule-events"].openModal({
                schedules: schedule,
                showDate: schedule[0].date,
                active_id: id,
            });
        }
    },
    components:{
        MonthSelector,
    },
}
</script>

<style>
/* public */
.nchu.calendar, .nchu.calendar>div {
    margin-top: .5em;
}
.nchu.calendar table {
    border-collapse:collapse;
    border: 1px solid rgba(204, 204, 204, 0.24) !important;
    margin-bottom: .5em !important;
}
.nchu.calendar div.header {
    color: #000;
    text-align: center;
    margin-top: .3em;
    margin-bottom: .5em;
    font-size: 1.5em;
    padding-bottom: .25em;
}
.nchu.calendar div.header .mode.buttons {
    position: absolute;
    top: -0.5em;
    right: .5em;
    font-size: 10px;
}
.nchu.calendar div.main,  .nchu.calendar div.header{
    width: 100%;
    margin-left: 1.5em;
    margin-right: 1.5em;
}
.nchu.calendar div.main .colorful {
    background-color: rgb(8, 138, 120);
    color: #fff;
    text-align: center;
    font-size: 22px;
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
    border-color: rgb(22, 129, 22) !important;
    background-color:  rgba(56, 216, 56, 0.527);
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
        font-size: 1.6em;
        margin-left: .5em;
        margin-right: .5em;
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
    .nchu.calendar div.main .day.cell.clickable:not(.different):hover{
        cursor: pointer;
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
