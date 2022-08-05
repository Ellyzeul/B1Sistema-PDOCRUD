import { MouseEventHandler, useEffect, useRef, useState } from "react"
import { Dropdown } from "./Dropdown"
import "./style.css"
import { NavbarProp } from "./types"

export const Navbar = (props: NavbarProp) => {
	const { items } = props
	const [dropdowns, setDropdowns] = useState([] as JSX.Element[])
	const logoRef = useRef(null)

	const onClickLogo: MouseEventHandler = () => {
		window.location.pathname = "/"
	}

	const onMouseEnterLogo: MouseEventHandler = () => {
		if(!logoRef.current) return
		const logo = logoRef.current as HTMLImageElement

		logo.style.filter = "invert(1)"
	}

	const onMouseLeaveLogo: MouseEventHandler = () => {
		if(!logoRef.current) return
		const logo = logoRef.current as HTMLImageElement

		logo.style.filter = "invert(0)"
	}

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
			<div 
				className={"nav-bar-img nav-bar-img-clickable"} 
				onClick={onClickLogo}
				onMouseEnter={onMouseEnterLogo}
				onMouseLeave={onMouseLeaveLogo}
			>
				<img ref={logoRef} src="/liv_seline_logo.png" alt="" />
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
