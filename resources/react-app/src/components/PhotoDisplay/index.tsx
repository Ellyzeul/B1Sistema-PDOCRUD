import { PhotoDisplayProp } from "./types"
import "./style.css"

export const PhotoDisplay = (props: PhotoDisplayProp) => {
  const { url } = props

  return (
    <img className="image-display" src={url} alt="Imagem não pôde ser carregada. Clique com botão direito e 'Abrir imagem em nova aba' para ver" />
  )
}