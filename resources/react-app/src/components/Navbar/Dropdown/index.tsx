import { MouseEventHandler, useEffect, useState } from "react"
import "./style.css"
import { DropdownProp } from "./types"
import { Option } from "./Option"

export const Dropdown = (props: DropdownProp) => {
	const { label, options } = props
	const [optionsElem, setOptionsElem] = useState([] as JSX.Element[])

	useEffect(() => {
		let i = 0
		setOptionsElem(options.map(option => 
			<Option id={option.id} name={option.name} url={option.url} color={option.color} key={i++} />
		))
	}, [options])

	return (
		<div className="dropdown">
			<button className="dropbtn">
				{label}
			</button>
			<div className="dropdown-content">
				{optionsElem}
			</div>
		</div>
	)
}
