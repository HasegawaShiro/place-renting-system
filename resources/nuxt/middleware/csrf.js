export default async function ({
    redirect
}) {
    if (window.$nuxt.$axios.defaults.headers.common['X-CSRF-TOKEN'] == undefined){
        window.$nuxt.$axios.defaults.headers.common['X-CSRF-TOKEN'] = (await window.$nuxt.$axios.get("api/csrf")).data}
}
