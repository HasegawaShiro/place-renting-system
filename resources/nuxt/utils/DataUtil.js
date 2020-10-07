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
                        equalityComparison(a[key], b[key], strict);
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
}
