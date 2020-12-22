export default {
    main: {
        TEXT: {
            menu: '選單',
            index: '首頁',
            opinion: '意見反應',
            title: '興大創產學院 場地借用系統',
            login: '登入',
            register: '註冊',
            greet: '您好',
            profile: '個人檔案',
            logout: '登出',
            username: '帳號',
            password: '密碼',
        },
        MENU: {
            utils: {
                text: '所屬單位資料',
                permission: 'admin',
            },
            users: {
                text: '用戶資料',
                permission: 'admin',
            },
            places: {
                text: '場地基本資料',
                permission: 'admin',
            },
            '': {
                text: '場地使用查詢',
                permission: 'all',
            },
            announcements: {
                text: '公告',
                permission: 'all',
            },
            opinions: {
                text: '意見反應',
                permission: 'admin',
            },
            guides: {
                text: '使用手冊',
                target: '_blank',
                url: 'api/download/guide/0/guide.pdf',
                permission: 'all',
            }
        },
        FOOTER: {
            top: '國立中興大學創產學院',
            left1: '402 台中市南區興大路145號 綜合教學大樓 9樓934室',
            left2: 'Rm. 934, 9F., No. 145, Xingda Rd., South Dist., Taichung City 402, Taiwan (R.O.C.)',
            right1: '電話：04-22840455轉2203',
            right2: 'E-mail：xiang@nchu.edu.tw',
            bottom: 'Copyright © '+(new Date()).getFullYear()+' 肆零玖數位工程有限公司. All rights reserved.'
        }
    },
    user: {
        FORM_TEXT: {
            title: {
                add: "新增用戶",
                edit: "編輯用戶",
                view: "查看用戶"
            }
        }
    },
    schedule: {
        TEXT: {
            selected_date: '當前所選日期',
            today_event: '本日活動',
            year: '年',
            month: '月',
            day: '日',
        },
        FORM_TEXT: {
            title: {
                add: '新增行程',
                edit: '編輯行程',
                view: '查看行程'
            },
            schedule_title: '主題',
            schedule_date: '日期',
            schedule_from: '開始時間',
            schedule_to: '結束時間',
            fullday: '全天',
            schedule_repeat: '是否重複',
            repeat: '重複',
            schedule_repeat_method: '重複方式',
            keep: '持續',
            cycle: '週期',
            schedule_end: '結束日期',
            at: '於',
            times: '週(包含本週)',
            place_id: '使用地點',
            schedule_registrant: '登記人',
            schedule_type: '借用型態',
            schedule_content: '內容',
            user_id: '承辦人',
            util: '承辦單位',
            phone: '電話',
            mail: '電子信箱',
            schedule_contact: '聯絡人',
            schedule_url: '相關網址'
        },
        selects:{
            types: {
                conference: '會議',
                activity: '活動',
                lesson: '課程',
                exam: '考試',
                other: '其他'
            },
        },
        messages: {
            'repeat-edit': '編輯週期性資料',
            'repeat-delete': '刪除週期性資料',
            'repeat-all': '本項以後的全部活動(不包含已過期資料)',
            'repeat-one': '僅限這項活動'
        },
    },
    place: {
        FORM_TEXT: {
            title: {
                add: "新增場地",
                edit: "編輯場地",
                view: "查看場地"
            }
        }
    },
    announcement: {
        FORM_TEXT: {
            title: {
                add: "新增公告",
                edit: "編輯公告",
                view: "查看公告"
            }
        }
    },
    util: {
        FORM_TEXT: {
            title: {
                add: "新增單位",
                edit: "編輯單位",
                view: "查看單位"
            }
        }
    },
    opinion: {
        FORM_TEXT: {
            title: {
                add: "新增意見",
                edit: "編輯意見",
                view: "查看意見"
            }
        }
    },
    calendar:{
        MODE_BUTTON: {
            month: '月表',
            week: '週表',
            list: '列表'
        },
        HEADER_TEXT: {
            title: '場地使用查詢',
            date: '日期',
            place: '場地'
        },
        DAY_TEXT: ['一','二','三','四','五','六','日'],
        selects: {
            TEXT: {
                type: '借用型態',
                place: '場地',
                util: '承辦單位',
                user: '承辦人'
            },
        },
        messages: {
            'repeat-edit': '編輯週期性資料',
            'repeat-delete': '刪除週期性資料',
            'repeat-all': '全部活動(不包含已過期資料)',
            'repeat-one': '僅限這項活動',
            'month-selector': '請選擇月份',
            'week-selector': '請選擇週次',
            'from-to-selector': '請選擇日期起迄',
            'from': '起',
            'to': '迄',
        },
    },
    messages: {
        'login-success': '登入成功',
        'user-disabled': '此用戶已遭停用',
        'login-error': '帳號或密碼錯誤',
        'auth-expired': '登入狀態過期，請重新登入',
        'cancel-confirmation': '取消將不保留已輸入或修改之資料，確定取消嗎？',
        'save-success': '儲存成功',
        'unknown-error': '發生未知錯誤',
        'contact-maintenance': '，請洽維護人員',
        'delete-confirmation': '確定要刪除此資料嗎？',
        'delete-success': '刪除成功',
        'delete-permission-denied': '您沒有權限刪除此資料',
        'permission-denied': '您沒有權限進行此操作',
        'data-not-found': '查無此資料',
        'data-is-referenced': '資料已被引用，無法刪除',
    },
    common: {
        TEXT: {
            save: '儲存',
            cancel: '取消',
        },
        selects: {
            type: {
                conference: '會議',
                activity: '活動',
                lesson: '課程',
                exam: '考試',
                other: '其他'
            },
            user: {},
            place: {},
            util: {},
        },
    }
}
