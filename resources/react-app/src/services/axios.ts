import axios from "axios"

const location = window.location
const baseURL = location.hostname === "localhost" || location.hostname === "192.168.0.195"
	? "http://localhost:8000/"
	: `${location.protocol}//${location.host}/`

const api = axios.create({
	baseURL: baseURL
})

export default api
