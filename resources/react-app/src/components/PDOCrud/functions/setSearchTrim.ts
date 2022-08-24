const setSearchTrim = () => {
  const searchInput = document.querySelector("#pdocrud_search_box") as HTMLInputElement
  let trim = false

  searchInput.onpaste = () => {
    trim = true
  }

  searchInput.oninput = () => {
    if(!trim) return
    searchInput.value = searchInput.value.trim()
    trim = false
  }
}

export default setSearchTrim
