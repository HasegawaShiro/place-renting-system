import axios from 'axios'

export default async function ({
    redirect
}) {
    if (window.$nuxt.$axios.defaults.headers.common['X-CSRF-TOKEN'] == undefined){
        window.$nuxt.$axios.defaults.headers.common['X-CSRF-TOKEN'] = (await axios.get("/api/csrf")).data}
}
