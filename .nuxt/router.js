import Vue from 'vue'
import Router from 'vue-router'
import { interopDefault } from './utils'
import scrollBehavior from './router.scrollBehavior.js'

const _a046be32 = () => interopDefault(import('..\\resources\\nuxt\\pages\\announcements.vue' /* webpackChunkName: "pages/announcements" */))
const _88bacdb4 = () => interopDefault(import('..\\resources\\nuxt\\pages\\opinions.vue' /* webpackChunkName: "pages/opinions" */))
const _4661f201 = () => interopDefault(import('..\\resources\\nuxt\\pages\\places.vue' /* webpackChunkName: "pages/places" */))
const _753b303a = () => interopDefault(import('..\\resources\\nuxt\\pages\\users.vue' /* webpackChunkName: "pages/users" */))
const _12c123e8 = () => interopDefault(import('..\\resources\\nuxt\\pages\\utils.vue' /* webpackChunkName: "pages/utils" */))
const _079072c7 = () => interopDefault(import('..\\resources\\nuxt\\pages\\errors\\403.vue' /* webpackChunkName: "pages/errors/403" */))
const _7ff88dad = () => interopDefault(import('..\\resources\\nuxt\\pages\\index.vue' /* webpackChunkName: "pages/index" */))

// TODO: remove in Nuxt 3
const emptyFn = () => {}
const originalPush = Router.prototype.push
Router.prototype.push = function push (location, onComplete = emptyFn, onAbort) {
  return originalPush.call(this, location, onComplete, onAbort)
}

Vue.use(Router)

export const routerOptions = {
  mode: 'history',
  base: decodeURI('/'),
  linkActiveClass: 'nuxt-link-active',
  linkExactActiveClass: 'nuxt-link-exact-active',
  scrollBehavior,

  routes: [{
    path: "/announcements",
    component: _a046be32,
    name: "announcements"
  }, {
    path: "/opinions",
    component: _88bacdb4,
    name: "opinions"
  }, {
    path: "/places",
    component: _4661f201,
    name: "places"
  }, {
    path: "/users",
    component: _753b303a,
    name: "users"
  }, {
    path: "/utils",
    component: _12c123e8,
    name: "utils"
  }, {
    path: "/errors/403",
    component: _079072c7,
    name: "errors-403"
  }, {
    path: "/",
    component: _7ff88dad,
    name: "index"
  }, {
    path: "/__laravel_nuxt__",
    component: _7ff88dad,
    name: "__laravel_nuxt__"
  }],

  fallback: false
}

export function createRouter () {
  return new Router(routerOptions)
}
