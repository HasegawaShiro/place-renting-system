<template>
<div class="ts modals dimmer">
    <dialog class="ts mobile-wrapper closable modal schedule-events">
        <header class="header">
        </header>

        <section class="today-box" id="today-box">
            <span class="breadcrumb">
                {{ CONSTANTS.TEXT.selected_date }}
            </span>
            <h3 class="date-title">
                {{ parseDate[0] }}
                {{ CONSTANTS.TEXT.year }}
                {{ parseDate[1] }}
                {{ CONSTANTS.TEXT.month }}
                {{ parseDate[2] }}
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
</div>
</template>

<script>
import CONSTANTS from "../../../constants.js";
import DataUtil from '../../../utils/DataUtil.js';

export default {
    data() {
        return {
            CONSTANTS: CONSTANTS.schedule,
            showDate: null,
            schedules: [],
            active_id: 0,
        };
    },
    props: {
    },
    computed: {
        parseDate() {
            let result = [];
            let toParseDate = new Date();
            if(!DataUtil.isEmpty(this.showDate) && this.showDate.constructor == Date) {
                toParseDate = this.showDate;
            }

            result.push(toParseDate.getFullYear(), toParseDate.getMonth()+1, toParseDate.getDate());
            return result;
        }
    },
    methods: {
        async openModal(inputData = {}) {
            function putData(putIn, data) {
                for(let k in data){
                    if(putIn[k] !== undefined) putIn[k] = data[k];
                }
            }
            await putData(this, inputData);
            console.log(this)
            ts('dialog.schedule-events').modal("show");
            // this.toHideAdd();
        },
        closeModal() {
            ts('dialog.schedule-events').modal("hide");
            // this.toShowAdd();
        },
        eventUnfold() {

        },
    },
    watch: {
    },
};
</script>

<style>
.schedule-events .container {
    width: 100%;
    margin: 0;
}

.schedule-events .mobile-wrapper {
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

.schedule-events .header {
    padding-bottom: 15px;
}
.schedule-events .header .container {
    position: relative;
}
.schedule-events .header .container span {
    color: #444;
    font-family: "Ramabhadra";
    font-size: 21px;
    font-weight: 700;
}

.schedule-events .today-box {
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
.schedule-events .today-box::before {
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
.schedule-events .today-box .breadcrumb {
    font-size: 1.5em;
    font-weight: 300;
    position: relative;
}
.schedule-events .today-box .date-title {
    color: #fff !important;
    margin: 7px 0 0 0;
    letter-spacing: 1px;
    font-weight: 600;
    text-shadow: 0px 0px 5px rgba(0, 0, 0, 0.15);
}
.schedule-events .today-box .plus-icon {
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
.schedule-events .today-box .plus-icon:hover {
  transform: translateY(-40%);
}
.schedule-events .today-box .plus-icon i {
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
.schedule-events .today-box .plus-icon:active {
  top: 52%;
  transform: translatey(-52%);
  right: 38px;
  box-shadow: 0px 8px 30px -14px #000;
}

.schedule-events .upcoming-events .container h3 {
    color: #333;
    font-size: 1.2em;
    margin-bottom: 30px;
    position: relative;
    margin-left: 5em;
}
.schedule-events .upcoming-events .container h3::before {
    content: "";
    display: block;
    width: 9%;
    height: 2px;
    background-color: #e8e8e8;
    position: absolute;
    top: 50%;
    transform: translatey(-60%);
    left: -5em;
}
/* .schedule-events .upcoming-events .container h3::after {
    content: "";
    display: block;
    width: 91%;
    height: 2px;
    background-color: #e8e8e8;
    position: absolute;
    top: 50%;
    transform: translatey(-60%);
    right: 0;
} */
.schedule-events .upcoming-events .container .events-wrapper {
    margin-bottom: 30px;
}
.schedule-events .upcoming-events .container .events-wrapper .event {
    margin: 0;
    padding: 0;
}
.schedule-events .upcoming-events .container .events-wrapper .event .accordion summary {
    color: #222;
    font-weight: 500;
    padding-left: 2.5em;
    padding-right: 2.5em;
}
.schedule-events .upcoming-events .container .events-wrapper .event .accordion[open] summary {
    background-color: rgba(63, 255, 182, 0.774);
}
@media(hover: hover) and (pointer: fine) {
    .schedule-events .upcoming-events .container .events-wrapper .event .accordion[open] summary,
    .schedule-events .upcoming-events .container .events-wrapper .event .accordion summary:hover {
        background-color: rgba(63, 255, 182, 0.774);
    }
}
.schedule-events .upcoming-events .container .events-wrapper .event .accordion summary i {
    position: absolute;
    right: 0;
    font-size: 1.5em;
    top: 30%;
}
.schedule-events .upcoming-events .container .events-wrapper .slate{
    box-shadow: 0px 0px 8px 3px #b3b3b3 inset;
}
.schedule-events .upcoming-events .container .events-wrapper .divider{
    margin: 0;
}
</style>
