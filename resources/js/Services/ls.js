export default {
    get(key) {
        const val = localStorage.getItem(key);

        return val ? val : null
    },

    set(key, val) {
        localStorage.setItem(key, val)
    },

    remove(key) {
        localStorage.removeItem(key)
    }
}
