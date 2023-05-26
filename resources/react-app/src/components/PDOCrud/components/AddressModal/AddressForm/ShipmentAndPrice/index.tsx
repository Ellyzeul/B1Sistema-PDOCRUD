import { useEffect, useRef, useState } from "react"
import { toast } from "react-toastify"
import api from "../../../../../../services/axios"
import "./style.css"
import { ShipmentAndPriceProp, CorreiosData, JadlogData } from "./types"

export const ShipmentAndPrice = (props: ShipmentAndPriceProp) => {
	const { orderId, address_form_ref, delivery_service, delivery_method, tracking_code } = props
	const inputsRef = useRef<HTMLDivElement | null>(null)
	const [jadlogData, setJadlogData] = useState({} as JadlogData)
	const [correiosData, setCorreiosData] = useState({} as CorreiosData)
	const [cotations, setCotations] = useState([] as JSX.Element[])

	useEffect(() => {
		if(jadlogData) JadlogInfo()
	}, [jadlogData])

	useEffect(() => {
		if(correiosData) CorreiosInfo()
	}, [correiosData])
			
	const getShipmentAndPrice = (originId: string, clientPostalCode: string, deliveryMethod: string, weight: string) => {
		api.get("/api/tracking/consult-price-and-shipping", {
			params: {
				"order_id": orderId,
				"origin_id": originId,
				"client_postal_code": clientPostalCode,
				"delivery_method": deliveryMethod,
				"weight": weight
			}
		})
		.then(response => response.data)
		.then((response) => {
			if(response.error_msg) toast.error(response.error_msg)

			if(deliveryMethod === "Correios") setCorreiosData(response)
			if(deliveryMethod === "Jadlog") setJadlogData(response)
			console.log(response)
		})
		.catch((error) => {
			toast.error("Erro ao calcular o frete. Por favor, tente novamente.")
			console.error(error)
		})
	}

	const CorreiosInfo = () => {
		const content = Object.keys(correiosData).map(service => {
			const { shipping_error_msg, price_error_msg, service_name, delivery_expected_date, max_date, price } = correiosData[service]

			return (
				<div className={correiosData && (shipping_error_msg || price_error_msg) ? "unavailable" : ""}>
					<strong>{service_name}</strong>
					<div>
						<strong>Prazo: </strong>
						{
							(delivery_expected_date && max_date)
								? `${delivery_expected_date} ${delivery_expected_date === 1 ? 'dia útil' : 'dias úteis'} - ${max_date}`
								: '--'
						}
					</div>
					<div>
						<strong>Custo: </strong>R$ {price || '--'}
					</div>
				</div>
			)
		})

		setCotations(content)
	}

	const JadlogInfo = () => {
		const { error_msg, price, max_date } = jadlogData

		const content = (
			<div className={error_msg ? "unavailable" : ""}>
				<p><strong>Custo: </strong>R$ {price ? price.toFixed(2) : '--'}</p>
				<p><strong>Prazo: </strong> {max_date ? `${max_date} dias úteis` : '--'}</p>
			</div>
		)

		setCotations([content])
	}

	const handleClick = () => {
		const addressForm = address_form_ref.current as HTMLDivElement | null
		if(!inputsRef.current || !addressForm) return
		const originId = (inputsRef.current?.querySelector('select[name="origin-zipcode"]') as HTMLSelectElement).value
		const clientPostalCode = (addressForm.querySelector('input[name="postal_code"]') as HTMLSelectElement).value
		const deliveryMethod = (inputsRef.current?.querySelector('select[name="delivery-method"]') as HTMLSelectElement).value
		const weight = (inputsRef.current?.querySelector('select[name="weight"]') as HTMLSelectElement).value
		
		getShipmentAndPrice(originId, clientPostalCode, deliveryMethod, weight)
	}

	return (
		<div className="inputs-container" ref={inputsRef}>
			<strong>Simulador de Frete</strong>
			{tracking_code && <p>Rastreio: {tracking_code} {delivery_method && <>/ {delivery_method}</>} {delivery_service && <>- {delivery_service}</>}</p>}
			<div className="label-select-container">
				<label htmlFor="origin-zipcode">CEP de origem:</label>
				<select name="origin-zipcode">
					<option value="1">Coworking</option>
					<option value="2">Caixa Postal</option>
					<option value="3">Itaberaba</option>
					<option value="4">Praça</option>
					<option value="5">Expedição</option>
					<option value="6">Sorocaba</option>
					<option value="7">Parnamirim</option>
				</select>
			</div>
			<div className="label-select-container">
				<label htmlFor="delivery-method">Método de envio:</label>
				<select name="delivery-method">
					<option value={"Correios"}>Correios</option>
					<option value={"Jadlog"}>Jadlog</option>
				</select>
			</div>
			<div className="label-select-container">
				<label htmlFor="weight">Peso:</label>
				<select name="weight" defaultValue={1}>
					<option value={0.5}>0,5 kg</option>
					<option value={1}>1 kg</option>
					<option value={1.5}>1,5 kg</option>
					<option value={2}>2 kg</option>
					<option value={2.5}>2,5 kg</option>
					<option value={3}>3 kg</option>
					<option value={3.5}>3,5 kg</option>
					<option value={4}>4 kg</option>
					<option value={4.5}>4,5 kg</option>
					<option value={5}>5 kg</option>
				</select>
			</div>
			<button onClick={handleClick} id={"shipping-button"}>Consultar</button>
			<div className="correios-content">
				{cotations}
			</div>
		</div>
	)
}
