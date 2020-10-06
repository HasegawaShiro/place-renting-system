<template>
<div>

    <div class="nchu main">
        <div class="ts static left sidebar">
            <div class="index">NCHU</div>
            <div class="ts divider"></div>
            <div class="ts divider"></div>
            <div class="ts divider"></div>
            <div class="ts divider"></div>
            <div class="ts divider"></div>
        </div>
        <div class="squeezable pusher">
            <header>
                <div class="ts pointing secondary large icon menu">
                    <a
                        class="item"
                        style="z-index: 2;"
                        id="menu-button"
                        data-tooltip="選單"
                    ><i class="list icon"></i></a>
                    <a
                        class="item nchu header title"
                        style="z-index: 2;"
                        data-tooltip="首頁"
                    >中興大學場地借用系統</a>
                    <div class="right menu">
                        <!-- <div class="item">OOO，您好</div> -->
                        <a
                            href=""
                            class="item"
                            style="z-index: 2;"
                            data-tooltip="意見反饋"
                        ><i class="mail outline icon"></i></a>
                        <div class="item">登入｜註冊</div>
                        <!-- <a
                            href=""
                            class="item"
                            style="z-index: 2;"
                            data-tooltip="個人檔案"
                        ><i class="user outline icon"></i></a>
                        <a
                            href=""
                            class="item"
                            style="z-index: 2;"
                            data-tooltip="登出"
                        ><i class="log out icon"></i></a> -->
                    </div>
                </div>
            </header>
            <Calendar></Calendar>
        </div>
    </div>
    <div class="nchu add">
        <i class="circular add icon"></i>
    </div>
</div>
</template>

<script>
import Calendar from '../components/calendar/calendar.vue'
export default {
    components : {
        Calendar
    },
    methods:{

    }
}
</script>

<style>

</style>
