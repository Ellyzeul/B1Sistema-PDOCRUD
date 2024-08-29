import { useEffect, useRef, useState } from "react"
import { toast } from "react-toastify"
import api from "../../../../../../services/axios"
import "./style.css"
import { ShipmentAndPriceProp, CorreiosData, JadlogData, KanguData, EnviaData, LoggiData } from "./types"

export const ShipmentAndPrice = (props: ShipmentAndPriceProp) => {
	const { orderId, address_form_ref, delivery_service, delivery_method, tracking_code, id_bling } = props
	const inputsRef = useRef<HTMLDivElement | null>(null)
	const [jadlogData, setJadlogData] = useState({} as JadlogData)
	const [correiosData, setCorreiosData] = useState([] as CorreiosData)
	const [kanguData, setKanguData] = useState(null as KanguData | null)
	const [enviaData, setEnviaData] = useState(null as EnviaData | null)
	const [loggiData, setLoggiData] = useState(null as LoggiData | null)
	const [jadlogCotations, setjadlogCotations] = useState([] as JSX.Element[])
	const [kanguCotations, setKanguCotations] = useState([] as JSX.Element[])
	const [enviaCotations, setEnviaCotations] = useState([] as JSX.Element[])
	const [correiosCotations, setcorreiosCotations] = useState([] as JSX.Element[])
	const [loggiCotations, setLoggiCotations] = useState([] as JSX.Element[])

	useEffect(() => {
		if(jadlogData) mapJadlog()
		if(correiosData) mapCorreios()
		if(kanguData) mapKangu(kanguData)
		if(enviaData) mapEnvia(enviaData)
		if(loggiData) mapLoggi(loggiData)
	}, [jadlogData, correiosData, kanguData, enviaData, loggiData])

			
	const getShipmentAndPrice = (originId: string, clientPostalCode: string, weight: string) => {
		api.get("/api/tracking/consult-price-and-shipping", {
			params: {
				"order_id": orderId,
				"origin_id": originId,
				"client_postal_code": clientPostalCode,
				"weight": weight
			}
		})
		.then(response => response.data)
		.then((response) => {
			if(response.error_msg) toast.error(response.error_msg)

			setLoggiData(response['Loggi'])
			setCorreiosData(response["Correios"])
			setJadlogData(response["Jadlog"][0])
			setKanguData(response["Kangu"])
			setEnviaData(response["Envia"])
		})
		.catch((error) => {
			toast.error("Erro ao calcular o frete. Por favor, tente novamente.")
			console.error(error)
		})
	}

	const handleServiceClick = (order_id: string, delivery_method: string, service_name: string) => {
		api.patch('/api/orders/delivery-method', { order_id, delivery_method, service_name })
			.then(response => response.data)
			.then(({ success, error_msg, msg }) => {
				if(!success) {
					toast.error(error_msg)
					return
				}

				toast.success(msg)
			})
	}

	const mapCorreios = () => {
		const content = correiosData.map(service => {
			const { shipping_error_msg, price_error_msg, name, expected_deadline, expected_date, price } = service
			const unavailable = !!shipping_error_msg || !!price_error_msg

			if(unavailable || (!expected_deadline && !expected_date)) return(<></>)

			return (
				<div 
					className={unavailable ? "unavailable" : "available"} 
					onClick={unavailable ? () => {} : () => handleServiceClick(orderId, 'correios', name)}
				>
					<strong>{name} -</strong>
					<strong> prazo: </strong>{
						(expected_deadline && expected_date)
							? `${expected_deadline} ${expected_deadline === 1 ? 'dia útil' : 'dias úteis'} - ${expected_date}`
							: '--'
					}
					<strong> custo: </strong>R$ {price || '--'}
				</div>
			)
		})

		setcorreiosCotations(content)
	}

	const mapJadlog = () => {
		const { error_msg, price, expected_deadline } = jadlogData

		if(!(price || expected_deadline || error_msg)) return

		const content = (
			<div 
				className={error_msg ? "unavailable" : "available"} 
				onClick={error_msg ? () => {} : () => handleServiceClick(orderId, 'jadlog', '.Package')}
			>
				<strong>Jadlog - </strong>
				<strong> custo: </strong>R$ {price ? price.toFixed(2) : '--'}
				<strong> prazo: </strong> {expected_deadline ? `${expected_deadline} dias úteis` : '--'}
			</div>
		)

		setjadlogCotations([content])
	}

	const mapKangu = (kanguData: KanguData) => {
		if(!Array.isArray(kanguData)) return
		const cotations = [] as JSX.Element[]

		kanguData.forEach(({ vlrFrete, dtPrevEnt, prazoEnt, transp_nome }) => cotations.push(
			<div 
				className="available" 
				onClick={() => handleServiceClick(orderId, 'kangu', transp_nome)}
			>
				<strong>{`Kangu ${transp_nome}`} -</strong>
				<strong> custo: </strong>R$ {vlrFrete ? String(vlrFrete).replace('.', ',') : '--'}
				<strong> prazo: </strong> {`${prazoEnt} dias úteis`} - {new Date(dtPrevEnt).toLocaleDateString()}
			</div>
		))

		setKanguCotations(cotations)
	}

	const mapEnvia = (data: EnviaData) => {
		setEnviaCotations(data.map(({ name, price, expected_deadline }) => 
			<div 
				className="available" 
				onClick={() => handleServiceClick(orderId, 'envia', name)}
			>
				<strong>{name}</strong>
				<strong>Custo: </strong>R$ {price ? String(price).replace('.', ',') : '--'}
				<strong>Prazo: </strong> {expected_deadline}
			</div>
		))
	}

	const mapLoggi = (data: LoggiData) => {
		setLoggiCotations(data.map(({ name, price, expected_deadline }) => 
			<div 
				className="available" 
				onClick={() => handleServiceClick(orderId, 'loggi', name)}
			>
				<strong>{name}</strong>
				<strong> - Custo: </strong>R$ {String(price ?? '--').replace('.', ',')}
				<strong> - Prazo: </strong> {expected_deadline ? `${expected_deadline} dias` : '--'}
			</div>
		))
	}

	const handleClick = () => {
		const addressForm = address_form_ref.current as HTMLDivElement | null
		if(!inputsRef.current || !addressForm) return
		const originId = (inputsRef.current?.querySelector('select[name="origin-zipcode"]') as HTMLSelectElement).value
		const clientPostalCode = (addressForm.querySelector('input[name="postal_code"]') as HTMLSelectElement).value
		const weight = (inputsRef.current?.querySelector('select[name="weight"]') as HTMLSelectElement).value
		
		getShipmentAndPrice(originId, clientPostalCode, weight)
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
				<label htmlFor="weight">Peso:</label>
				<select name="weight" defaultValue={1}>
					{
						[0.25, 0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5].map(value => 
							<option value={value}>{String(value).replace('.', ',')} Kg</option>
						)
					}
				</select>
				<a 
					className="fa-solid fa-pen" 
					title="Editar pedido no Bling" 
					href={`https://www.bling.com.br/vendas.php#edit/${id_bling}`}
					target="blank"
				> </a>
			</div>
			<button onClick={handleClick} id={"shipping-button"}>Consultar</button>
			<div className="correios-content">
				{loggiCotations}
				{correiosCotations}
				{jadlogCotations}
				{kanguCotations}
				{enviaCotations}
			</div>
		</div>
	)
}
