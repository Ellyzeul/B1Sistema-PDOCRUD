import { useEffect, useRef, useState } from "react"
import { Navbar } from "../../components/Navbar"
import { OptionProp } from "../../components/Navbar/Dropdown/Option/types"
import { DropdownProp } from "../../components/Navbar/Dropdown/types"
import { PDOCrud } from "../../components/PDOCrud"
import { SupplierURLModal } from "../../components/SupplierURLModal"
import api from "../../services/axios"
import { Phase } from "./types"

export const OrdersPage = () => {
	const refModal= useRef(null)
	const refModalId = useRef(null)
	const refOnlineOrderNumber = useRef(null)
	const refURLInput = useRef(null)
	const [navbarItems, setNavbarItems] = useState([] as DropdownProp[])

	useEffect(() => {
		api.get('/api/phases/read')
			.then(response => response.data)
			.then(response => {
				const phases = response.phases as Phase[]
				const items = {} as {[key: string]: OptionProp[]}
				const toUpdate = [{
					label: "Início",
					options: [{label: "Início", pathname: "/orders"}]
				}] as DropdownProp[]

				phases.forEach(phase => {
					const { id, name, color } = phase
					const label = (id.split('.') as string[])[0]
					const option = {
						label: `${id} - ${name}`,
						pathname: `/orders?phase=${id}`,
						color: `#${color}`
					}

					items[label] 
					 	? items[label].push(option)
						: items[label] = [option]
				})

				Object.keys(items).forEach(phaseId => toUpdate.push({
					label: `Fase ${phaseId}`,
					options: items[phaseId]
				}))

				setNavbarItems(toUpdate)
			})
	}, [])

	return (
		<>
			<Navbar items={navbarItems} />
			<PDOCrud 
				refModal={refModal} 
				refModalId={refModalId} 
				refOnlineOrderNumber={refOnlineOrderNumber} 
				refURLInput={refURLInput} 
			/>
			<SupplierURLModal 
				refModal={refModal} 
				refModalId={refModalId} 
				refOnlineOrderNumber={refOnlineOrderNumber} 
				refURLInput={refURLInput} 
			/>
		</>
	)
}
