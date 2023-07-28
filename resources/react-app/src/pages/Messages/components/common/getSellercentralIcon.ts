const getSellercentralIcon = (name: string) => sellercentralIcons[name]

export default getSellercentralIcon

const sellercentralIcons = {
  'mercado-livre': '/icons/sellercentrals/mercado-livre.png', 
  'fnac': '/icons/sellercentrals/fnac.png', 
} as { [key: string]: string }