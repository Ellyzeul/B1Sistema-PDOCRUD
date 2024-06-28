import api from "../../../services/axios"
import getColumnFieldIndex from "./getColumnFieldIndex"
import getTableRows from "./getTableRows"

export default function setSaveEvent() {
  const saveBtn = document.querySelector('.pdocrud-button-save') as HTMLAnchorElement
  const rows = getTableRows()
  const onlineOrderNumberIndex = getColumnFieldIndex('ORIGEM')
  const phaseIndex = getColumnFieldIndex('Fase do processo')

  saveBtn.addEventListener('click', async(event) => {
    event.preventDefault()
    const ordersPhases = {} as Record<string, string>
    rows.map(({children: row}) => [row[onlineOrderNumberIndex], row[phaseIndex]] as [HTMLTableCellElement, HTMLTableCellElement])
      .forEach(([numberCell, phaseCell]) => 
        ordersPhases[numberCell.innerText] = (phaseCell.children[0] as HTMLSelectElement).value
      )

    api.post('/api/tracking/update-internal', {orders_phases: ordersPhases})
  })
}
