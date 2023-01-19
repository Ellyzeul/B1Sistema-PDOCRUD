import { differenceInCalendarDays } from "date-fns"
import getColumnFieldIndex from "./getColumnFieldIndex"
import setNewColumn from "./setNewColumn"

const setDaysAfterDeliveryColumn = () => {
	setNewColumn.columns = {}
	const generateData = (row: HTMLTableRowElement) => {
		const deliveredDateIdx = getColumnFieldIndex("Data de entrega")
		const div = document.createElement('div')

		if(deliveredDateIdx === -1) return div
		const date = (row.children[deliveredDateIdx].children[0] as HTMLInputElement)
      .value
      .split('/')
			.map(part => Number(part))
    const delivery = new Date(date[2], date[1]-1, date[0])
		const today = new Date()

		const interval = differenceInCalendarDays(today, delivery)
		div.innerText = interval < 5
      ? "Não"
      : "Sim"

		return div
	}
	setNewColumn("Pronto para 6.2", generateData)
}

export default setDaysAfterDeliveryColumn
