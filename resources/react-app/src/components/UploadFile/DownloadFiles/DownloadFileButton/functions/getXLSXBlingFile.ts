import { Workbook } from "exceljs"
import XLSXFileData from "./types"

const FILENAME = 'Estante Virtual - Bling.xlsx'

const getXLSXBlingFile = (xlsxData: XLSXFileData) => {
  const { files_headers, data } = xlsxData
  const fileHeaders = files_headers['Bling']
  const workbook = new Workbook()
  const worksheet = workbook.addWorksheet('Dados Bling')

  worksheet.columns = fileHeaders
  worksheet.addRows(data.map(registry => formatRegistry(registry)))

  return workbook.xlsx.writeBuffer()
    .then(buffer => new Blob([buffer], {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'}))
    .then(blob => ({
      file: new File([blob], FILENAME), 
      name: FILENAME
    }))
}

const formatRegistry = (registry: {[key: string]: string}) => {
  registry["Data de pagamento"] = registry["Data de pagamento"]
  registry["Valor Unitário"] = registry["Preço do Livro (R$)"]
  registry["Logradouro Entrega"] = registry["Logradouro"]
  registry["Número Entrega"] = registry["Número"]
  registry["Complemento Entrega"] = registry["Complemento"]
  registry["Cidade Entrega"] = registry["Cidade"]
  registry["CEP Entrega"] = registry["CEP"]
  registry["Bairro Entrega"] = registry["Bairro"]
  registry["UF Entrega"] = registry["UF"]
  registry["Forma de pagamento"] = registry["Método de pagamento"]
  registry["unity"] = "UN"
  registry["quantity"] = "1"
  registry["discount"] = "0"
  registry["parcel_quantity"] = "0"

  return registry
}

export default getXLSXBlingFile
