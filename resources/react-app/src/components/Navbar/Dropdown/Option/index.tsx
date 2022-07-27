import { OptionProp } from "./types"
import "./style.css"

export const Option = (props: OptionProp) => {
	const { label, pathname, color } = props
	const style = {
		backgroundColor: color
	}

	return (
		<a
			className={"navbar-dropdown-option"} 
			href={pathname}
		>
			<div className={"navbar-dropdown-option-colour-div"} style={style}></div>
			{label}
		</a>
	)
}
