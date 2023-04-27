export type ShipmentLabelProp = {
  order_id: string, 
  company: {
    id: number, 
    name: string, 
    company_name: string, 
    cnpj: string, 
    state_registration: string, 
    municipal_registration: string
  }, 
  id_delivery_method: number, 
  bling_data: BlingData
}

export type BlingData = {
  desconto?: string,
  observacoes?: string,
  observacaointerna?: string,
  data?: string,
  numero?: string,
  numeroPedidoLoja?: string,
  vendedor?: string,
  valorfrete?: string,
  totalprodutos?: string,
  totalvenda?: string,
  situacao?: string,
  loja?: string,
  dataPrevista?: string,
  tipoIntegracao?: string,
  cliente?: {  
    nome?: string,
    cnpj?: string,
    ie?: string,
    rg?: string,
    endereco?: string,
    numero?: string,
    complemento?: string,
    cidade?: string,
    bairro?: string,
    cep?: string,
    uf?: string,
    email?: string,
    celular?: string,
    fone?: string
  },
  itens?: {  
    item: {  
      codigo: string,
      descricao: string,
      quantidade: string,
      valorunidade: string,
      precocusto: string,
      descontoItem: string,
      un: string,
      pesoBruto: string,
      largura: string,
      altura: string,
      profundidade: string,
      unidadeMedida: string,
      descricaoDetalhada: string
    }
  }[],
  parcelas?: {  
    parcela?: {  
      idLancamento?: string,
      valor?: string,
      dataVencimento?: string,
      obs?: string,
      destino?: string,
      forma_pagamento?: {  
        id?: string,
        descricao?: string,
        codigoFiscal?: number
      }
    }
  }[],
  nota?: {  
    serie?: string,
    numero?: string,
    dataEmissao?: string,
    situacao?: string,
    chaveAcesso?: string,
    valorNota?:  number
  },
  transporte?: {  
    transportadora?: string,
    cnpj?: string,
    tipo_frete?: string,
    qtde_volumes?: string,
    volumes?: {  
      volume?: {  
        id?: string,
        idServico?: string,
        servico?: string,
        codigoServico?: string,
        codigoRastreamento?: string,
        dataSaida?: string,
        prazoEntregaPrevisto?: string,
        valorFretePrevisto?: string,
        valorDeclarado?: string,
        remessa?: {  
          numero?: string,
          dataCriacao?: string
        },
        dimensoes?: {  
          peso?: string,
          altura?: string,
          largura?: string,
          comprimento?: string,
          diametro?: 0
        },
        urlRastreamento?: string
      }
    }[],
    enderecoEntrega?: {  
      nome?: string,
      endereco?: string,
      numero?: string,
      complemento?: string,
      cidade?: string,
      bairro?: string,
      cep?: string,
      uf?: string
    }
  },
  codigosRastreamento?: {
    codigoRastreamento?: string
  }
}
