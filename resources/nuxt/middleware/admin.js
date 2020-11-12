import axios from 'axios';

export default async function({
    redirect
}) {
    let isAdmin = true;
    // console.log('middleware: admin')
    await axios.get('/api/auth').then(response => {
        let user = response.data.user;
        if(user.id !== 1) isAdmin = false;
    }).catch(e => {
        isAdmin = false;
    });

    if(!isAdmin) return redirect('/errors/403');
}
