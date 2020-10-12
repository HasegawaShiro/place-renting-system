<template>
    <div class="nchu main">
        <Form
            v-if="isLogin"
            :form-mode="formMode"
            :form-data="formData"
        ></Form>
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
                        :data-tooltip="CONSTANTS.TEXT.menu"
                        @click="toggleSidebar()"
                    ><i class="list icon"></i></a>
                    <n-link
                        to="/"
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
                        <div class="ts icon content grid">
                            <div class="icon three wide column"><i class="big user circle icon"></i></div>
                            <div class="thirteen wide column">
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
            <slot name="content"></slot>
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
                util: null,
                mail: null,
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
            }
        };
    },
    mounted() {
        API.sendRequest("/api/user").then(response => {
            this.user = response.data.user;
        }).catch(e => {
            this.user = this.guest;
        });
    },
    computed: {
        isLogin() {
            return this.user.id !== 0 && this.user.name !== 'Guest' && !DataUtil.isAnyEmpty(this.user.id, this.user.name);
            // return false;
        },
    },
    methods: {
        toggleSidebar() {
            ts('.left.sidebar').sidebar('toggle');
        },
        openLoginModal() {
            ts('.login.modal').modal({
                onApprove: () => {console.log('A')},
                onDeny: () => {console.log('D')},
            }).modal('show');
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
        },
        logout() {
            this.$axios.get("/api/logout").then(async _ => {
                this.user = this.guest;
                API.refreshCSRFToken();
            }).catch(e => console.log(e));
        },

    },
    props: {
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
        formData: function(newVal, oldVal){
            console.log("data changed.")
            this.formData = newVal;
        }
    },
}
</script>

<style>
    .nchu.main>.pusher {
        overflow-x: hidden;
    }

    /* sidebar */
    .nchu.main .sidebar {
        color: white;
        background-color:  rgb(8, 138, 120);
    }
    .nchu.main .sidebar .index {
        text-align: center;
        font-size: 4em;
        font-weight: bolder;
        display: block;
    }

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

    @media(hover: hover) and (pointer: fine) {
        .nchu.main .login.modal #forget-password:hover {
            color: rgb(46, 119, 255);
            cursor: pointer;
        }
        .nchu.main header .text.button:hover {
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
