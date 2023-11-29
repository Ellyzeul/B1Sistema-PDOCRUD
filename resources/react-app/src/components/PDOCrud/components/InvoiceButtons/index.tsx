import { MouseEventHandler, useState } from "react"
import { toast } from "react-toastify"
import api from "../../../../services/axios"
import "./style.css" 
import InvoiceButtonsProp from "./types"

export const InvoiceButtons  = (props: InvoiceButtonsProp) => {
    const { orderId, companyId, sellercentralId, blingNumber, invoiceNumber, invoiceInput} = props
    const [ invoiceData, setInvoiceData ] = useState({} as {
		invoice_number: string | null,
		serie: string | null,
		link: string | null
	})

	const getInvoiceNumber: MouseEventHandler = event => {
		event.preventDefault()
		api.patch("/api/orders/invoice-number", {
				"order_id": orderId,	
				"invoice_number": invoiceNumber			
		})
		.then(response => response.data)
		.then((data) => {
			if(data) {
				setInvoiceData(data)
				invoiceInput.value = data.invoice_number
			}
			toast.success("NF atualizada com sucesso!")
		})
		.catch(() => toast.error("Erro. Tente novamente ou contate o TI em caso de muitos erros..."))
	}

	const getInvoicePDF: MouseEventHandler = event => {
		event.preventDefault()
		if(invoiceData.link) {
			window.open(invoiceData.link, "_blank", "noreferrer")
			return
		}

		toast.info("O link será aberto em alguns segundos...")
		api.get("/api/orders/invoice-link", {
			params: {
				"company_id": companyId,
				"bling_number": blingNumber
			}			
		})
			.then(response => response.data)
			.then(({ invoice_number, serie, link_full, link_simplified }) => {
				const link = sellercentralIsBR(sellercentralId) ? link_full : link_simplified
				setInvoiceData({ invoice_number, serie, link })
				window.open(link, "_blank", "noreferrer")
			})
			.catch(() => toast.error("Erro. Tente novamente ou contate o TI em caso de muitos erros..."))
	}
    
    return (
        <>
			<button 
				className={"invoice-number-button"}
				onClick={getInvoiceNumber}
			>
				<i className="fa-solid fa-floppy-disk"></i>
			</button>   
            <button 
				className={"pdf-button"}
				onClick={getInvoicePDF}
			>
				<i className="fa-solid fa-file-pdf"></i>
			</button>       
        </>
    )
}

const sellercentralIsBR = (sellercentralId: string | null) => {
	const id = Number(sellercentralId)

	return [0, 1, 5, 6, 9, 10].findIndex(isBR => isBR === id) !== -1
}
