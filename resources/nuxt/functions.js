export default {
    async delay(ms = 1500) {
        return new Promise(resolve => {
            setTimeout(() => {
              resolve(2);
            }, ms);
        });
    },
}
