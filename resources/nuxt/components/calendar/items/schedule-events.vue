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

            <div v-if="isLogin" class="plus-icon" @click="addClick()">
                <i class="plus icon"></i>
            </div>
        </section>

        <section class="upcoming-events">
            <div class="container">
                <!-- <h3>{{CONSTANTS.TEXT.today_event}}</h3> -->
                <div class="events-wrapper">
                    <template
                        v-for="(sd) in schedules"
                    >
                        <div
                            class="custom-accordion"
                            :class="sd.schedule_type"
                            :key="'schedule-event-'+sd.schedule_id"
                            :id="'schedule-accordion-'+sd.schedule_id"
                            @click="onAccordionClick($event, sd.schedule_id)"
                        >
                            <span>{{sd.schedule_from}} - {{sd.schedule_to}}</span><br>
                            <span>{{sd.schedule_title}}</span>
                            <i class="dropdown icon"></i>
                        </div>
                        <div
                            class="ts fluid segment grid schedule-detail"
                            :key="'schedule-detail-'+sd.schedule_id"
                            :id="'schedule-detail-'+sd.schedule_id"
                        >
                            <div
                                v-if="isLogin && (sd.editable || sd.deletable)"
                                class="two wide column form-items"
                            >
                                <div
                                    v-if="sd.editable"
                                    class="item"
                                    @click="editClick(sd)"
                                >
                                    <i class="write icon"></i>
                                    <span class="item-text tablet or large device only text">編輯</span>
                                </div>
                                <div
                                    v-if="sd.deletable"
                                    class="item"
                                    @click="deleteClick(sd.schedule_id)"
                                >
                                    <i class="trash icon"></i>
                                    <span class="item-text tablet or large device only text">刪除</span>
                                </div>
                            </div>
                            <div class="fourteen wide column schedule-main">
                                <!-- <div class="ts list">
                                    <div class="item">
                                        {{CONSTANTS.FORM_TEXT.schedule_from}}：
                                        {{sd.schedule_from}}
                                    </div>
                                </div>
                                <div class="ts list">
                                    <div class="item">
                                        {{CONSTANTS.FORM_TEXT.schedule_to}}：
                                        {{sd.schedule_to}}
                                    </div>
                                </div> -->
                                <div class="ts list">
                                    <div class="item">
                                        {{CONSTANTS.FORM_TEXT.repeat}}：
                                        {{parseScheduleRepeat(sd)}}
                                    </div>
                                </div>
                                <div class="ts list">
                                    <div class="item">
                                        {{CONSTANTS.FORM_TEXT.place_id}}：
                                        {{selects.place[sd.place_id]}}
                                    </div>
                                </div>
                                <div class="ts list">
                                    <div class="item">
                                        {{CONSTANTS.FORM_TEXT.schedule_registrant}}：
                                        {{sd.schedule_registrant}}
                                    </div>
                                </div>
                                <div class="ts list">
                                    <div class="item">
                                        {{CONSTANTS.FORM_TEXT.schedule_type}}：
                                        {{selects.type[sd.schedule_type]}}
                                    </div>
                                </div>
                                <div class="ts list">
                                    <div class="item">
                                        <div style="width: 100%; display:block">{{CONSTANTS.FORM_TEXT.schedule_content}}：</div>
                                        <div
                                            v-if="!isEmpty(sd.schedule_content)"
                                            style="margin-left: 1.2em; margin-top: .5em"
                                        >
                                            {{sd.schedule_content}}
                                        </div>
                                    </div>
                                </div>
                                <div class="ts list">
                                    <div class="item">
                                        {{CONSTANTS.FORM_TEXT.user_id}}：
                                        {{selects.user[sd.user_id]}}
                                    </div>
                                </div>
                                <div class="ts list">
                                    <div class="item">
                                        {{CONSTANTS.FORM_TEXT.util}}：
                                        {{sd.util_name}}
                                    </div>
                                </div>
                                <div class="ts list">
                                    <div class="item">
                                        {{CONSTANTS.FORM_TEXT.phone}}：
                                        {{sd.phone}}
                                    </div>
                                </div>
                                <div class="ts list">
                                    <div class="item">
                                        {{CONSTANTS.FORM_TEXT.mail}}：
                                        {{sd.email}}
                                    </div>
                                </div>
                                <div class="ts list">
                                    <div class="item">
                                        {{CONSTANTS.FORM_TEXT.schedule_contact}}：
                                        {{sd.schedule_contact}}
                                    </div>
                                </div>
                                <div class="ts list">
                                    <div class="item">
                                        {{CONSTANTS.FORM_TEXT.schedule_url}}：
                                        {{sd.schedule_url}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="ts divider"
                            :key="'schedule-event-divider-'+sd.schedule_id"
                        ></div>
                    </template>

                </div>
            </div>
        </section>
    </dialog>
</div>
</template>

<script>
import CONSTANTS from "../../../constants.js";
import DataUtil from '../../../utils/DataUtil.js';
import API from '../../../api.js';

export default {
    data() {
        return {
            CONSTANTS: CONSTANTS.schedule,
            selects: {
                user: {},
                place: {},
                util: {},
                type: CONSTANTS.schedule.selects.types
            },
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
        },
        isLogin() {
            return this.$parent.isLogin;
        },
    },
    methods: {
        async openModal(inputData = {}) {
            window.globalLoading.loading();
            function putData(putIn, data) {
                for(let k in data){
                    if(putIn[k] !== undefined) putIn[k] = data[k];
                }
            }
            await putData(this, inputData);
            this.selects.user = await API.getReferenceSelect("user");
            this.selects.place = await API.getReferenceSelect("place");
            this.selects.util = await API.getReferenceSelect("util");
            window.globalLoading.unloading();
            ts('dialog.schedule-events').modal("show");
            if(!DataUtil.isEmpty(inputData.active_id)){
                this.onAccordionClick(null, this.active_id);
            }
        },
        closeModal() {
            ts('dialog.schedule-events').modal("hide");
            // this.toShowAdd();
        },
        onAccordionClick(event = null, id = null) {
            let clicked = DataUtil.isEmpty(event) ? document.querySelector(`#schedule-accordion-${id}`) : event.target;
            if(DataUtil.isEmpty(event) && !clicked.classList.contains("active")){
                clicked.classList.add("active");
            }else if(!DataUtil.isEmpty(event)){
                clicked.classList.toggle("active");
            }
            this.active_id = id;
            let toOpen = clicked.classList.contains("active");
            for(let el of document.querySelectorAll(".custom-accordion")){
                if(el !== clicked){
                    el.classList.remove("active");
                }
            }
            for(let el of document.querySelectorAll(".schedule-detail")){
                el.style['max-height'] = '0px';
                el.style['padding'] = '0px';
            }

            if(toOpen){
                let detail = document.querySelector(`#schedule-detail-${id}`);
                // console.log(detail.querySelector(".schedule-main").scrollHeight)
                detail.style['max-height'] = detail.querySelector(".schedule-main").scrollHeight + 50 + 'px';
                detail.style['padding'] = '1em .25em';
            }
        },
        parseScheduleRepeat(schedule) {
            if(schedule.schedule_repeat) {
                return "";
            } else {
                return "否";
            }
        },
        isEmpty(obj) {
            return DataUtil.isEmpty(obj);
        },
        addClick() {
            if(this.$parent.isLogin) {
                let toParseDate = new Date();
                if(!DataUtil.isEmpty(this.showDate) && this.showDate.constructor == Date) {
                    toParseDate = this.showDate;
                }
                this.$parent.$refs["form"].openModal('add', {schedule_date: DataUtil.formatDateInput(toParseDate)});
                this.closeModal();
            }
        },
        async editClick(data) {
            this.$parent.$refs["form"].openModal('edit', data);
            this.closeModal();
        },
        async deleteClick(id) {
            await API.sendRequest(`/api/data/schedule/${id}`,'delete').then(response => {
                function deleteSchedule(source, id) {
                    const toDelete = source.findIndex(x => x.schedule_id == id);
                    if(toDelete != -1) delete source[toDelete];
                    return source.filter(() => true);
                };
                this.schedules = deleteSchedule(this.schedules, id);
                window.$page.$refs["content"].schedules = deleteSchedule(window.$page.$refs["content"].schedules, id);
                window.mainLayout.showSnackbar("success", response.data.messages);
            }).catch(e => {
                if(!DataUtil.isEmpty(e.response.data.messages)) {
                    window.mainLayout.showSnackbar("error", e.response.data.messages);
                }
            });
        },
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
.schedule-events .upcoming-events .container .events-wrapper {
    margin-bottom: 30px;
}
.schedule-events .upcoming-events .container .events-wrapper .custom-accordion {
    color: #222;
    font-weight: 500;
    padding-left: 2.5em;
    padding-right: 2.5em;
    cursor: pointer;
    padding: 18px;
    width: 100%;
    border: none;
    text-align: left;
    outline: none;
    font-size: 15px;
    transition: 0.4s;
}
.schedule-events .upcoming-events .container .events-wrapper .custom-accordion::before {
    content: "";
    display: block;
    width: .8em;
    height: 100%;
    background-color: #e8e8e8;
    position: absolute;
    top: 0px;
    left: 0px;
}
.schedule-events .upcoming-events .container .events-wrapper .custom-accordion.conference::before {
    background-color: rgb(240, 133, 61);
}
.schedule-events .upcoming-events .container .events-wrapper .custom-accordion.activity::before {
    background-color: rgb(255, 233, 110);
}
.schedule-events .upcoming-events .container .events-wrapper .custom-accordion.lesson::before {
    background-color: rgb(54, 99, 182);
}
.schedule-events .upcoming-events .container .events-wrapper .custom-accordion.exam::before {
    background-color: rgb(217, 41, 223);
}
.schedule-events .upcoming-events .container .events-wrapper .custom-accordion.other::before {
    background-color: rgb(61, 216, 255);
}
.schedule-events .upcoming-events .container .events-wrapper .custom-accordion.active {
    background-color:  rgb(198, 255, 233);
}
@media(hover: hover) and (pointer: fine) {
    .schedule-events .upcoming-events .container .events-wrapper .custom-accordion:hover {
        background-color: rgba(130, 226, 189, 0.774);
    }
    .schedule-events .upcoming-events .container .events-wrapper .form-items .item:hover {
        color: rgb(8, 138, 120);
        cursor: pointer;
    }
}
.schedule-events .upcoming-events .container .events-wrapper .custom-accordion i {
    transform: rotate(90deg);
    position: absolute;
    right: 0;
    font-size: 1.5em;
    top: 30%;
    transition: 0.4s;
}
.schedule-events .upcoming-events .container .events-wrapper .custom-accordion.active i {
    transition: 0.4s;
    transform: rotate(0deg);
}
.schedule-events .upcoming-events .container .events-wrapper .form-items {
    color: #888;
    font-size: 1.25em;
    text-align: right;
    border-right: 1px solid #ccc;
    padding-right: .25em;
    padding-left: .15em;
}
.schedule-events .upcoming-events .container .events-wrapper .form-items .item {
    text-align: right;
    margin-bottom: .5em;
}
.schedule-events .upcoming-events .container .events-wrapper .form-items .item:active {
        color: rgb(8, 138, 120);
        cursor: pointer;
    }
.schedule-events .upcoming-events .container .events-wrapper .form-items .icon {
    margin-right: .05em;
    display: inline;
}
.schedule-events .upcoming-events .container .events-wrapper .form-items .item-text {
    display: inline;
    font-size: .85em;
    max-width: 0px;
}
.schedule-events .upcoming-events .container .events-wrapper .schedule-detail {
    box-shadow: 0px 2px 8px 3px #b3b3b3 inset;
    border-radius: 0px;
    margin: 0px;
    padding: 0px;
    max-height: 0px;
    overflow: hidden;
    transition: padding 0.2s ease-out, max-height 0.4s ease-out;
}
.schedule-events .upcoming-events .container .events-wrapper .divider{
    margin: 0;
}

@media only screen and (max-width:767px){
    .schedule-events .today-box {
    background: linear-gradient(
        to left,
        rgb(8, 138, 120),
        rgba(54, 255, 188, 0.479)
    ),
    #1f6585;
    color: #ffffff;
    padding: 2.5em 1.5em;
    position: relative;
    box-shadow: 0px 0px 40px -9px #026b6e;
    margin-bottom: 50px;
}
}
</style>
