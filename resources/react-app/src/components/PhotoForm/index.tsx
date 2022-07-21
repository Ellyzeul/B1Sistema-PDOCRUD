import { FormEventHandler } from "react"
import { toast, ToastContainer } from "react-toastify"
import api from "../../services/axios"
import "./style.css"

export const PhotoForm = () => {
  const renameImage = (img: File, newName: string): File => {
    const imgType = img.type
    const blob = img.slice(0, img.size, imgType)
    const imgExt = (imgType.match(/(png|jpeg|jpg)/) as RegExpExecArray)[0]
    const renamed = new File(
      [blob], 
      `${newName}.${imgExt === "jpeg" ? "jpg" : imgExt}`, 
      {type: imgType}
    )

    return renamed
  }

  const onSubmit: FormEventHandler = (event) => {
    const form = event.target as HTMLFormElement
    const fileList = (form[1] as HTMLInputElement).files as FileList

    if(fileList.length === 0) {
      toast.error("Selecione alguma imagem")
      return
    }

    const imageName = (form[0] as HTMLInputElement).value
    const imageFile = fileList[0]

    const toUpload = renameImage(imageFile, imageName)
    const photoURL = URL.createObjectURL(toUpload)
    const photoName = toUpload.name

    api.post("/photo/create", {
      photo_url: photoURL,
      photo_name: photoName
    })
      .then(() => {
        URL.revokeObjectURL(photoURL)
        toast.success("Imagem salva!")
      })
  }

  return (
    <>
      <form 
        className="photos-form" 
        onSubmit={onSubmit}
      >
        <div>
          <label htmlFor="photo-name">Nome da foto</label>
          <input type="text" name="photo-name" id="photo-name" />
        </div>
        <div>
          <label htmlFor="photo-image">Foto a ser postada</label>
          <input 
            type="file" 
            name="photo-image"
            id="photo-image"
            accept="image/jpeg, image/jpg, image/png"
          />
        </div>
        <input type="submit" value="Salvar" />
      </form>
      <ToastContainer/>
    </>
  )
}
