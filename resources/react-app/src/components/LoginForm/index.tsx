import { useState } from "react"
import "./style.css"

export const LoginForm = () => {
	const [passVisible, setPassVisible] = useState(false)

	const changePasswordVisibility = () => setPassVisible(!passVisible)

	return (
		<form id="login_form">
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
