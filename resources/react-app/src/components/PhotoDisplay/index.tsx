import { PhotoDisplayProp } from "./types"
import "./style.css"
import { MouseEventHandler, useRef } from "react"
import api from "../../services/axios"
import { toast, ToastContainer } from "react-toastify"

export const PhotoDisplay = (props: PhotoDisplayProp) => {
  const { url } = props
  const altText = "Imagem não pôde ser carregada. Clique com botão direito e 'Abrir imagem em nova aba' para ver"
  const overlayRef = useRef(null)
  const deleteOverlayRef = useRef(null)
  const imgContainerRef = useRef(null)
  const imgRef = useRef(null)

  const onMouseOver: MouseEventHandler = () => {
    if(!overlayRef.current) return
    if(!isHidden) return 
    const overlay = overlayRef.current as HTMLDivElement

    overlay.style.visibility = "visible"
  }

  const onMouseLeave: MouseEventHandler = () => {
    if(!overlayRef.current) return
    const overlay = overlayRef.current as HTMLDivElement

    overlay.style.visibility = "hidden"
  }

  const onClick: MouseEventHandler = () => {
    const rawFilename = (url.match(/\/([A-Za-z0-9_-\s])*\.(jpg|png|jpeg)/) as string[])[0]
    const filename = rawFilename.substring(1)
    const a = document.createElement('a')

    a.href = url
    a.target = "_blank"
    a.download = filename
    a.click()
  }

  const hideDelete: MouseEventHandler = () =>  {
    if(!deleteOverlayRef.current) return
    const deleteOverlay = deleteOverlayRef.current as HTMLDivElement

    deleteOverlay.style.visibility = "hidden"
  }

  const unhideDelete: MouseEventHandler = () => {
    if(!deleteOverlayRef.current) return
    const deleteOverlay = deleteOverlayRef.current as HTMLDivElement

    deleteOverlay.style.visibility = "visible"
  }

  const isHidden = (): Boolean => {
    if(!deleteOverlayRef.current) return false
    const deleteOverlay = deleteOverlayRef.current as HTMLDivElement

    return deleteOverlay.style.visibility === "hidden" ? true : false
  }

  const deletePhotos = () => {
    if(!imgContainerRef.current) return
    if(!imgRef.current) return
    const imgContainer = imgContainerRef.current as HTMLDivElement
    const img = imgRef.current as HTMLImageElement

    imgContainer.remove()

    api.delete(`api/photo/exclude?photoName=${img.src.split('/').pop()}`)
    .then(response => response.data.message)
    .then(toast.success)
  }

  return (
    <div 
      className="image-display" 
      onMouseOver={onMouseOver}
      onMouseLeave={onMouseLeave}
      ref={imgContainerRef}
    >
      <img 
        src={url} 
        alt={altText} 
        ref={imgRef}
      />
      <div 
        className="image-overlay" 
        ref={overlayRef} 
        onClick={onClick}
      >
        Clique para baixar
      </div>
      <div className="image-overlay-delete">
        <i className="fa-solid fa-trash" onClick={unhideDelete}></i>
      </div> 
      <div className="image-overlay-delete-confirm" ref={deleteOverlayRef}>
        <p>Deseja mesmo deletar essa foto?</p>
        <button className="image-overlay-delete-button" onClick={deletePhotos}>Deletar</button>
        <button className="image-overlay-cancel-button" onClick={hideDelete}>Cancelar</button>
      </div>
    </div>
  )
}