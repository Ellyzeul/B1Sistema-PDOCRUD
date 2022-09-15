const getTableHeaders = (): HTMLTableRowElement => {
  const headers = document.querySelector(".pdocrud-table > thead > tr") as HTMLTableRowElement

  return headers
}

export default getTableHeaders
