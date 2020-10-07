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
        ]
    },
    css: [
        'assets/css/main.css',
        'assets/css/calendar.css',
    ],
    axios: {
        baseURL: '/api/'
    },
    modules: [],
    plugins: [],
    build: {},
    ssr: false,
    telemetry: false
});
