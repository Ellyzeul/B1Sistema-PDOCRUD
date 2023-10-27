import axios from "axios";

const location = window.location
const baseURL = location.hostname === "localhost" || location.hostname === "192.168.0.195"
	? 'http://localhost:3000'
	: 'http://b1sistema.com.br'

const apiServices = axios.create({
  baseURL: `${baseURL}/api`, 
})

export default apiServices
