import axios from 'axios';
import DataUtil from '../utils/DataUtil.js';

export default async function({
    redirect
}) {
    let loginStatus = (await axios.get('/gateway/auth/user')).data
    if (!loginStatus.user) {
        if(!DataUtil.isEmpty(window.mainLayout)) {
            window.mainLayout.relogin();
        }
    }
}
