const getCompanyIcon = (name: string) => companiesIcons[name]

export default getCompanyIcon

const companiesIcons = {
  'seline': '/seline_white_bg.png', 
  'b1': '/b1_logo.png'
} as { [key: string]: string }
