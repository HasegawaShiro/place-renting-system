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
            <template
                v-for="(p, page) in showMenu"
            >
                <div
                    v-if="typeof p.sub === 'object'"
                    class="item"
                    :key="`menu-${page}`"
                >
                    {{p.text}}
                    <div class="menu">
                        <template
                            v-for="(s, sub) in p.sub"
                        >
                            <n-link
                                v-if="s.url === undefined"
                                class="item"
                                :key="`menu-${sub}`"
                                :to="`/${sub}`"
                            >{{s.text}}</n-link>
                            <a
                                v-else
                                class="item"
                                :key="`menu-${sub}`"
                                :href="`/${s.url}`"
                            >{{s.text}}</a>
                        </template>
                    </div>
                </div>
                <n-link
                    v-else
                    class="item"
                    :target="p.target === undefined ? '' : p.target"
                    :key="`menu-${page}`"
                    :to="`/${p.url === undefined ? page : p.url}`"
                >{{p.text}}</n-link>
            </template>
            <a class="bottom item" @click="closeSidebar">關閉選單</a>
        </div>
        <div class="squeezable pusher" id="sidebar-pusher">
            <CustomForm
                v-if="hasForm"
                :page="page"
                :form-mode="formMode"
                :form-data="formData"
                :show-add="showAddMutation"
                ref="form"
            ></CustomForm>
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
                        <CustomForm
                            page="opinion"
                            ref="add-opinion"
                            key="add-opinion"
                            form-name="add-opinion"
                            :show-add="false"
                        ></CustomForm>
                        <a
                            class="item"
                            style="z-index: 2;"
                            :data-tooltip="CONSTANTS.TEXT.opinion"
                            @click="addOpinion()"
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
                                    @click="register()"
                                >{{CONSTANTS.TEXT.register}}</span>
                            </div>
                            <CustomForm
                                page="user"
                                ref="register"
                                form-name="register"
                                :show-add="false"
                                @saved="registerSaved()"
                            ></CustomForm>
                        </template>
                        <template v-else-if="isLogin">
                            <CustomForm
                                page="user"
                                ref="profile"
                                key="profile"
                                form-name="profile"
                                :show-add="false"
                                @saved="profileSaved()"
                            ></CustomForm>
                            <a
                                href=""
                                class="item"
                                style="z-index: 2;"
                                :data-tooltip="CONSTANTS.TEXT.profile"
                                @click.prevent="editProfile()"
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

                <!-- login & register form -->
                <template v-if="!isLogin">
                    <div class="ts modals dimmer">
                        <dialog
                            class="ts tiny closable modal login"
                        >
                            <div class="ts inverted negative segment" v-if="authExpired">
                                <p>{{MESSAGES['auth-expired']}}</p>
                            </div>
                            <div class="ts icon content">
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
                                        <button
                                            type="button"
                                            class="ts login fluid button"
                                            :class="{loading: loginLoading, disabled: loginLoading}"
                                            @click="login()"
                                        >
                                            {{CONSTANTS.TEXT.login}}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </dialog>
                    </div>
                </template>

            </header>
            <content>
                <slot @hook:mounted="mounted" name="content" ref="content"></slot>
            </content>
            <footer>
                <div id="footer-top">
                    {{CONSTANTS.FOOTER.top}}<br>
                </div>
                <div class="ts grid">
                    <div class="eight wide column" id="footer-left">
                        {{CONSTANTS.FOOTER.left1}}<br>
                        {{CONSTANTS.FOOTER.left2}}<br>
                    </div>
                    <div class="eight wide column" id="footer-right">
                        {{CONSTANTS.FOOTER.right1}}<br>
                        {{CONSTANTS.FOOTER.right2}}<br>
                    </div>
                </div>
                <div id="footer-bottom">
                    {{CONSTANTS.FOOTER.bottom}}
                </div>
            </footer>
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
import CustomForm from '../layouts/form.vue'
import DataUtil from '../utils/DataUtil.js';
import API from '../api.js';

export default {
    components: {
        CustomForm,
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
            loginLoading: false,
            showAddMutation: true,
        };
    },
    async mounted() {
        const that = this;

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
        window.globalLoading.loading();
        await API.sendRequest("/api/auth","get",null,{doNotRelogin: true}).then(response => {
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

        this.$emit("mounted");
        this.showAddMutation = this.showAdd;
        if(this.$slots['content'][0].componentInstance != undefined && typeof this.$slots['content'][0].componentInstance.mainLayoutLoaded === 'function') this.$slots['content'][0].componentInstance.mainLayoutLoaded();
        // window.globalLoading.unloading();
    },
    computed: {
        isLogin() {
            return this.user.id !== 0 && this.user.name !== 'Guest' && !DataUtil.isAnyEmpty(this.user.id, this.user.name);
        },
        showMenu() {
            let result = {};

            for(const [k, page] of Object.entries(CONSTANTS.main.MENU)) {
                let show = true;

                if(page.permission === 'admin' && this.user.id !== 1) show = false;
                if(show) {
                    result[k] = {...page};
                    if(!DataUtil.isEmpty(page.sub)) {
                        let sub = {};
                        for(const [key, value] of Object.entries(page.sub)) {
                            if(!DataUtil.isEmpty(page.prefix)) {
                                value.url = `${page.prefix}/${DataUtil.isEmpty(value.url) ? key : sub.url}`;
                            }
                            sub[key] = {...value};
                        }
                        result[k].sub = {...sub};
                    }
                }
            }

            return result;
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
            this.loginLoading = true;
            let loginFormData = new FormData();
            loginFormData.set('username',this.input.username);
            loginFormData.set('password',this.input.password);
            await API.sendRequest("/api/login", "post", loginFormData, {
                onlyData: true,
                doNotRelogin: true,
            }).then(data => {
                this.user = data.user;
                ts('.success.snackbar').snackbar({
                    content: DataUtil.parseResponseMessages(data.message)
                });
                this.$store.commit("userStore/set", this.user);
                location.reload();
                // this.closeLoginModal();
            }).catch(e => {
                this.loginLoading = false;
                try {
                    let errorData = e.response.data;

                    if(errorData.message == "The given data was invalid."){
                        ts('.error.snackbar').snackbar({
                            content: DataUtil.getMessage('login-error')
                        });
                    } else {
                        ts('.error.snackbar').snackbar({
                            content: DataUtil.parseResponseMessages(errorData.message)
                        });
                    }
                } catch (error) {
                    console.log(JSON.stringify(e));
                    this.showSnackbar('error', ['unknown-error','contact-maintenance'])
                }

            });
            this.input.username = null;
            this.input.password = null;
            this.authExpired = false;
        },
        async register() {
            this.$refs["register"].openModal('add');
        },
        async registerSaved() {},
        async addOpinion() {
            this.$refs['add-opinion'].openModal('add');
        },
        async editProfile() {
            const auth = await API.sendRequest(`/api/data/user/${this.user.id}`);
            if(!DataUtil.isEmpty(auth.data)) {
                this.$refs["profile"].openModal('edit', auth.data.datas[0]);
            } else {
                this.showSnackbar('error', ['unknown-error','contact-maintenance'])
            }
        },
        async profileSaved() {
            window.globalLoading.loading();
            await API.sendRequest("/api/auth","get",null,{doNotRelogin: true}).then(response => {
                this.user = response.data.user;
                this.$store.commit("userStore/set", this.user);
            }).catch(e => {
                this.user = this.guest;
                this.$store.commit("userStore/set", this.guest);
            });
            window.globalLoading.unloading();
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
            if(!DataUtil.isEmpty(messages)) {
                ts(`.${type}.snackbar`).snackbar({
                    content: DataUtil.parseResponseMessages(messages),
                    hoverStay: true,
                });
            }
        },
        auth(user_id) {
            return this.user.id === 1 || this.user.id === user_id;
        },
        contentLoaded() {
            window.globalLoading.unloading();
            window.contentLoaded = true;
        }
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
        },
        'show-add': {
            type: Boolean,
            default() {
                return true;
            },
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
    #sidebar-pusher>header {
        position: sticky;
        top: 0px;
        z-index: 5;
        background-color: rgba(8, 80, 70, 0.712);
        border: none;
        /* box-shadow: -3px -4px 14px black !important; */
    }
    header .menu {
        border: none !important;
    }
    header .menu a, header .menu i, header .menu div:not(.header) {
        color: white !important;
    }
    content {
        min-height: 100vh;
        display: block;
    }
    footer {
        width: 100%;
        position: relative;
        bottom: 0px;
        background-color:  rgb(8, 138, 120);
        color: white;
        padding: 1.5em;
    }
    /* footer #footer-top, footer #footer-bottom {
        text-align: center;
    } */

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
        .nchu.main header .text.button:hover {
            /* color: rgb(138, 138, 138); */
            cursor: pointer;
        }
        .nchu.main .login.modal #forget-password:hover {
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
