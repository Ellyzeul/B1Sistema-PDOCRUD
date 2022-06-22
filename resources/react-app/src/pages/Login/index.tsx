import { LoginForm } from "../../components/LoginForm"
import "./style.css"
import { LoginProp } from "./types"

export const Login = (props: LoginProp) => {
	const { setIsLogged } = props

	return (
		<div id="login_page">
			<LoginForm />
		</div>
	)
}
