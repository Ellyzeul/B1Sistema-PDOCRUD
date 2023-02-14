import { toast } from "react-toastify"
import api from "../../../services/axios"
import getColumnFieldIndex from "./getColumnFieldIndex"
import getTableRows from "./getTableRows"

const setTrackingCodeUpdateButton = () => {
    const trackingCodeIdx = getColumnFieldIndex("Código de rastreio")
    const numberBlingIdx = getColumnFieldIndex("Nº Bling")
    const orderIdx = getColumnFieldIndex("Nº")

    if(trackingCodeIdx === -1 || numberBlingIdx === -1 || orderIdx === -1) return

    const rows = getTableRows()

    rows.forEach(row => {
        const cell = row.cells[trackingCodeIdx]
        const blingNumber = (row.cells[numberBlingIdx].children[0] as HTMLInputElement).value
        const orderId = row.cells[orderIdx].textContent?.trim()
        
        const trackingCodeInput = (cell.children[0] as HTMLInputElement)
        
        const icon = document.createElement("i")
        icon.classList.add("fa-solid")
        icon.classList.add("fa-cloud-arrow-down")
        icon.classList.add("icon-css")

        cell.style.display = "grid"
        cell.style.gridTemplateColumns = "90% 10%"

        icon.style.paddingTop = "10px"

        trackingCodeInput.style.gridColumnStart = "1"
        trackingCodeInput.style.gridColumnEnd = "1"

        icon.style.gridColumnStart = "2"
        icon.style.gridColumnEnd = "2"
        
        icon.addEventListener("click", () =>{
            
            api.patch(`/api/orders/traking-id/update?blingNumber=${blingNumber}&orderId=${orderId}`)
                .then(response => response.data[0])
                .then((data) => {
                    trackingCodeInput.value = data.trackingCode
                    toast.success("Código de rastreio atualizado com sucesso!")
                })
                .catch(() => {toast.error("Falha na requisição de dados do Bling!")})
        })

        cell.appendChild(icon)
    })

    document.styleSheets[1].addRule('.icon-css:hover', 'cursor: pointer;');
}

export default setTrackingCodeUpdateButton
