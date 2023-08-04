import { useEffect } from "react"
import api from "../../../services/axios"
import "./style.css"
import { FormProps } from "./types"

export const Form = (props: FormProps) => {
    const { orderNumber } = props
    let id_company = 0
     
    useEffect(() => {
        api.get(`/api/orders/get-order-addresses?order_number=${orderNumber}`)
            .then(response => response)
            .then(response => {
                console.log(response)
            })
            .catch(error => console.error(error))
    },[])

    const handleClick = () => {
        console.log("Envia para o Bling")
    }

    return (
        <div className="form-container">
            <div className="send-button" onClick={handleClick}>Enviar</div>
            <div className="contact-panel">
                <strong className="panel-section-header">Dados do Cliente</strong>
                <span className="address-panel-order-number">ORIGEM: {} ({id_company === 0 ? <img src="/seline_white_bg.png" width="20" height="20"/> : <img src="/b1_white_bg.png" width="20" height="20"/>}) - Canal de Venda: {<a target="_blank" href={""} rel="noreferrer">{"salesChannel"}</a>}</span>
                <span>Nome</span>
                <input name={"Nome"} type={"text"} defaultValue={""} />
                <span>CPF/CNPJ</span>
                <input name={"cpf_cnpj"} type={"text"} defaultValue={""} />

                <div>
                    <span><label htmlFor="person_type">Tipo de Pessoa:</label></span>
                    <select name="person_type" id="person_type">
                    <option value="F">F</option>
                    <option value="J">J</option>
                    <option value="E">EX</option>
                    </select>
                </div>
                <span>Celular</span>
                <input name={"phone"} type={"text"} defaultValue={""} />
                <span>Email</span>
                <input name={"email"} type={"text"} defaultValue={""} />
                <span>Endereço</span>
                <input name={"address"} type={"text"} defaultValue={""} />
                <span>CEP</span>
                <input name={"postal_code"} type={"text"} defaultValue={""} />
                <span>Número</span>
                <input name={"number"} type={"text"} defaultValue={""} />
                <span>Bairro</span>
                <input name={"county"} type={"text"} defaultValue={""} />
                <span>UF</span>
                <input name={"uf"} type={"text"} defaultValue={""} />
                <span>Cidade</span>
                <input name={"city"} type={"text"} defaultValue={""} />
                <span>Complemento</span>
                <input name={"complement"} type={"text"} defaultValue={""} />  
                <span>País</span>
                <input name={"country"} type={"text"} defaultValue={""} />                               
            </div>
            <div className="order-container">
                <strong className="panel-section-header">Itens</strong>
            </div>
        </div>
    )
}