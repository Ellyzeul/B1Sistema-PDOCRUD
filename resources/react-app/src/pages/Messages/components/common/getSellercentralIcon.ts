const getSellercentralIcon = (name: string) => sellercentralIcons[name]

export default getSellercentralIcon

const sellercentralIcons = {
  'MercadoLivre-BR': '/icons/sellercentrals/mercado-livre.png', 
  'FNAC-PT': '/icons/sellercentrals/fnac.png', 
  'FNAC-ES': '/icons/sellercentrals/fnac.png',  
  'FNAC-FR': '/icons/sellercentrals/fnac.png', 
} as { [key: string]: string }