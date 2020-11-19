import CONSTANTS from '../constants.js';

export default class DataUtil{
    static equalityComparison(a, b, strict = false) {
        let result = true;
        try {
            if (strict) {
                result = JSON.stringify(a) === JSON.stringify(b);
            } else {
                result = JSON.stringify(a) == JSON.stringify(b);
            }
        } catch (error) {
            if (typeof a === "object" && typeof b === "object") {
                for (let key in a) {
                    if (b[key] !== undefined) {
                        DataUtil.equalityComparison(a[key], b[key], strict);
                    } else {
                        result = false;
                        break;
                    }
                }
            } else if (typeof a === typeof b) {
                if (strict) {
                    result = a === b;
                } else {
                    result = a == b;
                }
            } else {
                result = false;
            }
        }
        return result;
    }

    static isEmpty(...values) {
        let result = [];
        for (let value of values) {
            let determination = false;
            if (value === undefined) {
                determination = true;
            } else if (value === null) {
                determination = true;
            } else if (typeof value === "string" && (value === "" || value.trim() === "")) {
                determination = true;
            } else if (typeof value === "object") {
                if (value.constructor === Array) {
                    determination = DataUtil.equalityComparison(value, [], true);
                } else if (value.constructor === Object) {
                    determination = DataUtil.equalityComparison(value, {}, true);
                }
            }
            result.push(determination);
        }

        return result.find(x => x == false) === undefined;
    }

    static isAnyEmpty(...values) {
        for (let value of values) {
            if (DataUtil.isEmpty(value)) return true;
        }
        return false;
    }

    static uuidv4(){
        return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c =>
            (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
        )
    }

    static getMessage(msg) {
        let result = CONSTANTS.messages[msg];
        if(DataUtil.isEmpty(result)) result = msg;
        return result;
    }

    static parseResponseMessages(msg) {
        if(typeof msg == "object" && msg.constructor === Array){
            for(let i in msg){
                msg[i] = DataUtil.getMessage(msg[i]);
            }
            return DataUtil.unionString("", msg, "\r\n");
        }else if(typeof msg == "string"){
            return DataUtil.getMessage(msg);
        }
    }

    static deepClone(obj) {
        try {
            return JSON.parse(JSON.stringify(obj));
        } catch (error) {
            if (typeof obj === "object") {
                let result;
                if (obj.constructor === Array) {
                    result = [];
                } else {
                    result = {};
                }
                for (let key in obj) {
                    result[key] = DataUtil.deepClone(obj[key]);
                }
                return result;
            } else {
                return obj;
            }
        }
    }

    static compareToSort(a, b, order) {
        if (a > b) {
            if (order === "ASC") {
                return 1;
            } else {
                return -1;
            }
        } else if (a == b) {
            return 0;
        } else {
            if (order === "ASC") {
                return -1;
            } else {
                return 1;
            }
        }
    }

    static sortArrayByCustomKey(array, key = [], order = "ASC") {
        if (array.constructor === Array) {
            if (typeof key === "string" || (typeof key === "number" && Number.isInteger(key))) {
                key = [key];
            }

            if (key.constructor !== Array) {
                console.error("The second parameter must be an array, string or integer.");
            } else {
                array.sort((a, b) => {
                    let compared = 0;
                    if ((a.constructor === Array || a.constructor === Object) && (b.constructor === Array || b.constructor === Object)) {
                        for (let k of key) {
                            compared = DataUtil.compareToSort(a[k], b[k], order);
                            if (compared === 0) {
                                continue;
                            } else {
                                break;
                            }
                        }
                    } else {
                        console.error("The item which want to be compared must be array or object.");
                    }
                    return compared;
                });
            }
        } else {
            console.error("The first parameter must be an array.");
        }
    }

    static unionString(string, union, delimiter = ",") {
        string = string.toString();
        if (typeof union === "object") {
            for (let i in union) {
                string = DataUtil.unionString(string, union[i], delimiter);
            }
        } else {
            if (!DataUtil.isEmpty(string)) {
                string += delimiter;
            }
            string += union;
        }

        return string;
    }

    static formatDateInput(date) {
        function fillZero(s){
            if(s < 10) return `0${s}`; else return s;
        };
        let year, month, day;
        if(typeof date == "string") {
            const temp = date.includes("-") ? date.split("-") : date.split("/");
            year = temp[0];
            month = temp[1];
            day = temp[2];
        }else if(typeof date == "object" && date.constructor == Date) {
            year = date.getFullYear();
            month = date.getMonth()+1;
            day = date.getDate();
        }else{
            throw new Error("Invalid date format.");
        }

        return `${year}-${fillZero(month)}-${fillZero(day)}`
    }

    static dateTextToDateObject(datetext) {
        const textSplit = datetext.split('-');
        if(textSplit.length == 3) {
            textSplit[1]--;
            return new Date(...textSplit);
        }
    }

    static decimalToBinary(int, fill = 0) {
        let bin = int.toString(2);
        let len = fill - bin.length < 0 ? 0 : fill - bin.length;
        for(let i = 0; i < len; i++) {
            bin = "0"+bin;
        }

        return bin;
    }
}
