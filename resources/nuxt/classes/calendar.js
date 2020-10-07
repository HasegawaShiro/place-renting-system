import {API} from '../functions/API.js';
import {DataUtil} from '../utils/DataUtil.js';

export class Calendar {
    #Year;
    #Month;
    #Week;
    #Dates = [];
    #Schedules = [];
    #Today;
    #SelectedDate;
    static CONSTANTS = {
        DAY_TEXT: ['一','二','三','四','五','六','日'],
    }

    constructor(year, month) {
        this.#Today = new Date();
        this.#Today.setHours(0,0,0,0);
        this.#SelectedDate = this.#Today;
        if(year === undefined){
            this.#Year = this.#Today.getFullYear();
        }else if(year >= 1900 && year <= 275760){
            this.Year = year;
        }else{
            throw new Error("Invalid year. The year must in 1900~275760.");
        }

        if(month === undefined){
            this.#Month = this.#Today.getMonth();
        }else if(month >= 1 && month <= 12){
            this.Month = month;
        }else{
            throw new Error("Invalid month. The month must in 1~12.");
        }
        this.setDates();
    }

    setDates() {
        this.#Week = null;
        this.#Dates = [];
        const year = this.#Year;
        const month = this.#Month;
        const D = new Date(year, month);
        const today = this.#Today;
        const selected = this.#SelectedDate;
        let firstDay = D.getDay() == 0 ? 7 : D.getDay();
        let dp = D.getDate();
        for(let i = 0; i < 6; i++){
            const temp = new Date(year, month, dp);
            if(temp.getDay() != 1 && temp.getDay() <= firstDay){
                dp--;
            }else{
                break;
            }
        }

        for(let i = 0; i < 6; i++) {
            const week = [];
            let weekOfThisMonth = false;
            for(let j = 0; j < 7; j++){
                let putIn = {};
                const temp = new Date(year, month, dp);
                putIn.id = DataUtil.uuidv4();
                putIn.date = temp;
                putIn.different = temp.getMonth() !== month;
                putIn.holiday = temp.getDay() === 0 || temp.getDay() === 6
                week.push(putIn);
                dp++;
                if(temp.getMonth() === month) weekOfThisMonth = true;
                if((selected.getTime() === temp.getTime() && temp.getMonth() === month) || (today.getTime() === selected.getTime() && today.getTime() === temp.getTime())){
                    console.log(`today => ${today.getTime()}, temp => ${temp.getTime()}, selected => ${selected.getTime()}`)
                    this.#Week = i;
                }
            }
            if(weekOfThisMonth) this.#Dates.push(week);
        }

        if(DataUtil.isEmpty(this.#Week)) {
            this.#Week = 0;
            this.#SelectedDate = new Date(year, month, 1 ,0 ,0 ,0);
        };
    }
    setSchedules(){

    }

    get Year() {return this.#Year}
    set Year(y) {
        y = Number(y)
        if(isNaN(y) && y >= 1900 && y <= 275760){
            console.error("Invalid year. The year must in 1900~275760.");
        }else{
            this.#Year = y;
            this.setDates();
        }
    }

    get Month() {return this.#Month+1}
    set Month(m) {
        m = Number(m);
        if(isNaN(m) && m >= 1 && m <=12){
            console.error("Invalid month. The month must in 1~12.");
        }else{
            this.#Month = m-1;
            this.setDates();
        }
    }

    get Dates() {return this.#Dates}
    set Dates(x) {
        console.error("Dates cannot be setted.");
    }

    get Schedules() {return this.#Schedules}
    set Schedules(x) {
        console.error("Schedules cannot be setted.");
    }

    get SelectedDate() {return this.#SelectedDate}
    set SelectedDate(date) {
        if(typeof date === "object" && date.constructor === Date){
            this.#SelectedDate = date;
        }else{
            console.error("SelectedDate must be a Date object.");
        }
    }

    getDatesOfWeek() {
        return this.#Dates[this.#Week];
    }
    getYearOfWeek() {
        let result = {};
        for(let date of this.getDatesOfWeek()){
            if(result[date.date.getFullYear()] === undefined){
                result[date.date.getFullYear()] = 1;
            }else{
                result[date.date.getFullYear()]++;
            }
        }
        return result;
    }

    getDateToString(date, delimiter = '-'){
        if(date.date !== undefined){
            return this.getDateToString(date.date);
        }else if(typeof date !== "object" || date.constructor !== Date){
            date = this.#SelectedDate;
        }

        return `${date.getFullYear()}${delimiter}${date.getMonth()+1}${delimiter}${date.getDate()}`
    }

    prevYear() {
        let prevy = this.Year - 1;
        if(prevy > 1900){
            this.Year = prevy;
        }
    }
    nextYear() {
        let nexty = this.Year + 1;
        if(nexty < 275760){
            this.Year = nexty;
        }
    }
    prevMonth() {
        let prevm = this.Month - 1;
        if(prevm < 1){
            prevm = 12;
            this.prevYear();
        }
        this.Month = prevm;
    }
    nextMonth() {
        let nextm = this.Month + 1;
        if(nextm > 12){
            nextm = 1;
            this.nextYear();
        }
        this.Month = nextm;
    }
    prevWeek() {
        let prevw = this.#Week - 1;
        if(prevw < 0){
            this.prevMonth();
            prevw = this.#Dates.length-2;
        }
        let lastWeek = this.#Dates[prevw];
        this.#Week = prevw;
        this.#SelectedDate = lastWeek[0].date;
        console.log(this)
    }
    nextWeek() {
        let nextw = this.#Week + 1;
        if(nextw > this.#Dates.length-1){
            this.nextMonth();
            nextw = 1;
        }
        let nextWeek = this.#Dates[nextw];
        this.#Week = nextw;
        this.#SelectedDate = nextWeek[0].date;
        console.log(this)
    }
}
