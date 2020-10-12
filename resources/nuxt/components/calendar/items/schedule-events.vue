<template>
    <dialog class="ts mobile-wrapper tiny closable modal new schedule">
        <header class="header">
        </header>

        <section class="today-box" id="today-box">
            <span class="breadcrumb">
                {{ CONSTANTS.TEXT.selected_date }}
            </span>
            <h3 class="date-title">
                {{ parseSelectedDate[0] }}
                {{ CONSTANTS.TEXT.year }}
                {{ parseSelectedDate[1] }}
                {{ CONSTANTS.TEXT.month }}
                {{ parseSelectedDate[2] }}
                {{ CONSTANTS.TEXT.day }}
            </h3>

            <div class="plus-icon">
                <i class="plus icon"></i>
            </div>
        </section>

        <section class="upcoming-events">
            <div class="container">
                <h3>{{CONSTANTS.TEXT.today_event}}</h3>
                <div class="events-wrapper">
                    <div class="ts flatted borderless segment event">
                        <details class="ts accordion">
                            <summary>
                                <span>主題主題主題</span><br>
                                <span>場地場地場地場地場地</span>
                                <i class="dropdown icon"></i>
                            </summary>
                            <div class="ts fluid insetted slate">
                                <p>內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容</p>
                            </div>
                        </details>
                    </div>
                    <div class="ts divider"></div>
                    <div class="ts flatted borderless segment event">
                        <details class="ts accordion">
                            <summary @click="eventUnfold()">
                                <span>主題主題主題</span><br>
                                <span>場地場地場地場地場地</span>
                                <i class="dropdown icon"></i>
                            </summary>
                            <div class="ts fluid insetted slate">
                                <p>內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容內容</p>
                            </div>
                        </details>
                    </div>
                    <div class="ts divider"></div>
                </div>
            </div>
        </section>
    </dialog>
</template>

<script>
import CONSTANTS from "../../constants.js";
import DataUtil from '../../utils/DataUtil.js';

export default {
    data() {
        return {
            CONSTANTS: CONSTANTS.schedule,
        };
    },
    props: {
        'form-data': {
            type: Object,
        },
    },
    computed: {
        parseSelectedDate() {
            let result = [];
            let toParseDate = new Date();
            if(!DataUtil.isEmpty(this.formData.selected_date) && this.formData.selected_date.constructor === Date) {
                toParseDate = this.formData.selected_date;
            }

            result.push(toParseDate.getFullYear(), toParseDate.getMonth()+1, toParseDate.getDate());
            return result;
        }
    },
    methods: {
        eventUnfold() {

        },
    },
    watch: {
        formData(newVal, oldVal){
            this.formData = newVal;
        }
    },
};
</script>

<style>
.container {
    width: 100%;
    margin: 0;
}

.mobile-wrapper {
    background: #fff;
    /* relative with .today-box::before*/
    z-index: 1;
    /*positive*/
    position: relative;
    /*---------*/
    width: 380px;
    min-height: 100%;
    margin: auto;
    padding: 10px 0 20px;
    border-radius: 10px;
    box-shadow: 0px 10px 30px -10px #000;
    overflow: hidden;
}

.header {
    padding-bottom: 15px;
}
.header .container {
    position: relative;
}
.header .container span {
    color: #444;
    font-family: "Ramabhadra";
    font-size: 21px;
    font-weight: 700;
}

.today-box {
    background: linear-gradient(
        to left,
        rgb(8, 138, 120),
        rgba(54, 255, 188, 0.479)
    ),
    #1f6585;
    color: #ffffff;
    padding: 37px 40px;
    position: relative;
    box-shadow: 0px 0px 40px -9px #026b6e;
    margin-bottom: 50px;
}
.today-box::before {
    content: "";
    background: linear-gradient(
        to left,
        rgb(8, 138, 120),
        rgba(54, 255, 188, 0.479)
        ),
        #1f6585;
    opacity: 0.4;
    z-index: -1;
    display: block;
    width: 100%;
    height: 40px;
    margin: auto;
    position: absolute;
    bottom: -13px;
    left: 50%;
    transform: translatex(-50%);
    border-radius: 50%;
    box-shadow: 0px 0px 40px 0 #026b6e;
}
.today-box .breadcrumb {
    font-size: 1.5em;
    font-weight: 300;
    position: relative;
}
.today-box .date-title {
    color: #fff !important;
    margin: 7px 0 0 0;
    letter-spacing: 1px;
    font-weight: 600;
    text-shadow: 0px 0px 5px rgba(0, 0, 0, 0.15);
}
.today-box .plus-icon {
    border: 2px solid rgba(255, 255, 255, 0.6);
    border-radius: 50%;
    box-shadow: 0px 10px 30px -14px #000;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    right: 40px;
    cursor: pointer;
    transition: all 350ms ease;
    transition-timing-function: cubic-bezier(0.05, 1.8, 1, 1.57);
}
.today-box .plus-icon:hover {
  transform: translateY(-40%);
}
.today-box .plus-icon i {
    font-size: 1.2em;
    font-weight: 700;
    background: #fff;
    color: #777;
    width: 45px;
    height: 45px;
    border: 6px solid #0e8d82;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0;
}
.today-box .plus-icon:active {
  top: 52%;
  transform: translatey(-52%);
  right: 38px;
  box-shadow: 0px 8px 30px -14px #000;
}

.upcoming-events .container h3 {
    color: #333;
    font-size: 1.2em;
    margin-bottom: 30px;
    position: relative;
    left: 10%;
}
.upcoming-events .container h3::before {
    content: "";
    display: block;
    width: 18.5%;
    height: 2px;
    background-color: #e8e8e8;
    position: absolute;
    top: 50%;
    transform: translatey(-60%);
    left: -20%;
}
.upcoming-events .container h3::after {
    content: "";
    display: block;
    width: 84%;
    height: 2px;
    background-color: #e8e8e8;
    position: absolute;
    top: 50%;
    transform: translatey(-60%);
    right: 0;
}
.upcoming-events .container .events-wrapper {
    margin-bottom: 30px;
}
.upcoming-events .container .events-wrapper .event {
    margin: 0;
    padding: 0;
}
.upcoming-events .container .events-wrapper .event .accordion summary {
    color: #222;
    font-weight: 500;
    padding-left: 2.5em;
    padding-right: 2.5em;
}
.upcoming-events .container .events-wrapper .event .accordion[open] summary {
    background-color: rgba(63, 255, 182, 0.774);
}
@media(hover: hover) and (pointer: fine) {
    .upcoming-events .container .events-wrapper .event .accordion[open] summary,
    .upcoming-events .container .events-wrapper .event .accordion summary:hover {
        background-color: rgba(63, 255, 182, 0.774);
    }
}
.upcoming-events .container .events-wrapper .event .accordion summary i {
    position: absolute;
    right: 0;
    font-size: 1.5em;
    top: 30%;
}
.upcoming-events .container .events-wrapper .slate{
    box-shadow: 0px 0px 8px 3px #b3b3b3 inset;
}
.upcoming-events .container .events-wrapper .divider{
    margin: 0;
}
</style>
