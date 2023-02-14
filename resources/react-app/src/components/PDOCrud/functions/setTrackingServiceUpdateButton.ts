import { toast } from "react-toastify"
import api from "../../../services/axios"
import getColumnFieldIndex from "./getColumnFieldIndex"
import getTableRows from "./getTableRows"

const setTrackingServiceUpdateButton = () => {
    const deliveryMethodIdx = getColumnFieldIndex("Forma de envio")
    const numberBlingIdx = getColumnFieldIndex("Nº Bling")
    const orderIdx = getColumnFieldIndex("Nº")

    if(deliveryMethodIdx === -1 || numberBlingIdx === -1 || orderIdx === -1) return

    const rows = getTableRows()

    rows.forEach(row => {
        const cell = row.cells[deliveryMethodIdx]
        const blingNumber = (row.cells[numberBlingIdx].children[0] as HTMLInputElement).value
        const orderId = row.cells[orderIdx].textContent?.trim()

        const trackingServiceSelect = (cell.children[0] as HTMLSelectElement)

        const icon = document.createElement("i")
        icon.classList.add("fa-solid")
        icon.classList.add("fa-cloud-arrow-down")
        icon.classList.add("icon-css")

        icon.style.position = "relative"
        icon.style.paddingTop = "1px"
        icon.style.paddingLeft = "110px"

        icon.addEventListener("click", () =>{

            api.patch( `/api/orders/traking-service/update?blingNumber=${blingNumber}&orderId=${orderId}`)
                .then(response => response.data[0])
                .then((data) => {
                    trackingServiceSelect.selectedIndex = data.trackingService; 
                    toast.success("Forma de envio atualizado com sucesso!")
                })
                .catch(() => {toast.error("Falha na requisição de dados do Bling!")})
        })

        cell.appendChild(icon)
    })

    document.styleSheets[1].addRule('.icon-css:hover', 'cursor: pointer;');
}

export default setTrackingServiceUpdateButton
