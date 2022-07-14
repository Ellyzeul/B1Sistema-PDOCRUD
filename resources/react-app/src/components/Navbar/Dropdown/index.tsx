import { useEffect, useState } from "react"
import "./style.css"
import { DropdownProp } from "./types"
import { Option } from "./Option"

export const Dropdown = (props: DropdownProp) => {
	const { label, options } = props
	const [optionsElem, setOptionsElem] = useState([] as JSX.Element[])

	useEffect(() => {
		const toUpdate = [] as JSX.Element[]
		let i = 0
		options.forEach(option => toUpdate.push(
			<Option label={option.label} pathname={option.pathname} color={option.color} key={i++} />
		))
		setOptionsElem(toUpdate)
	}, [options])

	return (
		<div className="dropdown">
			<button className="dropbtn">{label}</button>
			<div className="dropdown-content">
				{optionsElem}
			</div>
		</div>
	)
}
