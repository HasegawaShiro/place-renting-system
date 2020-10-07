<template>
    <div class="nchu calendar">
        <div class="ts grid">
            <div class="header">
                <span class="title">場地使用查詢</span>
                <span class="mode buttons">
                    <div class="ts buttons">
                        <button
                            class="ts tiny button"
                            :class="{active:config.mode === 'month'}"
                            @click="config.mode = 'month'"
                        >{{CONSTANTS['MODE_BUTTON'].month}}</button>
                        <button
                            class="ts tiny button"
                            :class="{active:config.mode === 'week'}"
                            @click="config.mode = 'week'"
                        >{{CONSTANTS['MODE_BUTTON'].week}}</button>
                        <button
                            class="ts tiny button"
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
                                <template
                                    v-if="config.mode !== 'list'"
                                >
                                    <i
                                        class="prev button angle double left icon"
                                        :class="config.mode === 'month' ? 'year' : 'week'"
                                        @click="doubleLeft()"
                                    ></i>
                                    <i
                                        v-if="config.mode === 'month'"
                                        class="prev month button angle left icon"
                                        @click="changeCalendar('prevMonth')"
                                    ></i>
                                </template>
                                <i
                                    v-if="config.mode === 'month'"
                                    class="month day button"
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
                                <template
                                    v-if="config.mode !== 'list'"
                                >
                                    <i
                                        v-if="config.mode === 'month'"
                                        class="next month button angle right icon"
                                        @click="changeCalendar('nextMonth')"
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
                                    <div class="divider">借用型態：</div>
                                    <select class="ts basic tiny dropdown">
                                        <option>欸欸欸</option>
                                    </select>
                                    <div class="divider">承辦單位：</div>
                                    <select class="ts basic tiny dropdown">
                                        <option>啊啊啊啊啊啊</option>
                                    </select>
                                    <div class="divider">承辦人：</div>
                                    <select class="ts basic tiny dropdown">
                                        <option>嗚嗚嗚嗚</option>
                                    </select>
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
                                :class="{different: dateObj.different, holiday: dateObj.holiday}"
                                :key="dateObj.id"
                                :id="dateObj.id"
                            >
                                <h2
                                    :class="{holiday: dateObj.holiday}"
                                >{{dateObj.date.getDate()}}</h2>
                                <div class="schedules">
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else-if="config.mode === 'week'">
                        <tr>
                            <td class="colorful place cell">場地一</td>
                            <td class="day cell"></td>
                            <td class="day cell"></td>
                            <td class="day cell">
                                <div class="schedules">
                                    <div class="conference" id="4as56d4e">
                                        <div class="tablet or large device only text">
                                            <i>10:00-11:00</i><br>
                                            <i>薛小謙</i>
                                        </div>
                                        <div class="mobile only text">
                                            <i>10:00</i>
                                        </div>
                                    </div>
                                    <div class="tablet or large device only test" id="s6a5d41e">
                                        <div class="tablet or large device only text">
                                            <i>13:00-14:30</i><br>
                                        <i>王小明</i>
                                        </div>
                                        <div class="mobile only text">
                                            <i>13:00</i>
                                        </div>
                                    </div>
                                </div>
                                <div class="schedule overflow">
                                    <i class="ellipsis vertical icon"></i>
                                </div>
                            </td>
                            <td class="day cell"></td>
                            <td class="day cell"></td>
                            <td class="day cell"></td>
                            <td class="day cell"></td>
                        </tr>
                        <tr>
                            <td class="colorful place cell">場地二</td>
                            <td class="day cell"></td>
                            <td class="day cell"></td>
                            <td class="day cell"></td>
                            <td class="day cell"></td>
                            <td class="day cell"></td>
                            <td class="day cell"></td>
                            <td class="day cell"></td>
                        </tr>
                        <tr>
                            <td class="colorful place cell">場地三</td>
                            <td class="day cell"></td>
                            <td class="day cell"></td>
                            <td class="day cell"></td>
                            <td class="day cell"></td>
                            <td class="day cell"></td>
                            <td class="day cell"></td>
                            <td class="day cell"></td>
                        </tr>
                        <tr>
                            <td class="colorful place cell">場地四</td>
                            <td class="day cell"></td>
                            <td class="day cell"></td>
                            <td class="day cell"></td>
                            <td class="day cell"></td>
                            <td class="day cell"></td>
                            <td class="day cell"></td>
                            <td class="day cell"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
// import calendarMonthMode from "./items/calendarMonthMode.vue";
import CONSTANTS from '../../constants.js'
import Calendar from '../../classes/calendar.js';

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
            calendar: new Calendar(),
        };
    },
    props:{},
    compute:{},
    methods:{
        changeCalendar(method, ...params){
            this.calendar[method](...params);
            this.$forceUpdate();
        },
        doubleLeft(){
            if(this.config.mode === 'month'){
                this.calendar.prevYear();
            }else if(this.config.mode === 'week'){
                this.calendar.prevWeek();
            }
            this.$forceUpdate();
        },
        doubleRight(){
            if(this.config.mode === 'month'){
                this.calendar.nextYear();
            }else if(this.config.mode === 'week'){
                this.calendar.nextWeek();
            }
            this.$forceUpdate();
        },
    },
    components:{
        // calendarMonthMode
    },
}
</script>

<style>

</style>
