import { useContext, useEffect, useRef, useState } from "react"
import { ToastContainer } from "react-toastify"
import { UserData } from "../../components/LoginForm/types"
import { Navbar } from "../../components/Navbar"
import { DropdownProp } from "../../components/Navbar/Dropdown/types"
import { PDOCrud } from "../../components/PDOCrud"
import { SupplierURLModal } from "../../components/SupplierURLModal"
import { NavbarContext } from "../../contexts/Navbar"
import { UserDataContext } from "../../contexts/UserData"
import api from "../../services/axios"
import { PhaseResponse } from "./types"
import TrackingMethodModal from "../../components/PDOCrud/components/TrackingMethodModal"
import { TrackingMethodParams } from "../../components/PDOCrud/components/TrackingMethodModal/types"

export const OrdersPage = () => {
	const refModal= useRef(null)
	const refModalId = useRef(null)
	const refOnlineOrderNumber = useRef(null)
	const refURLInput = useRef(null)
	const refTrackingMethodModal = useRef(null as HTMLDivElement | null)
	const trackingMethodInfo = { info: {} } as { info: TrackingMethodParams }
	const navbarContext = useContext(NavbarContext)
	const userDataContext = useContext(UserDataContext)
	const navbarItems = navbarContext[0] as {[key: string]: DropdownProp[]}
	const setNavbarItems = navbarContext[1] as (prevState: {[key: string]: DropdownProp[]}) => void
	const userData = userDataContext[0] as UserData
	const [phases, setPhases] = useState([] as DropdownProp[])

	const fetchPhases = () => {
		api.get(`/api/phases/read?email=${userData.email}`)
			.then(response => response.data as PhaseResponse)
			.then(response => {
				const { message, items } = response
				const { inicio, ...phases } = items
				const dropdowns = [] as DropdownProp[]

				dropdowns.push({
					label: "Geral",
					options: inicio
				})
				Object.keys(phases).forEach(phase => dropdowns.push({
					label: phase,
					options: items[phase]
				}))

				setPhases(dropdowns)
				navbarItems.phases = dropdowns
				setNavbarItems(navbarItems)
			})
	}

	useEffect(() => {
		if(!("phases" in navbarItems)) return fetchPhases()

		setPhases(navbarItems.phases)
	}, [navbarItems])

	return (
		<>
			<Navbar items={phases} />
			<PDOCrud 
				refModal={refModal} 
				refModalId={refModalId} 
				refOnlineOrderNumber={refOnlineOrderNumber} 
				refURLInput={refURLInput} 
				refTrackingMethodModal={refTrackingMethodModal}
			/>
			<SupplierURLModal 
				refModal={refModal} 
				refModalId={refModalId} 
				refOnlineOrderNumber={refOnlineOrderNumber} 
				refURLInput={refURLInput} 
			/>
			<TrackingMethodModal 
				refTrackingMethodModal={refTrackingMethodModal}
				refOnlineOrderNumber={refOnlineOrderNumber}
			/>
			<ToastContainer/>
		</>
	)
}
