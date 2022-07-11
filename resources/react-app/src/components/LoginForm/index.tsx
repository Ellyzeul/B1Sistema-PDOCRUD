import { FormEventHandler, useRef, useState } from "react"
import { toast } from "react-toastify"
import api from "../../services/axios"
import 'react-toastify/dist/ReactToastify.css'
import "./style.css"

export const LoginForm = () => {
	const [passVisible, setPassVisible] = useState(false)
	const formRef = useRef(null)

	const changePasswordVisibility = () => setPassVisible(!passVisible)

	const makeLogin: FormEventHandler = (event) => {
		event.preventDefault()
		if(!formRef.current) return
		const form = formRef.current as HTMLFormElement
		const email = (form[0] as HTMLInputElement).value
		const password = (form[1] as HTMLInputElement).value

		api.post('/api/user/login', {
			email: email,
			password: password
		})
		.then(response => response.data)
		.then(response => {
			const { message, ...userData } = response
			toast.success(message)
			window.localStorage.setItem("userData", JSON.stringify(userData))
			window.location.pathname = '/orders'
		})
	}

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
		</form>
	)
}
