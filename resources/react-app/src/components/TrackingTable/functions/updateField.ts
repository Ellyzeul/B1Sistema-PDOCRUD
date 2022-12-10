import { toast } from "react-toastify"
import api from "../../../services/axios"

const updateField = (trackingCode: string, input: HTMLInputElement, field: string) => {
  const value = input.value

  api.post('/api/tracking/update-field', {
    tracking_code: trackingCode,
    field: field,
    value: value
  })
    .then(_ => toast.success('Observação atualizada!'))
    .catch(_ => toast.error('Erro ao salvar a observação...'))
}

export default updateField
