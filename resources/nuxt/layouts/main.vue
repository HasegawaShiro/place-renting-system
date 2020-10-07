<template>
    <div class="nchu main">
        <Form
            v-if="isLogin"
            :form-mode="formMode"
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
                    <a
                        class="item nchu header title"
                        style="z-index: 2;"
                        :data-tooltip="CONSTANTS.TEXT.index"
                    >{{CONSTANTS.TEXT.title}}</a>
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
                                href=""
                                class="item"
                                style="z-index: 2;"
                                :data-tooltip="CONSTANTS.TEXT.logout"
                            ><i class="log out icon"></i></a>
                        </template>
                    </div>
                </div>
                <div class="ts modals dimmer">
                    <dialog
                        class="ts tiny closable modal login"
                    >
                        <div class="ts icon content grid">
                            <div class="icon two wide column"><i class="big user circle icon"></i></div>
                            <div class="fourteen wide column">
                                <form class="ts form">
                                    <div class="inline fluid field">
                                        <label>{{CONSTANTS.TEXT.username}}</label>
                                        <input type="text" v-model="input.username">
                                    </div>
                                    <div class="inline fluid field">
                                        <label>{{CONSTANTS.TEXT.password}}</label>
                                        <input type="password" v-model="input.password">
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="actions">
                            <button type="button" class="ts login button" @click="login()">
                                {{CONSTANTS.TEXT.login}}
                            </button>
                        </div>
                    </dialog>
                </div>
            </header>
            <slot name="content"></slot>
        </div>
    </div>
</template>

<script>
import CONSTANTS from '../constants.js'
import Form from '../layouts/form.vue'
import DataUtil from '../utils/DataUtil.js';

export default {
    components: {
        Form,
    },
    data() {
        return {
            CONSTANTS: CONSTANTS.main,
            user: {
                id: 0,
                name: 'Guest',
                util: null,
                mail: null,
                phone: null,
            },
            input: {
                username: null,
                password: null,
            }
        };
    },
    mounted() {
        console.log(`Login: ${this.isLogin}`);
    },
    computed: {
        isLogin() {
            // return this.user.id !== 0 && this.user.name !== 'Guest' && !DataUtil.isAnyEmpty(this.user.id, this.user.name);
            return false;
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
        login() {

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
        padding-top: 2.5em;
    }
    .nchu.main .login.modal .login.button {
        color: white;
        background-color:  rgb(8, 138, 120);
    }

    @media(hover: hover) and (pointer: fine) {
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
