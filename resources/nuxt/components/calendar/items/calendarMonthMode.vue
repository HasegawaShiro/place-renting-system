<template>
    <table
        class="ts fixed seven column table month mode"
        :style="toggleShowStyle"
    >
        <thead>
            <tr>
                <td class="colorful month" colspan="7">
                    <i class="prev year button angle double left icon"></i>
                    <i class="prev month button angle left icon"></i>
                    <i class="month day button">
                        {{calendar.Year}}
                        {{calendar.Month}}
                    </i>
                    <i class="next month button angle right icon"></i>
                    <i class="next year button angle double right icon"></i>
                </td>
            </tr>
            <tr>
                <td class="colorful search" colspan="7">
                    <calendar-search-bar
                        :datas="searchBarDatas"
                    ></calendar-search-bar>
                </td>
            </tr>
            <tr>
                <th class="colorful day header">一</th>
                <th class="colorful day header">二</th>
                <th class="colorful day header">三</th>
                <th class="colorful day header">四</th>
                <th class="colorful day header">五</th>
                <th class="colorful day header">六</th>
                <th class="colorful day header">日</th>
            </tr>
        </thead>
        <tbody>
            <tr class="week">
                <td class="prev month day cell">
                    <h2>30</h2>
                    <div class="schedules">
                        <div class="conference" id="4as56d4e">
                            <div class="tablet or large device only text">
                                <i>10:00-11:00</i><br>
                                <i>ㄏ哀哀哀哀哀哀哀哀哀哀哀哀</i>
                            </div>
                            <div class="mobile only text">
                                <i>10:00</i>
                            </div>
                        </div>
                        <div class="tablet or large device only test" id="s6a5d41e">
                            <div class="tablet or large device only text">
                                <i>13:00-14:30</i><br>
                            <i>考試欸欸欸欸欸欸欸欸欸欸欸欸</i>
                            </div>
                            <div class="mobile only text">
                                <i>13:00</i>
                            </div>
                        </div>
                    </div>
                    <div class="mobile only schedule overflow">
                        <i class="ellipsis vertical icon"></i>
                    </div>
                </td>
                <td class="prev month day cell">
                    <h2>31</h2>
                    <div class="schedules">
                    </div>
                </td>
                <td class="day cell">
                    <h2>1</h2>
                    <div class="schedules">
                    </div>
                </td>
                <td class="day cell">
                    <h2>2</h2>
                    <div class="schedules">
                    </div>
                </td>
                <td class="day cell">
                    <h2>3</h2>
                    <div class="schedules">
                    </div>
                </td>
                <td class="day cell">
                    <h2 class="holiday">4</h2>
                    <div class="schedules">
                    </div>
                </td>
                <td class="day cell">
                    <h2 class="holiday">5</h2>
                    <div class="schedules">
                    </div>
                </td>
            </tr>
            <tr class="week">
                <td class="day cell">
                    <h2>6</h2>
                    <div class="schedules">
                    </div>
                </td>
                <td class="day cell">
                    <h2>7</h2>
                    <div class="schedules">
                    </div>
                </td>
                <td class="day cell">
                    <h2>8</h2>
                    <div class="schedules">
                    </div>
                </td>
                <td class="day cell">
                    <h2>9</h2>
                    <div class="schedules">
                        <div class="other" id="4as56d4e">
                                <div class="tablet or large device only text">
                                    <i>08:00-11:30</i><br>
                                    <i>其他</i>
                                </div>
                                <div class="mobile only text">
                                    <i>08:00</i>
                                </div>
                            </div>
                            <div class="tablet or large device only course" id="a5f4wer5">
                                <div class="tablet or large device only text">
                                    <i>13:00-15:00</i><br>
                                    <i>上課喔嗚嗚嗚嗚嗚嗚嗚嗚嗚嗚嗚</i>
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
                    <td class="day cell">
                        <h2>10</h2>
                        <div class="schedules">
                        </div>
                    </td>
                    <td class="day cell">
                        <h2 class="holiday">11</h2>
                        <div class="schedules">
                        </div>
                    </td>
                    <td class="day cell">
                        <h2 class="holiday">12</h2>
                        <div class="schedules">
                        </div>
                    </td>
                </tr>
                <tr class="week">
                    <td class="day cell">
                        <h2>13</h2>
                        <div class="schedules">
                        </div>
                    </td>
                    <td class="day cell">
                        <h2>14</h2>
                        <div class="schedules">
                        </div>
                    </td>
                    <td class="day cell">
                        <h2>15</h2>
                        <div class="schedules">
                        </div>
                    </td>
                    <td class="day cell">
                        <h2>16</h2>
                        <div class="schedules">
                        </div>
                    </td>
                    <td class="day cell">
                        <h2>17</h2>
                        <div class="schedules">
                        </div>
                    </td>
                    <td class="day cell">
                        <h2 class="holiday">18</h2>
                        <div class="schedules">
                        </div>
                    </td>
                    <td class="day cell">
                        <h2 class="holiday">19</h2>
                        <div class="schedules">
                        </div>
                    </td>
                </tr>
                <tr class="week">
                    <td class="day cell">
                        <h2>20</h2>
                        <div class="schedules">
                        </div>
                    </td>
                    <td class="day cell">
                        <h2>21</h2>
                        <div class="schedules">
                        </div>
                    </td>
                    <td class="day cell">
                        <h2>22</h2>
                        <div class="schedules">
                        </div>
                    </td>
                    <td class="day cell">
                        <h2>23</h2>
                        <div class="schedules">
                        </div>
                    </td>
                    <td class="day cell">
                        <h2>24</h2>
                        <div class="schedules">
                        </div>
                    </td>
                    <td class="day cell">
                        <h2 class="holiday">25</h2>
                        <div class="schedules">
                        </div>
                    </td>
                    <td class="day cell">
                        <h2 class="holiday">26</h2>
                        <div class="schedules">
                        </div>
                    </td>
                </tr>
                <tr class="week">
                    <td class="day cell">
                        <h2>27</h2>
                        <div class="schedules">
                        </div>
                    </td>
                    <td class="day cell">
                        <h2>28</h2>
                        <div class="schedules">
                        </div>
                    </td>
                    <td class="day cell">
                        <h2>29</h2>
                        <div class="schedules">
                        </div>
                    </td>
                    <td class="day cell">
                        <h2>30</h2>
                        <div class="schedules">
                        </div>
                    </td>
                    <td class="next month day cell">
                        <h2>1</h2>
                        <div class="schedules">
                        </div>
                    </td>
                    <td class="next month day cell">
                        <h2 class="holiday">2</h2>
                        <div class="schedules">
                        </div>
                    </td>
                    <td class="next month day cell">
                        <h2 class="holiday">3</h2>
                        <div class="schedules">
                        </div>
                    </td>
                </tr>
        </tbody>
    </table>
</template>

<script>
import {Calendar} from '../../../classes/calendar.js';

export default {
    data() {
        return {

        };
    },
    props: {
        show: {
            type: Boolean,
            default() {
                return true;
            }
        },
        calendar: {
            type: Calendar,
        },
        'borrow-types': {
            type: Object,
            default() {
                return {};
            }
        },
        'users': {
            type: Array,
            default() {
                return [];
            }
        }
    },
    compute:{
        toggleShowStyle() {
            if(this.props.show){
                return {
                    display: 'inherit'
                }
            }else{
                return {
                    display: 'none'
                }
            }
        },
    },
    methods:{},
}
</script>

<style>

</style>
