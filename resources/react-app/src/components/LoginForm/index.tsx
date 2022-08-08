import { FormEventHandler, useContext, useEffect, useRef, useState } from "react"
import { toast, ToastContainer } from "react-toastify"
import api from "../../services/axios"
import 'react-toastify/dist/ReactToastify.css'
import "./style.css"
import { UserDataContext } from "../../contexts/UserData"
import { UserData } from "./types"
import { NavbarContext } from "../../contexts/Navbar"
import { DropdownProp } from "../Navbar/Dropdown/types"

export const LoginForm = () => {
	const [passVisible, setPassVisible] = useState(false)
	const userDataContext = useContext(UserDataContext)
	const navbarContext = useContext(NavbarContext)
	const setUserData = userDataContext[1] as (prevState: UserData) => void
	const setNavbarItems = navbarContext[1] as (prevState: {[key: string]: DropdownProp[]}) => void
	const formRef = useRef(null)
	const root = document.querySelector("#root") as HTMLDivElement

	const changePasswordVisibility = () => setPassVisible(!passVisible)

	const treatLogin = (message: string, userData: UserData) => {
		root.style.cursor = "context-menu"
		if(!("name" in userData) || !("token" in userData) || !("id_section" in userData)) {
			toast.error(message)
			return
		}

		toast.success(message)
		setUserData(userData)
		window.localStorage.setItem("userData", JSON.stringify(userData))
		window.location.pathname = '/'
	}

	const makeLogin: FormEventHandler = (event) => {
		event.preventDefault()
		if(!formRef.current) return
		const form = formRef.current as HTMLFormElement
		const email = (form[0] as HTMLInputElement).value
		const password = (form[1] as HTMLInputElement).value

		root.style.cursor = "wait"

		api.post('/api/user/login', {
			email: email,
			password: password
		})
		.then(response => response.data)
		.then(response => {
			const { message, ...userData } = response
			treatLogin(message, userData)
		})
	}

	useEffect(() => {
		setNavbarItems({})
	}, [])

	return (
		<form id="login_form" ref={formRef} onSubmit={makeLogin}>
			<h1>Login</h1>
			<div id="login_inputs">
				<input type="email" name="login_email" id="login_email" placeholder="E-mail" />
				<div id="password">
					<input type={passVisible ? "text" : "password"} name="login_pass" id="login_pass" placeholder="Senha" />
					<div onClick={changePasswordVisibility}>{passVisible ? "Esconder senha" : "Ver senha"}</div>
				</div>
				<button type="submit" id="submit_login">Enviar</button>
			</div>
      <ToastContainer />
		</form>
	)
}
