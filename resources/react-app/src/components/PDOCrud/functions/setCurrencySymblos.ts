import getColumnFieldIndex from "./getColumnFieldIndex"

const setCurrencySymbols = () => {
	const rows = document.querySelectorAll('.pdocrud-data-row') as NodeListOf<HTMLTableCellElement>
	const valueIdx = getColumnFieldIndex("Valor")
	const sellercentralIdx = getColumnFieldIndex("Exportação")

	if(valueIdx === -1 || sellercentralIdx === -1) return
	const regex = [
		{regex: /Brasil/, symbol: "R$"},
		{regex: /Canadá/, symbol: "CA$"},
		{regex: /Estados Unidos/, symbol: "US$"},
		{regex: /Reino Unido/, symbol: "£"},
	]
	const getCurrency = (country: string) => {
		return regex
			.filter(obj => obj.regex.test(country))
			.map(obj => obj.symbol)
	}

	rows.forEach(row => {
		const country = row.children[sellercentralIdx].textContent as string
		const currency = row.children[valueIdx]

		currency.textContent = `${getCurrency(country)} ${currency.textContent}`
	})
}

export default setCurrencySymbols
