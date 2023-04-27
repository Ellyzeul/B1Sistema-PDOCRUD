import { ChangeEventHandler, MouseEventHandler, useEffect, useRef, useState } from "react"
import { toast, ToastContainer } from "react-toastify"
import { Navbar } from "../../../components/Navbar"
import { PhotoDisplay } from "../../../components/PhotoDisplay"
import api from "../../../services/axios"
import "./style.css"
import { PhotosSearchResponse } from "./types"

export const PhotosSearchPage = () => {
  const inputRef = useRef(null)
  const buttonRef = useRef(null)
  const [photosElem, setPhotos] = useState([] as JSX.Element[])

  useEffect(() => {
    if(!inputRef.current || !buttonRef.current) return
    const getParams = new URLSearchParams(window.location.search)
    const photoNumber = getParams.get("photo_number")
    if(!photoNumber) return
    const input = inputRef.current as HTMLInputElement
    const button = buttonRef.current as HTMLButtonElement

    input.value = photoNumber
    button.click()
  }, [inputRef, buttonRef])

  const searchPhotos: MouseEventHandler = event => {
    if(!inputRef.current) return
    const input = inputRef.current as HTMLInputElement
    const namePattern = input.value
    if(namePattern === "") return

    api.get(`/api/photo/read?name_pattern=${namePattern}`)
      .then(response => response.data as PhotosSearchResponse)
      .then(response => {
        const { message, photos } = response
        toast.success(message)
        let i = 0
        setPhotos(photos.map(url => <PhotoDisplay url={url} key={i++} />))
      })
  }

  const onChange: ChangeEventHandler = (event) => {
    const input = event.target as HTMLInputElement
    const value = input.value

    if(value.length !== 44) return

    input.value = `${Number(value.substring(25, 34))}`
  }

  return (
    <div className="photos-search-page">
      <Navbar items={[{label: "Fotos", options: [{name:"Enviar", url: "/expedicao/fotos/enviar"}]}]} />
      <div className="photos-search-content">
        <div className="search-container">
          <input ref={inputRef} className="photo-search" type="text" onChange={onChange} />
          <button ref={buttonRef} onClick={searchPhotos}>Pesquisar</button>
          <ToastContainer />
        </div>
        <div className="bottom-container">
          <p>Fotos pesquisadas</p>
          <div className="display-container">
            <div>
              {photosElem}
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}
