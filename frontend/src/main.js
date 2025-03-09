import { createApp } from "vue"
import { createPinia } from "pinia"
import App from "./App.vue"
import router from "./router"
import axios from "axios"
import "./assets/main.css"

// // Configure axios defaults
// axios.defaults.baseURL = "http://localhost:9000"

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.mount("#app")

