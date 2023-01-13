import { Workbook } from "exceljs";
import { toast } from "react-toastify"
import api from "../../../services/axios";

const downloadExcel = (data: {
  tracking_code: string;
  online_order_number: string;
  delivery_method: string;
  status: string;
  last_update_date: string;
  details: string;
  expected_date: string;
  delivery_expected_date: string;
  api_calling_date: string;
  observation: string;
}[]) => {
  const idToast = toast.info('Processando...')
  const orderNumbers = Array.from(new Set(data.map(row => row.online_order_number)))
  api.post('/api/tracking/read-for-excel', {
    order_numbers: orderNumbers
  })
    .then(response => response.data)
    .then(response => {
      const { columns, data: xlsxRows } = response as {columns: {[key: string]: string}, data: {
        tracking_code: string,
        online_order_number: string,
        [key: string]: string
      }[]}
      const xlsxColumns = Object
        .keys(xlsxRows[0])
        .map((key) => ({
          key: key,
          header: columns[key]
        }))
      const workbook = new Workbook()
      const worksheet = workbook.addWorksheet("Pedidos")
      toast.dismiss(idToast)

      worksheet.columns = [
        ...xlsxColumns,
        {key: "status", header: "Última movimentação"},
        {key: "last_update_date", header: "Data da movimentação"},
      ]
      const rows = xlsxRows.map(row => {
        const tracking = data.find(tracking => (tracking.tracking_code === row.tracking_code) && (tracking.online_order_number === row.online_order_number))

        return {
          ...row,
          status: tracking !== undefined ? tracking.status : null,
          last_update_date: tracking !== undefined ? tracking.last_update_date : null,
        }
      })
      worksheet.addRows(rows)

      workbook.xlsx.writeBuffer()
        .then(buffer => new Blob([buffer], {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'}))
        .then(blob => new File([blob], 'Rastreamento.xlsx'))
        .then(file => {
          const url = URL.createObjectURL(file)
          const anchor = document.createElement('a')
          anchor.href = url
          anchor.download = "Rastreamento.xlsx"
          anchor.click()

          URL.revokeObjectURL(url)
          toast.success('Excel gerado!')
        })
        .catch(_ => toast.error('Erro. Tente novamente ou contate o TI em caso de muitos erros...'))
    })
    .catch(_ => {
      toast.dismiss(idToast)
      toast.error('Algum erro interno ocorreu...')
    })
}

export default downloadExcel
