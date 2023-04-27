import { Navbar } from "../../../components/Navbar"
import { PhotoForm } from "../../../components/PhotoForm"
import "./style.css"

export const PhotosUploadPage = () => {
  return (
    <div className="photos-upload-page">
      <Navbar items={[{label: "Fotos", options: [{name: "Pesquisar", url: "/expedicao/fotos/pesquisar"}]}]} />
      <div className="photos-upload-content">
        <PhotoForm />
      </div>
    </div>
  )
}
