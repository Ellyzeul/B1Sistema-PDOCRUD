import { OptionProp } from "./types"
import "./style.css"

export const Option = (props: OptionProp) => {
	const { id, name, url, color } = props
	const style = {
		backgroundColor: `#${color}`
	}

	return (
		<a className={"navbar-dropdown-option"} href={url}>
			{
				color 
				? <div className={"navbar-dropdown-option-colour-div"} style={style}></div> 
				: null
			}
			{name}
		</a>
	)
}
