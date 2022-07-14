import { OptionProp } from "./types"

export const Option = (props: OptionProp) => {
	const { label, pathname, color } = props
	const style = {
		backgroundColor: color,
		padding: "4px",
		borderRadius: "10px",
		border: "1px solid #DDD"
	}

	return (
			<a href={pathname}><div style={style}></div>{label}</a>
	)
}
