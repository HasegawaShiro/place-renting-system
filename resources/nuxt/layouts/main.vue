<template>
    <div class="nchu main">
        <slot name="other" ref="other"></slot>
        <div class="ts static left sidebar inverted vertical menu" id="main-sidebar">
            <div class="item">
                中興大學
                <div class="menu">
                    <a class="item" target="_blank" href="https://www.nchu.edu.tw/">官網</a>
                    <a class="item" target="_blank" href="https://www.iciil.nchu.edu.tw/">創產學院</a>
                </div>
            </div>
            <!-- <a class="item" target="_blank" href="https://www.nchu.edu.tw/">官網</a>
            <a class="item" target="_blank" href="https://www.iciil.nchu.edu.tw/">創產學院</a> -->
            <a class="item">公告</a>
            <a class="item">使用手冊</a>

            <a class="bottom item" @click="closeSidebar">關閉選單</a>
        </div>
        <div class="squeezable pusher" id="sidebar-pusher">
            <Form
                v-if="isLogin"
                :page="page"
                :form-mode="formMode"
                :form-data="formData"
                ref="form"
            ></Form>
            <div class="ts dimmer" id="global-loading">
                <div class="ts loader"></div>
            </div>
            <header>
                <div class="ts pointing secondary large icon menu">
                    <a
                        class="item"
                        style="z-index: 2;"
                        id="menu-button"
                        :data-tooltip="CONSTANTS.TEXT.menu"
                        @click="toggleSidebar()"
                    ><i class="list icon"></i></a>
                    <n-link
                        to="/"
                        id="menu-index"
                        class="item nchu header title"
                        style="z-index: 2;"
                        :data-tooltip="CONSTANTS.TEXT.index"
                    >{{CONSTANTS.TEXT.title}}</n-link>
                    <div class="right menu">
                        <div v-if="isLogin" class="item">{{user.name}}, {{CONSTANTS.TEXT.greet}}</div>
                        <a
                            class="item"
                            style="z-index: 2;"
                            :data-tooltip="CONSTANTS.TEXT.opinion"
                        ><i class="mail outline icon"></i></a>
                        <template v-if="!isLogin">
                            <div class="item">
                                <span
                                    class="text button"
                                    id="login-button"
                                    @click="openLoginModal()"
                                >{{CONSTANTS.TEXT.login}}</span>
                                ｜
                                <span
                                    class="text button"
                                    id="register-button"
                                >{{CONSTANTS.TEXT.register}}</span>
                            </div>
                        </template>
                        <template v-else-if="isLogin">
                            <a
                                href=""
                                class="item"
                                style="z-index: 2;"
                                :data-tooltip="CONSTANTS.TEXT.profile"
                            ><i class="user outline icon"></i></a>
                            <a
                                class="item"
                                style="z-index: 2;"
                                :data-tooltip="CONSTANTS.TEXT.logout"
                                @click="logout()"
                            ><i class="log out icon"></i></a>
                        </template>
                    </div>
                </div>
                <div class="ts modals dimmer">
                    <dialog
                        class="ts tiny closable modal login"
                    >
                        <div class="ts inverted negative segment" v-if="authExpired">
                            <p>{{MESSAGES['auth-expired']}}</p>
                        </div>
                        <div class="ts icon content">
                            <!-- <div class="icon three wide column large device only">
                                <i class="big user circle icon"></i>
                            </div> -->
                            <div class="">
                                <form class="ts form" @keydown.enter="login()">
                                    <div class="fluid field">
                                        <label>{{CONSTANTS.TEXT.username}}</label>
                                        <input type="text" v-model="input.username" >
                                    </div>
                                    <div class="fluid field">
                                        <label>{{CONSTANTS.TEXT.password}}</label>
                                        <input type="password" v-model="input.password">
                                        <small id="forget-password">忘記密碼?</small>
                                    </div>
                                    <button type="button" class="ts login fluid button" @click="login()">
                                        {{CONSTANTS.TEXT.login}}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </dialog>
                </div>
            </header>
            <slot @hook:created="created" name="content" ref="content"></slot>
            <!-- <slot name="loading"></slot> -->
        </div>
        <div class="ts bottom left snackbar">
            <div class="content">
                {{snackbar.info}}
            </div>
        </div>
        <div class="ts bottom left success snackbar">
            <div class="content">
                {{snackbar.success}}
            </div>
        </div>
        <div class="ts bottom left error snackbar">
            <div class="content">
                {{snackbar.error}}
            </div>
        </div>
    </div>
</template>

<script>
import CONSTANTS from '../constants.js'
import Form from '../layouts/form.vue'
import DataUtil from '../utils/DataUtil.js';
import API from '../api.js';

export default {
    components: {
        Form,
    },
    data() {
        return {
            CONSTANTS: CONSTANTS.main,
            MESSAGES: CONSTANTS.messages,
            user: {},
            guest:{
                id: 0,
                name: 'Guest',
                util: {},
                email: null,
                phone: null,
            },
            input: {
                username: null,
                password: null,
            },
            snackbar: {
                info: null,
                success: null,
                error: null,
            },
            authExpired: false,
        };
    },
    beforeMount() {

    },
    async mounted() {
        window.mainLayout = this;
        window.globalLoading = {
            $el: document.querySelector("#global-loading"),
            loading() {
                const el = window.globalLoading.$el;
                if(!el.classList.contains("active")) el.classList.add("active");
            },
            unloading() {
                const el = window.globalLoading.$el;
                if(el.classList.contains("active")) el.classList.remove("active");
            },
        };
        API.sendRequest("/api/user","get",null,{doNotRelogin: true}).then(response => {
            this.user = response.data.user;
            this.$store.commit("userStore/set", this.user);
        }).catch(e => {
            this.user = this.guest;
            this.$store.commit("userStore/set", this.guest);
        });

        const selects = DataUtil.deepClone(CONSTANTS.common.selects);
        for(let s in selects) {
            if(typeof selects[s] == "object" && selects[s].constructor == Object){
                if(DataUtil.isEmpty(selects[s])){
                    selects[s] = await API.getReferenceSelect(s);
                }
            }
        }
        window.globalSelects = selects;
    },
    computed: {
        isLogin() {
            return this.user.id !== 0 && this.user.name !== 'Guest' && !DataUtil.isAnyEmpty(this.user.id, this.user.name);
        },
    },
    methods: {
        toggleSidebar() {
            const sidebar = document.querySelector('#main-sidebar');
            const pusher = document.querySelector('#sidebar-pusher');
            const mediaQueryList = window.matchMedia('(max-width: 767px)');

            function responsivePusher(e) {
                if (e.matches) {
                    if(pusher.classList.contains("squeezable")) pusher.classList.remove("squeezable");
                    // if(!sidebar.classList.contains("overlapped")) sidebar.classList.add("overlapped");

                    ts('#main-sidebar').sidebar({
                        dimPage: true,
                        closable: true,
                    }).sidebar('toggle');
                    // ts('#main-sidebar').sidebar('toggle');
                } else {
                    if(!pusher.classList.contains("squeezable")) pusher.classList.add("squeezable");
                    // if(sidebar.classList.contains("overlapped")) sidebar.classList.remove("overlapped");
                    ts('#main-sidebar').sidebar('toggle');
                }
            }
            responsivePusher(mediaQueryList);
            // mediaQueryList.addListener(responsivePusher);
        },
        closeSidebar() {
            ts('#main-sidebar').sidebar('hide');
        },
        openLoginModal() {
            ts('.login.modal').modal('show');
        },
        closeLoginModal() {
            ts('.login.modal').modal('hide');
        },
        async login() {
            let loginFormData = new FormData();
            loginFormData.set('username',this.input.username);
            loginFormData.set('password',this.input.password);
            await API.sendRequest("/api/login", "post", loginFormData, {onlyData: true}).then(data => {
                this.user = data.user;
                this.closeLoginModal();
                ts('.success.snackbar').snackbar({
                    content: DataUtil.parseResponseMessages(data.message)
                });
                this.$store.commit("userStore/set", this.user);
            }).catch(e => {
                try {
                    let errorData = e.response.data;
                    ts('.error.snackbar').snackbar({
                        content: DataUtil.parseResponseMessages(errorData.message)
                    });
                } catch (error) {
                    ts('.error.snackbar').snackbar({
                        content: DataUtil.getMessage('login-error')
                    });
                }
            });
            this.input.username = null;
            this.input.password = null;
            this.authExpired = false;
        },
        logout() {
            this.$axios.get("/api/logout").then(async _ => {
                location.reload();
            }).catch(e => {});
        },
        relogin() {
            this.user = this.guest;
            this.authExpired = true;
            this.openLoginModal();
        },
        showSnackbar(type, messages){
            ts(`.${type}.snackbar`).snackbar({
                content: DataUtil.parseResponseMessages(messages),
                hoverStay: true,
            });
        },
        auth(user_id) {
            return this.user.id === 1 || this.user.id === user_id;
        },
    },
    props: {
        'page': {
            type: String,
        },
        'has-form': {
            type: Boolean,
            default() {
                return false;
            }
        },
        'form-mode': {
            type: String,
            default() {
                return "universal";
            },
        },
        'form-data': {
            type: Object,
        }
    },
    watch: {
    },
}
</script>

<style>
    .nchu.main>.pusher {
        overflow-x: hidden;
    }
    #global-loading {
        position: fixed;
    }

    /* sidebar */
    .nchu.main .sidebar {
        color: white;
        background-color:  rgb(8, 138, 120);
    }
    .nchu.main .sidebar>.item {
        border-bottom: 1px solid rgba(221, 221, 221, 0.363) !important;
    }
    .nchu.main .sidebar .item .menu a.item {
        font-size: .94555em;
    }
    /* .nchu.main .sidebar .menu .item{
        border-bottom: 0px !important;
    } */

    /* .nchu.main .login.modal .icon.column, .nchu.main .login.modal form {
        display: inline-grid;
    } */
    .nchu.main .login.modal .icon.column {
        font-size: 2em;
        padding-top: 3.3em;
        padding-left: 0px;
        border-right: 1px solid #ccc;
    }
    .nchu.main .login.modal .login.button {
        color: white;
        background-color:  rgb(8, 138, 120);
        margin-top: 1em;
    }
    .nchu.main .login.modal #forget-password {
        float: right;
        margin-left: auto;
    }

    .nchu.main .success.snackbar {
        background-color:  rgb(83, 145, 64);
        color: white;
        border-left: .5em solid rgb(53, 94, 43);
    }
    .nchu.main .error.snackbar {
        background-color:  rgb(145, 64, 64);
        color: white;
        border-left: .5em solid rgb(94, 43, 43);
    }
    @media only screen and (max-width:767px){
        #menu-index {
            padding-left: 0px;
            padding-right: 0px;
        }
    }
    @media(hover: hover) and (pointer: fine) {
        .nchu.main .sidebar a.item:hover {
            background-color:  rgb(8, 112, 98) !important;
        }
        .nchu.main header .text.button:hover, .nchu.main .login.modal #forget-password:hover {
            color: rgb(8, 138, 120);
            cursor: pointer;
        }
        .nchu.main .login.modal .login.button:hover{
            color: white !important;
            background-color:  rgb(8, 138, 120) !important;
        }
        .nchu.main .login.modal .login.button:hover::before {
            content: "";
            position: absolute;
            left: 0px;
            top: 0px;
            z-index: 99;
            width: 100%;
            height: 100%;
            cursor: pointer;
            background-color: rgba(255, 255, 255, 0.295)
        }
    }
    .nchu.main .login.modal .login.button:active::before {
        content: "";
        position: absolute;
        left: 0px;
        top: 0px;
        z-index: 99;
        width: 100%;
        height: 100%;
        cursor: pointer;
        background-color: rgba(10, 10, 10, 0.384)
    }

</style>
