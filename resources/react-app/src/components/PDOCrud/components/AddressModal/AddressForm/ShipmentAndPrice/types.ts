export type ShipmentAndPriceProp = {
	orderId: string,
	address_form_ref: React.MutableRefObject<null>,
	delivery_method: string | null,
	delivery_service: string | null,
	tracking_code: string | null,
	id_bling: number,
} 

export type CorreiosData = {
	[key: string]: {
		service_name: string,
		delivery_expected_date: number | null,
		max_date: string | null,
		shipping_error_msg: string | null,
		price: string | null,
		price_error_msg: string | null,
	}
}

export type JadlogData = {
	price: number | null,
	max_date: number | null,
	error_msg: string | null
}
