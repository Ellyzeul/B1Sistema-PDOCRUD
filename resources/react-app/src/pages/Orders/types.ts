export interface PhaseResponse {
	message: string,
	items: {
		inicio: Phase[],
		[key: string]: Phase[]
	}
}

interface Phase {
	id: string,
	name: string,
	url: string,
	color?: string
}
