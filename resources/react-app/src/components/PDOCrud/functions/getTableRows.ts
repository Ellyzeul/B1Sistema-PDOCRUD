const getTableRows = (): Array<HTMLTableRowElement> => {
  const rows = Array.from(
    document.querySelectorAll(".pdocrud-table > tbody > tr") as NodeListOf<HTMLTableRowElement>
  )

  return rows
}

export default getTableRows
