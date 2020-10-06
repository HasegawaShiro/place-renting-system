export class API{
    static sendRequest(url, method, data = null){
        let headers = {
            'X-CSRF-TOKEN': window.csrfToken
        };
        if (data != undefined && data != null) {
            if (data.constructor == Object || data.constructor == Array) {
                headers['Content-Type'] = "application/json";
                data = JSON.stringify(data);
            }
        }
        return fetch(url, {
            method: method,
            headers,
            redirect: 'follow',
            body: data == null ? null : data
        }).then(_ => {
            return _.json();
        });
    }
}
