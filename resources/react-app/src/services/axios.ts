import axios from "axios"

const location = window.location
const baseURL = location.hostname === "localhost"
	? "http://localhost:8000/"
	: `${location.protocol}//${location.host}/`

const api = axios.create({
	baseURL: baseURL
})

export default api
