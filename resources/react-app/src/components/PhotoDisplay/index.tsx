import { PhotoDisplayProp } from "./types"
import "./style.css"
import { MouseEventHandler, useRef } from "react"
import api from "../../services/axios"
import { toast, ToastContainer } from "react-toastify"

export const PhotoDisplay = (props: PhotoDisplayProp) => {
  const { url } = props
  const altText = "Imagem não pôde ser carregada. Clique com botão direito e 'Abrir imagem em nova aba' para ver"
  const overlayRef = useRef(null)
  const trashCanRef = useRef(null)
  const deleteOverlayRef = useRef(null)
  const imgContainerRef = useRef(null)
  const imgRef = useRef(null)

  const onMouseEnterOverlay: MouseEventHandler = () => {
    if(!overlayRef.current) return
    if(!trashCanRef.current) return
    if(!isHidden) return
    const overlay = overlayRef.current as HTMLDivElement
    const trashCan = trashCanRef.current as HTMLDivElement

    trashCan.style.visibility = "visible"
    overlay.style.visibility = "visible"
  }

  const onMouseLeaveOverlay: MouseEventHandler = () => {
    if(!overlayRef.current) return
    if(!trashCanRef.current) return
    const overlay = overlayRef.current as HTMLDivElement
    const trashCan = trashCanRef.current as HTMLDivElement

    trashCan.style.visibility = "hidden"
    overlay.style.visibility = "hidden"
  }

  const onMouseEnterTrashCan = () => {
    if(!trashCanRef.current) return
    const trashCan = trashCanRef.current as HTMLDivElement

    trashCan.style.backgroundColor = "white"
    trashCan.style.color = "red"
  }

  const onMouseLeaveTrashCan = () => {
    if(!trashCanRef.current) return
    const trashCan = trashCanRef.current as HTMLDivElement

    trashCan.style.backgroundColor = "red"
    trashCan.style.color = "white"
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
    const display = imgContainer.parentElement as HTMLDivElement
    const img = imgRef.current as HTMLImageElement
    const photoName = img.src.split('/').pop()

    display.removeChild(imgContainer)

    api.delete(`api/photo/exclude?photoName=${photoName}`)
      .then(response => response.data.message)
      .then(toast.success)
      .catch(() => {
        display.appendChild(imgContainer)
        toast.error("Algum erro ocorreu na exclusão da foto...")
      })
  }

  return (
    <div 
      className="image-display" 
      onMouseEnter={onMouseEnterOverlay}
      onMouseLeave={onMouseLeaveOverlay}
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
      <div 
        className="image-overlay-delete" 
        ref={trashCanRef} 
        onClick={unhideDelete} 
        onMouseEnter={onMouseEnterTrashCan}
        onMouseLeave={onMouseLeaveTrashCan}
      >
        <i className="fa-solid fa-trash"></i>
      </div> 
      <div className="image-overlay-delete-confirm" ref={deleteOverlayRef}>
        <p>Deseja mesmo deletar essa foto?</p>
        <button className="image-overlay-delete-button" onClick={deletePhotos}>Deletar</button>
        <button className="image-overlay-cancel-button" onClick={hideDelete}>Cancelar</button>
      </div>
    </div>
  )
}