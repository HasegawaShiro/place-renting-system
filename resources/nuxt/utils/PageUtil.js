import API from '../api.js';
import DataUtil from './DataUtil.js';

export default class PageUtil {}

class User {}

class Schedule {
    static fields() {
        return [
            (new Field(
                'schedule_id',
                '',
                'number',
                {
                    showOnForm: false,
                    readOnly: true,
                    sync: false,
                }
            )),
            (new Field(
                'schedule_title',
                '主題',
                'text',
                {
                    editable: false,
                }
            )),
            (new Field(
                'schedule_date',
                '日期',
                'date',
                {
                    editable: false,
                }
            )),
            (new Field(
                'schedule_from',
                '開始時間',
                'time',
                {}
            )),
            (new Field(
                'schedule_to',
                '結束時間',
                'time',
                {}
            )),
            (new Field(
                'fullday',
                '全天',
                'boolean',
                {
                    onlyFrontend: true,
                    showOnList: false,
                }
            )),
            (new Field(
                'schedule_repeat',
                '是否重複',
                'boolean',
                {}
            )),
            (new Field(
                'repeat_details',
                '重複方式',
                'custom',
                {
                    onlyFrontend: true,
                    showOnList: false,
                }
            )),
            (new Field(
                'place_id',
                '使用地點',
                'select',
                {
                    selectOptions: API.getReferenceSelect("place"),
                }
            )),
            (new Field(
                'schedule_registrant',
                '登記人',
                'text',
                {
                    remarks: '校外申請者，請填寫主辦單位',
                }
            )),
            (new Field(
                'schedule_type',
                '借用型態',
                'select',
                {
                    selectOptions: {
                        conference: '會議',
                        activity: '活動',
                        lesson: '課程',
                        exam: '考試',
                        other: '其他'
                    }
                }
            )),
            (new Field(
                'schedule_content',
                '內容',
                'textarea',
                {}
            )),
            (new Field(
                '',
                '',
                '',
                {}
            )),
            (new Field(
                '',
                '',
                '',
                {}
            )),
            (new Field(
                '',
                '',
                '',
                {}
            )),
            (new Field(
                '',
                '',
                '',
                {}
            )),
        ];
    }
    static text() { return '行程' }
}

export class Form {
    #Name;
    #Text;
    #Fields = [];
    static classes() {
        return {
            'user': User,
            'schedule' : Schedule,
        }
    };

    constructor(name){
        if(Form.classes[name]){
            const page = Form.classes[name];
            this.#Name = name;
            this.#Text = page.text();
            this.#Fields = page.fields();
        }else{
            console.error('Page not found.');
        }
    }
}

class Field {
    #Name;
    #Text;
    #Type;
    #Options = {
        readOnly: false,
        editable: true,
        showOnList: true,
        showOnForm: true,
        selectOptions: [],
        onlyFrontend: false,
        remarks: null,
        sync: true,
    };
    static allowType = ['text', 'textarea', 'number', 'boolean', 'date', 'time', 'datetime', 'select', 'custom'];

    constructor(name, text = null, type = 'string', options = {}) {
        this.#Name = name;
        this.#Text = DataUtil.isEmpty(text) ? name : text;
        this.#Type = Field.allowType.includes(type) ? type : 'text';
        for(let key in options){
            if(key == 'selectOptions'){
                let o = options[key];
                if(typeof o == 'string'){

                }
            }else if(this.#Options[key] !== undefined){
                this.#Options[key] = options[key];
            }
        }
    }

    get Name() {return this.#Name}
    get Text() {return this.#Text}
    get Type() {return this.#Type}
    get Options() {return this.#Options}
}
