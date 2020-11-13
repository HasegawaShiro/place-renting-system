import DataUtil from './utils/DataUtil.js';
import CONSTANTS from './constants.js';

export default class API {
    static sendRequest(url, method = 'get', data = null, options = {}) {
        const allowedMethods = ['get', 'post', 'put', 'delete'];
        method = method.toLowerCase();
        const toSendData = method == "get" ? {params: data} : data;
        if(options.hasFile === true) {
            let tmp = new FormData();
            for(let d in data) {
                tmp.append(data[d]);
            }
            window.$nuxt.$axios.setHeader({'Content-Type': 'multipart/form-data'});
        } else {
            window.$nuxt.$axios.setHeader({'Content-Type': 'application/json'});
        }

        if(allowedMethods.includes(method)){
            return window.$nuxt.$axios[method](url, toSendData)
                .then((response) => {
                    if(options.onlyData === true){
                        return response.data;
                    }else{
                        return response;
                    }
                })
                .catch(async (error) => {
                    if(error.response.status === 419) {
                        await API.refreshCSRFToken();
                        return API.sendRequest(url, method, data, options);
                    }else if(error.response.status === 401 && !DataUtil.isEmpty(window.mainLayout)) {
                        if(options.doNotRelogin !== true){
                            window.mainLayout.relogin();
                        }else{
                            throw error;
                        }
                    }else if(error.response.status === 500 && !DataUtil.isEmpty(window.mainLayout)) {
                        window.mainLayout.showSnackbar('error', CONSTANTS.messages['unknown-error']+CONSTANTS.messages["contact-maintenance"]);
                    }else{
                        throw error;
                    }
                });
        }else{
            console.error("Invalid method: " + method);
        }

    }
    static async refreshCSRFToken() {
        window.$nuxt.$axios.defaults.headers.common['X-CSRF-TOKEN'] = (await window.$nuxt.$axios.get("/api/csrf")).data;
    }
    static async getReferenceSelect(table, options = {}){
        let result = [];
        await API.sendRequest(`/api/select/${table}`,'get',options).then(response => {
            result = response.data;
        });
        return result;
    }
}
