import { useEffect, useState } from "react"
import { toast } from "react-toastify"
import api from "../../../../../../services/axios"
import "./style.css"
import { AdressData, ZipCodeConsultationProp } from "./types"

export const ZipCodeConsultation = (props: ZipCodeConsultationProp) => {
    const { bling_postal_code, country } = props
    const [addressData, setAdressData] = useState({} as AdressData)

    const fetchAdress = () => {
        console.log(country)
        if(country === "BR"){
            api.get(`/api/tracking/consult-zipcode?zip_code=${bling_postal_code}`)
            .then(response => {
                setAdressData(response.data)
            })
            .catch(error => { toast.error("Erro ao consultar dados de endereço")})
        }
        else toast.error("Consulta do endereço indisponível")
    }

    return (
        <>
        <strong  className="address-panel-section-header">Consulta do Endereço</strong>
        <div className="zipcode-external-container">
        <div className="zipcode-container">
            <div className="zipcode-info-container">
                <div className="info">
                    <strong>CEP</strong>
                    <p>{addressData.cep}</p>
                </div>
                <div className="info">
                    <strong>Logradouro/Nome</strong>
                    <p>{addressData.logradouro}</p>
                </div>
                <div className="info">
                    <strong>Bairro/Distrito</strong>
                    <p>{addressData.bairro}</p>
                </div>
                <div className="info">
                    <strong>Localidade/UF</strong>
                    <p>{addressData.bairro} {addressData.uf}</p>
                </div>
            </div>
        </div>
            <button id="fetch-adress-button" onClick={fetchAdress}>Consultar</button>
        </div>
        </>
    )
}