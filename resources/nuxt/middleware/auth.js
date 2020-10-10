import axios from 'axios'

export default async function({
    redirect
}) {
    let loginStatus = (await axios.get('/gateway/auth/user')).data
    if (!loginStatus.user) {
        return redirect('/auth/login')
    }
}
