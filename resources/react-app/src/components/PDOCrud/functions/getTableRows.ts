let rows = null as (Array<HTMLTableRowElement> | null)

const getTableRows = (): Array<HTMLTableRowElement> => {
  if(!rows) {
    rows = Array.from(
      document.querySelectorAll(".pdocrud-table > tbody > tr") as NodeListOf<HTMLTableRowElement>
    )
  }

  return rows
}

export default getTableRows
