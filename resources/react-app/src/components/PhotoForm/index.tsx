import { ChangeEventHandler, FormEventHandler, useRef, useState } from "react"
import { toast, ToastContainer } from "react-toastify"
import api, { postFile } from "../../services/axios"
import "./style.css"
import WebcamModal from "../WebcamModal"

export const PhotoForm = () => {
  const inputRef = useRef(null as HTMLInputElement | null)
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [imageFile, setImageFile] = useState(null as File | null)

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

    if(!imageFile) {
      toast.error("Selecione alguma imagem")
      return
    }

    const imageName = (form[0] as HTMLInputElement).value
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

    input.value = value.substring(29, 34)
  }

  const onChangePhotoInput: ChangeEventHandler = (event) => {
    setImageFile(((event.target as HTMLInputElement).files as FileList)[0])
  }

  const onClickMobile = () => {
    if(!inputRef.current) return
    
    inputRef.current.click()
  }

  const onClickComputer = () => setIsModalOpen(true)

  const handleWebcamImage = (imageURL: string) => {
    if(!inputRef.current) return
    const input = inputRef.current
    
    setImageFile(dataURLtoFile(imageURL, 'image.png'))
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
        <div id="buttons-container">
          <p>Dispositivo para enviar a foto</p>
          <i className="fa-solid fa-mobile-screen-button mobile-button" onClick={onClickMobile}/>
          <i className="fa-solid fa-computer computer-button" onClick={onClickComputer}/>
          <input 
            ref={inputRef}
            onChange={onChangePhotoInput}
            type="file" 
            name="photo-image"
            id="photo-image"
            accept="image/jpeg, image/jpg, image/png"
          />
          <WebcamModal is_open={isModalOpen} set_is_open={setIsModalOpen} handle_image={handleWebcamImage}/>
        </div>
        <input type="submit" value="Salvar" />
      </form>
      <ToastContainer/>
    </>
  )
}

const dataURLtoFile = (dataURL: string, filename: string) => {
  const arr = dataURL.split(',')
  const mime = (arr[0].match(/:(.*?);/) as RegExpMatchArray)[1]
  const bstr = atob(arr[arr.length - 1]) 
  let n = bstr.length;
  const u8arr = new Uint8Array(n);

  while(n--) u8arr[n] = bstr.charCodeAt(n)

  return new File([u8arr], filename, {type:mime});
}
