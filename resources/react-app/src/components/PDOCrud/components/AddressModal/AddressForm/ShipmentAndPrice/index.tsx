import { useEffect, useRef, useState } from "react"
import { createRoot } from "react-dom/client"
import { toast } from "react-toastify"
import api from "../../../../../../services/axios"
import "./style.css"
import ShipmentAndPricePros, { CorreiosData, JadlogData } from "./types"

export const ShipmentAndPrice = (props: ShipmentAndPricePros) => {
    const { orderId } = props
    const inputsRef = useRef<HTMLDivElement | null>(null);
    const outputRef = useRef<HTMLDivElement | null>(null);
    const [JadlogData, setJadlogData] = useState<JadlogData | null>(null)
    const [correiosData, setCorreiosData] = useState<CorreiosData | null>(null)

    useEffect(() => {
        if(JadlogData) JadlogInfo()
    }, [JadlogData])

    useEffect(() => {
        if(correiosData) CorreiosInfo()
    }, [correiosData])
        
    const getShipmentAndPrice = (originId: string, deliveryMethod: string, weight: string) => {
        api.get("/api/tracking/consult-price-and-shipping", {
            params: {
                "origin_id": originId,
                "order_id": orderId,
                "delivery_method": deliveryMethod,
                "weight": weight
            }
        })
        .then(response => response.data)
        .then((response) => {
            if(response.error_msg) toast.error(response.error_msg)

            if(deliveryMethod === "Correios") setCorreiosData(response)
            if(deliveryMethod === "Jadlog") setJadlogData(response)
        })
        .catch((error) => {
            toast.error("Erro ao calcular o frete. Por favor, tente novamente.")
            console.error(error)
        })
    }

    const CorreiosInfo = () => {
        if(!outputRef.current) return

        while (outputRef.current.firstChild) {
            outputRef.current.removeChild(outputRef.current.firstChild)
        }

        const content = (
            <div className="Correios-content">
                <div className={correiosData && (correiosData[0]["04227"].shipping_error_msg !== null || correiosData[0]["04227"].price_error_msg !== null) ? "unavailable" : ""}>
                    <strong><p>Mini Envios</p></strong>
                        <p><strong>Prazo:</strong> {correiosData && (correiosData[0]["04227"].delivery_expected_date && correiosData[0]["04227"].max_date)
                                ? `${correiosData[0]["04227"].delivery_expected_date} dias úteis - ${correiosData[0]["04227"].max_date}`: "-"}</p>  

                        <p><strong>Custo:</strong> {correiosData && correiosData[0]["04227"].price 
                                    ? `R$ ${correiosData[0]["04227"].price}` : "-"}</p>
                </div>

                <div className={correiosData && (correiosData[1]["03298"].shipping_error_msg !== null || correiosData[1]["03298"].price_error_msg !== null) ? "unavailable" : ""}>
                    <strong><p>PAC Contrato</p></strong>
                        <p><strong>Prazo: </strong>{correiosData && (correiosData[1]["03298"].delivery_expected_date && correiosData[1]["03298"].max_date)
                                ? `${correiosData[1]["03298"].delivery_expected_date} dias úteis - ${correiosData[1]["03298"].max_date}`: "-"}</p>
    
                        <p><strong>Custo:</strong> {correiosData && correiosData[1]["03298"].price 
                                    ? `R$ ${correiosData[1]["03298"].price}` : "-"}</p>
                </div>

                <div className={correiosData && (correiosData[2]["03220"].shipping_error_msg !== null || correiosData[2]["03220"].price_error_msg !== null) ? "unavailable" : ""}>
                    <strong><p>Sedex Contrato</p></strong>
                        <p><strong>Prazo:</strong> {correiosData && (correiosData[2]["03220"].delivery_expected_date && correiosData[2]["03220"].max_date)
                                ? `${correiosData[2]["03220"].delivery_expected_date} dias úteis - ${correiosData[2]["03220"].max_date}`: "-"}</p>

                        <p><strong>Custo:</strong> {correiosData && correiosData[2]["03220"].price 
                                    ? `R$ ${correiosData[2]["03220"].price}` : "-"}</p>
                </div>

                <div className={correiosData && (correiosData[3]["03204"].shipping_error_msg !== null || correiosData[3]["03204"].price_error_msg !== null) ? "unavailable" : ""}>
                    <strong><p>SEDEX 10</p></strong>     
                        <p><strong>Prazo:</strong> {correiosData && (correiosData[3]["03204"].delivery_expected_date && correiosData[3]["03204"].max_date)
                                ? `${correiosData[3]["03204"].delivery_expected_date} dias úteis - ${correiosData[3]["03204"].max_date}`: "-"}</p>
        
                        <p><strong>Custo:</strong> {correiosData && correiosData[3]["03204"].price 
                                    ? `R$ ${correiosData[3]["03204"].price}` : "-"}</p>
                </div>                                
            </div>
        )

        const container = document.createElement('div')
        const contentRoot = createRoot(container)

        contentRoot.render(content)
        outputRef.current.appendChild(container)
    }

    const JadlogInfo = () => {
        if(!outputRef.current) return

        while (outputRef.current.firstChild) {
            outputRef.current.removeChild(outputRef.current.firstChild);
        }

        const content = (
            <div className={JadlogData && JadlogData.error_msg !== null ? "unavailable" : ""}>
                <p><strong>Custo:</strong> {JadlogData && JadlogData.price ? `R$ ${JadlogData.price.toFixed(2)}` : "-"}</p>
                <p><strong>Prazo:</strong> {JadlogData && JadlogData.max_date ? `${JadlogData.max_date} dias úteis` : "-"}</p>            
            </div>
        )

        const container = document.createElement('div')
        const contentRoot = createRoot(container)

        contentRoot.render(content)
        outputRef.current.appendChild(container)
    }

    const handleClick = () => {
        if(!inputsRef.current) return
        const originId = (inputsRef.current?.querySelector('select[name="origin-zipcode"]') as HTMLSelectElement).value
        const deliveryMethod = (inputsRef.current?.querySelector('select[name="delivery-method"]') as HTMLSelectElement).value
        const weight = (inputsRef.current?.querySelector('select[name="weight"]') as HTMLSelectElement).value
        
        getShipmentAndPrice(originId, deliveryMethod, weight)
    }

    return (
        <>
            <div className="inputs-container" ref={inputsRef}>
                <p><strong>Simulador de Frete</strong></p>
                <div className="label-select-container">
                    <label htmlFor="origin-zipcode">Cep de origem:</label>
                    <select name="origin-zipcode">
                    <option value={"1"}>Coworking</option>
                    <option value={"2"}>Caixa Postal</option>
                    <option value={"3"}>Itaberaba</option>
                    <option value={"4"}>Praça</option>
                    <option value={"5"}>Expedição</option>
                    <option value={"6"}>Sorocaba</option>
                    <option value={"7"}>Parnamirim</option>
                    </select>
                </div>
                <div className="label-select-container">
                    <label htmlFor="delivery-method">Método de envio:</label>
                    <select name="delivery-method">
                    <option value={"Correios"}>Correios</option>
                    <option value={"Jadlog"}>Jadlog</option>
                    </select>
                </div>
                <div className="label-select-container">
                    <label htmlFor="weight">Peso:</label>
                    <select name="weight">
                    <option value={0.5}>0,5 kg</option>
                    <option value={1}>1 kg</option>
                    <option value={1.5}>1,5 kg</option>
                    <option value={2}>2 kg</option>
                    <option value={2.5}>2,5 kg</option>
                    <option value={3}>3 kg</option>
                    <option value={3.5}>3,5 kg</option>
                    <option value={4}>4 kg</option>
                    <option value={4.5}>4,5 kg</option>
                    <option value={5}>5 kg</option>
                    </select>
                </div>
                <button onClick={handleClick} id={"shipping-button"}>Consultar</button>
                <div className="output-container" ref={outputRef}>
                </div>
            </div>
        </>
    )
}