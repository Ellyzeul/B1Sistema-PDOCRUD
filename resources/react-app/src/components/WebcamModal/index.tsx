import { WebcamModalProp } from "./types"
import "./style.css"
import { MouseEventHandler, useEffect, useRef } from "react"

const WebcamModal = (props: WebcamModalProp) => {
  const { is_open, set_is_open, handle_image } = props
  const videoRef = useRef(null as HTMLVideoElement | null)
  const canvas = document.createElement('canvas')

  useEffect(() => {
    if(!videoRef.current || !is_open) return
    const video = videoRef.current

    navigator.mediaDevices.getUserMedia({ video: true })
      .then(stream => {
        video.srcObject = stream
        video.play()
      })
  }, [videoRef, is_open])

  const onClickClose = () => set_is_open(false)

  const onClickButton: MouseEventHandler = (event) => {
    event.preventDefault()
    if(!videoRef.current) return
    const video = videoRef.current

    canvas.width = video.videoWidth
    canvas.height = video.videoHeight
    canvas.getContext('2d')?.drawImage(video, 0, 0, canvas.width, canvas.height)

    handle_image(canvas.toDataURL())
    set_is_open(false)
  }

  return (
    <div className="webcam-modal" style={{ display: is_open ? 'grid' : 'none' }}>
      <div className="webcam-modal-container">
        <i className="fa-solid fa-x modal-close" onClick={onClickClose}/>
        <video ref={videoRef} className="webcam" autoPlay/>
        <button onClick={onClickButton}>Fotografar</button>
      </div>
    </div>
  )
}

export default WebcamModal
