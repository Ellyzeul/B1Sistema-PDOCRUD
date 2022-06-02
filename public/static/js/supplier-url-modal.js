const supplierURLModal = document.querySelector('#purchase_link_modal')
const supplierURLModalId = document.querySelector('#purchase_link_form_id')
const supplierURLModalOnlineOrderNumber = document.querySelector('#purchase_link_form_online_order_number > span')
const supplierURLModalURLInput = document.querySelector('#purchase_link_formsupplier_url')


const urlModalOpen = (event) => {
    const td = event.target
    const row = td.parentElement.children
    const pos = Array.from(row).indexOf(td)
    
    if(pos === 8) {
        supplierURLModal.style.visibility = 'visible'
        document.body.style.overflowY = 'hidden'
        supplierURLModalId.textContent = row[0].textContent
        supplierURLModalOnlineOrderNumber.textContent = row[4].textContent
        const id = row[0].textContent.match(/[0-9]{1,}/)[0]
        fetch(`/api/supplier_url/read?id=${id}`, {method: 'GET'})
            .then(response => response.json())
            .then(response => {
                console.log(response)
                supplierURLModalURLInput.value = response.url
            })
        return
    }
}
const urlModalClose = () => {
    supplierURLModal.style.visibility = 'hidden'
    supplierURLModalId.textContent = ''
    supplierURLModalOnlineOrderNumber.textContent = ''
    supplierURLModalURLInput.value = ''
    document.body.style.overflowY = 'auto'
}
const closeModals = event => {
    const key = event.key
    
    if(key === 'Escape') {
        urlModalClose()
    }
}
const saveUrlModal = event => {
    event.preventDefault()
    const idRaw = supplierURLModalId.textContent
    const id = idRaw.match(/[0-9]{1,}/)[0]
    const supplierURL = supplierURLModalURLInput.value

    fetch('/api/supplier_url/update', {
        method: 'POST',
        body: JSON.stringify({
            id: id,
            supplier_url: supplierURL
        }),
        headers: {
            "Content-Type": "application/json"
        }
    })
    .then(() => urlModalClose())
}
