import { ChangeEventHandler, FormEventHandler } from "react"
import { toast, ToastContainer } from "react-toastify"
import api, { postFile } from "../../services/axios"
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
    event.preventDefault()
    const form = event.target as HTMLFormElement
    const fileList = (form[1] as HTMLInputElement).files as FileList

    if(fileList.length === 0) {
      toast.error("Selecione alguma imagem")
      return
    }

    const imageName = (form[0] as HTMLInputElement).value
    const imageFile = fileList[0]
    const formData = new FormData()

    const photoFile = renameImage(imageFile, imageName)
    const photoName = photoFile.name
    formData.append("photo", photoFile, photoName)

    postFile("/api/photo/create", formData)
      .then(response => response.data)
      .then(response => {
        const { message } = response
        toast.success(message)
      })
  }

  const onChange: ChangeEventHandler = (event) => {
    const input = event.target as HTMLInputElement
    const value = input.value

    if(value.length !== 44) return

    input.value = `${Number(value.substring(25, 34))}`
  }

  return (
    <>
      <form 
        className="photos-form" 
        onSubmit={onSubmit}
      >
        <div>
          <label htmlFor="photo-name">Nome da foto</label>
          <input type="text" name="photo-name" id="photo-name" onChange={onChange} />
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
