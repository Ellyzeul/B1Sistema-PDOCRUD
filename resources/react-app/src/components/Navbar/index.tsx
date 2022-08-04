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
		<nav className="nav-bar">
			<div className="nav-bar-img">
				<img src="/liv_seline_logo.png" alt="" />
			</div>
			<div className="nav-bar-dropdowns">
				{dropdowns}
			</div>
			<div className="nav-bar-img nav-bar-img-right">
				<img src="/b1_logo.png" alt="" />
			</div>
		</nav>
	)
}
