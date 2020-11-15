import API from '../api.js';
import DataUtil from './DataUtil.js';

export default class PageUtil {
    static getPageData(page) {
        const classes = {
            user: User,
            schedule: Schedule,
            announcement: Announcement,
            util: Util,
            place: Place,
            opinion: Opinion,
        }

        page = page.toLowerCase();
        if(!DataUtil.isEmpty(classes[page])){
            return new classes[page]();
        }else{
            console.error("The page is not found.");
        }
    }
}

class User {
    #Name = "user"
    #Title = "用戶資料"
    fields() {
        return [
            (new Field(
                'name',
                '姓名',
                'text',
                {
                    listOrder: 0,
                    formOrder: 0,
                }
            )),
            (new Field(
                'phone',
                '電話',
                'text',
                {
                    listOrder: 1,
                    formOrder: 1,
                }
            )),
            (new Field(
                'email',
                '電子信箱',
                'text',
                {
                    listOrder: 2,
                    formOrder: 2,
                }
            )),
            (new Field(
                'util_id',
                '所屬單位',
                'select',
                {
                    selectOptions: 'util',
                    showOnList: false,
                    formOrder: 3,
                }
            )),
            (new Field(
                'util_name',
                '所屬單位',
                'text',
                {
                    listOrder: 3,
                    showOnForm: false,
                }
            )),
            (new Field(
                'username',
                '帳號',
                'text',
                {
                    editable: false,
                    listOrder: 4,
                    formOrder: 4,
                }
            )),
            (new Field(
                'password',
                '密碼',
                'password',
                {
                    showOnList: false,
                    formOrder: 5,
                    confirmation: true,
                    required: false,
                    addRequired: true,
                    hideOnView: true,
                }
            )),
            (new Field(
                'user_disabled',
                '停用',
                'boolean',
                {
                    trueText: '是',
                    falseText: '否',
                    listOrder: 5,
                    formOrder: 6,
                    default: false,
                    required: false,
                }
            )),
        ]
    }

    get Name() {return this.#Name}
    get Title() {return this.#Title}
}

class Schedule {
    #Name = "schedule"
    fields() {
        return [
            (new Field(
                'schedule_date',
                '日期',
                'date',
                {
                    editable: false,
                    listOrder: 0,
                    formOrder: 0,
                }
            )),
            (new Field(
                'schedule_title',
                '主題',
                'text',
                {
                    editable: false,
                    listOrder: 0,
                    formOrder: 0,
                }
            )),
            (new Field(
                'schedule_from',
                '開始時間',
                'time',
                {
                    listOrder: 0,
                    formOrder: 0,
                }
            )),
            (new Field(
                'place_id',
                '使用地點',
                'select',
                {
                    selectOptions: 'place',
                    listOrder: 0,
                    formOrder: 0,
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
                    },
                    listOrder: 0,
                    formOrder: 0,
                }
            )),
            (new Field(
                'place_disabled',
                '場地狀態',
                'boolean',
                {
                    trueText: '已停用',
                    falseText: '啟用中',
                    trueStyle: {
                        color: 'red'
                    },
                    falseStyle: {
                        color: 'green'
                    },
                    listOrder: 0,
                    formOrder: 0,
                }
            )),
            (new Field(
                'util_name',
                '承辦單位',
                'string',
                {
                    listOrder: 0,
                    formOrder: 0,
                }
            )),
            (new Field(
                'user_name',
                '承辦人',
                'string',
                {
                    listOrder: 0,
                    formOrder: 0,
                }
            )),
            /* (new Field(
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
                'schedule_registrant',
                '登記人',
                'text',
                {
                    remarks: '校外申請者，請填寫主辦單位',
                }
            )),
            (new Field(
                'schedule_content',
                '內容',
                'textarea',
                {}
            )), */
        ];
    }

    get Name() {return this.#Name}
}

class Announcement {
    #Name = "annoucement"
    #Title = "公告"
    fields() {
        return [
            (new Field(
                    'announcement_title',
                    '標題',
                    'text',
                    {
                        listOrder: 2,
                        formOrder: 0,
                    }
              )),
              (new Field(
                    'announcement_type',
                    '公告類型',
                    'select',
                    {
                        selectOptions: {
                            announcement: '公告',
                            update: '系統更新',
                        },
                        listOrder: 1,
                        formOrder: 1,
                        default: 'announcement',
                    }
              )),
              (new Field(
                    'announcement_date',
                    '發佈日期',
                    'date',
                    {
                        listOrder: 0,
                        formOrder: 2,
                    }
              )),
              (new Field(
                    'announcement_content',
                    '內容',
                    'textarea',
                    {
                        showOnList: false,
                        formOrder: 3,
                        required: false,
                    }
              )),
              (new Field(
                    'util_id',
                    '承辦單位',
                    'select',
                    {
                        selectOptions: 'util',
                        listOrder: 4,
                        formOrder: 4,
                    }
              )),
              (new Field(
                    'user_id',
                    '承辦人',
                    'select',
                    {
                        selectOptions: 'user',
                        filter: {
                            foreignField: 'util_id',
                            target: 'util_id',
                            from: 'util'
                        },
                        listOrder: 5,
                        formOrder: 5,
                    }
              )),
              (new Field(
                    'announcement_file',
                    '附件下載',
                    'file',
                    {
                        listOrder: 3,
                        formOrder: 6,
                        required: false,
                    }
              )),
        ];
    }

    get Name() {return this.#Name}
    get Title() {return this.#Title}
}

class Util {
    #Name = "util"
    #Title = "所屬單位資料"
    fields() {
        return [
            (new Field(
                'util_code',
                '所屬單位代碼',
                'text',
                {
                    editable: false
                }
            )),
            (new Field(
                'util_name',
                '所屬單位名稱',
                'text',
                {
                    listOrder: 1,
                    formOrder: 1,
                }
            )),
            (new Field(
                'remarks',
                '備註',
                'text',
                {
                    listOrder: 2,
                    formOrder: 2,
                    required: false,
                }
            )),
            (new Field(
                'util_disabled',
                '停用',
                'boolean',
                {
                    trueText: '是',
                    falseText: '否',
                    listOrder: 3,
                    formOrder: 3,
                    required: false,
                    default: false,
                }
            )),
        ];
    }
    get Name() {return this.#Name}
    get Title() {return this.#Title}
}

class Place {
    #Name = "place"
    #Title = "場地基本資料"
    fields() {
        return [
            (new Field(
                'place_code',
                '場地代碼',
                'text',
                {
                    editable: false,
                }
            )),
            (new Field(
                'place_name',
                '場地名稱',
                'text',
                {
                    listOrder: 1,
                    formOrder: 1,
                }
            )),
            (new Field(
                'remarks',
                '備註',
                'text',
                {
                    listOrder: 2,
                    formOrder: 2,
                    required: false,
                }
            )),
            (new Field(
                'place_disabled',
                '停用',
                'boolean',
                {
                    trueText: '是',
                    falseText: '否',
                    listOrder: 3,
                    formOrder: 3,
                    default: false,
                    required: false,
                }
            )),
        ];
    }

    get Name() {return this.#Name}
    get Title() {return this.#Title}
}

class Opinion {
    #Name = "user"
    #Title = "用戶資料"
    get Name() {return this.#Name}
    get Title() {return this.#Title}
}

class Field {
    #Name;
    #Text;
    #Type;
    #Options = {
        readOnly: false,    // 是否唯獨
        editable: true,     // 新增可以輸入，若為false則修改不能動
        showOnList: true,   // 是否出現在列表畫面
        showOnForm: true,   // 是否出現在表單畫面
        hideOnView: false,
        selectOptions: [],  // 下拉選項：如果是動態下拉，就直接寫table名稱'user', 若為固定下拉，則寫Array: [1,2, '字串']
        filter: {},
        remarks: null,      // 顯示於欄位下方的備註文字
        trueText: null,     // boolean型態，出現在列表時，若為True，則顯示的文字
        falseText: null,    // boolean型態，出現在列表時，若為False，則顯示的文字
        trueStyle: {},
        falseStyle: {},
        listOrder: 0,       // 列表欄位排序
        formOrder: 0,       // 表單欄位排序
        required: true,     // 是否必填
        addRequired: false,
        editRequired: false,
        default: null,
        confirmation: false,
    };
    static allowType = ['text', 'password', 'textarea', 'number', 'boolean', 'date', 'time', 'datetime', 'select', 'file', 'custom'];

    constructor(name, text = null, type = 'string', options = {}) {
        this.#Name = name;
        this.#Text = DataUtil.isEmpty(text) ? name : text;
        this.#Type = Field.allowType.includes(type) ? type : 'text';
        for(let key in options){
            if(key == 'selectOptions'){
                let o = options[key];
                // console.log(o);
                /* if(typeof o == 'string'){
                    this.#Options.selectOptions.push(o);
                } */
                this.#Options[key] = options[key];
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
