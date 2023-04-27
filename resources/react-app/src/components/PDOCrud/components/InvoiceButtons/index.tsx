import { MouseEventHandler, useState } from "react"
import { toast } from "react-toastify"
import api from "../../../../services/axios"
import "./style.css" 
import InvoiceButtonsProp from "./types"

export const InvoiceButtons  = (props: InvoiceButtonsProp) => {
    const { orderId, companyId, blingNumber, invoiceNumber, invoiceInput} = props
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
			toast.success("O link será aberto em alguns segundos...")
			window.open(invoiceData.link, "_blank", "noreferrer")
			return
		}

		api.get("/api/orders/invoice-link", {
			params: {
				"company_id": companyId,
				"bling_number": blingNumber
			}			
		})
		.then(response => response.data)
		.then((data) => {
			setInvoiceData(data)
			toast.success("O link será aberto em alguns segundos...")
			window.open(data.link, "_blank", "noreferrer")
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