export type ShipmentAndPriceProp = {
	orderId: string,
	address_form_ref: React.MutableRefObject<null>,
	delivery_method: string | null,
	delivery_service: string | null,
	tracking_code: string | null,
	id_bling: number,
} 

export type CorreiosData = Array<{
	name: string,
	expected_deadline: number | null,
	expected_date: string | null,
	shipping_error_msg: string | null,
	price: string | null,
	price_error_msg: string | null,
}>

export type JadlogData = {
	price: number | null,
	expected_deadline: number | null,
	error_msg: string | null
}

export type KanguData = Array<{
	vlrFrete: number,
	prazoEnt: string,
	dtPrevEnt: string,
	transp_nome: string,
}> | { error: {
	codigo: string,
	mensagem: string,
} }

export type EnviaData = Array<{
	name: string,
	price?: number,
	expected_deadline?: string,
}>

export type LoggiData = Array<{
	name: string,
	price: number | null,
	expected_deadline: number | null,
}>
