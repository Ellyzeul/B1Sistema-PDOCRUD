import { PhotoDisplayProp } from "./types"
import "./style.css"
import { MouseEventHandler, useRef } from "react"

export const PhotoDisplay = (props: PhotoDisplayProp) => {
  const { url } = props
  const altText = "Imagem não pôde ser carregada. Clique com botão direito e 'Abrir imagem em nova aba' para ver"
  const overlayRef = useRef(null)

  const onMouseOver: MouseEventHandler = () => {
    if(!overlayRef.current) return
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

  return (
    <div 
      className="image-display" 
      onMouseOver={onMouseOver}
      onMouseLeave={onMouseLeave}
    >
      <img 
        src={url} 
        alt={altText} 
      />
      <div 
        className="image-overlay" 
        ref={overlayRef} 
        onClick={onClick}
      >
        Clique para baixar
      </div>
      {/* <div className="image-overlay-delete">
        <i className="fa-solid fa-trash"></i>
      </div> */}
      {/* <div className="image-overlay-delete-confirm">
        <p>Deseja mesmo deletar essa foto?</p>
        <button className="image-overlay-delete-button">Deletar</button>
        <button className="image-overlay-cancel-button">Cancelar</button>
      </div> */}
    </div>
  )
}