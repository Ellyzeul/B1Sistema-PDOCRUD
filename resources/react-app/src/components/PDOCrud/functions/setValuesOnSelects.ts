const setValuesOnSelects = () => {
	const selects = document.querySelectorAll('.pdocrud-row-cols > select') as NodeListOf<HTMLSelectElement>

	selects.forEach(select => {
		const val = select.dataset.originalVal
		const options = Array.from(select.options)

		options.forEach(option => option.value === val ? option.selected = true : null)
	})
}

export default setValuesOnSelects
