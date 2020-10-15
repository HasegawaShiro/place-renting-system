const laravelNuxt = require("laravel-nuxt");

const baseUrl = "/NCHU/"

module.exports = laravelNuxt({
    /* router: {
        base: baseUrl
    }, */
    head: {
        script: [
            {
                src: 'https://cdnjs.cloudflare.com/ajax/libs/tocas-ui/2.3.3/tocas.js'
            }
        ],
        link: [
            {
                rel: 'stylesheet',
                href: 'https://cdnjs.cloudflare.com/ajax/libs/tocas-ui/2.3.3/tocas.css'
            }
        ],
        meta: [
            {
                name: 'viewport',
                content: 'width=device-width, initial-scale=1.0',
                charset: 'UTF-8',
            }
        ],
    },
    css: [
        'assets/css/main.css',
    ],
    axios: {
        baseURL: '/api/'
    },
    modules: [],
    build: {},
    ssr: false,
    telemetry: false
});
