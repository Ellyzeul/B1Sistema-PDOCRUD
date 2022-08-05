import { MouseEventHandler, useRef } from "react"
import "./style.css"
import { SectionButtonProp } from "./types"

export const SectionButton = (props: SectionButtonProp) => {
  const { label, icon, url } = props
  const iconRef = useRef(null)

  const onClick: MouseEventHandler = () => {
    window.location.pathname = url
  }

  const onMouseEnter: MouseEventHandler = () => {
    if(!iconRef.current) return
    const img = iconRef.current as HTMLImageElement

    img.style.filter = "invert(1)"
  }

  const onMouseLeave: MouseEventHandler = () => {
    if(!iconRef.current) return
    const img = iconRef.current as HTMLImageElement

    img.style.filter = "invert(0)"
  }

  return (
    <div 
      className={"section-button"} 
      onClick={onClick} 
      onMouseEnter={onMouseEnter}
      onMouseLeave={onMouseLeave}
    >
      <div><img ref={iconRef} src={icon} alt="" draggable={false} /></div>
      <strong>{label}</strong>
    </div>
  )
}