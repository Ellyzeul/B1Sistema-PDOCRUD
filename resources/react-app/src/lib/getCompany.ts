export default function getCompany(idCompany: string | number | undefined, bling: boolean = false) {
  if(idCompany === undefined) {
    throw new Error('ID de empresa indefinido')
  }

  const id = Number(idCompany)

  if(bling && id > 1) {
    return 'b1'
  }

  if(!COMPANIES[id]) {
    throw new Error(`Empresa de ID ${id} não está registrada...`)
  }

  return COMPANIES[id]
}

const COMPANIES: Record<number, string> = {
  0: 'seline',
  1: 'b1',
  2: 'j1',
  3: 'r1',
}
