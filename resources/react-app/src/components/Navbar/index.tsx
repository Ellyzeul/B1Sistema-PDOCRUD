import { useEffect, useState } from "react"
import { Dropdown } from "./Dropdown"
import "./style.css"
import { NavbarProp } from "./types"

export const Navbar = (props: NavbarProp) => {
	const { items } = props
	const [dropdowns, setDropdowns] = useState([] as JSX.Element[])

	useEffect(() => {
		const toUpdate = [] as JSX.Element[]
		let i = 0

		items.forEach(item => toUpdate.push(
			<Dropdown label={item.label} options={item.options} key={i++} />
		))

		setDropdowns(toUpdate)
	}, [items])

	return (
		<nav className="nav_bar">
			{dropdowns}
		</nav>
	)
}
