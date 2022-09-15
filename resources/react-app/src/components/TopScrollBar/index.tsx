import { UIEventHandler, useEffect, useRef } from "react"
import "./style.css"

export const TopScrollBar = () => {
  const scrollRef = useRef(null)
  const scrollContentRef = useRef(null)
  const tableDiv = document.querySelector(".table-responsive") as HTMLDivElement

  useEffect(() => updateScrollWidth(), [scrollContentRef, tableDiv])

  const updateScrollWidth = () => {
    if(!scrollContentRef.current) return
    const scrollContent = scrollContentRef.current as HTMLDivElement
    const scrollWidth = tableDiv.scrollWidth

    scrollContent.style.width = `${scrollWidth}px`
  }

  const onScroll: UIEventHandler<HTMLDivElement> = (event) => {
    const scroll = event.target as HTMLDivElement
    tableDiv.scrollLeft = scroll.scrollLeft
    updateScrollWidth() // Ineficiente, corrigir futuramente
  }

  tableDiv.addEventListener("scroll", (event) => {
    if(!scrollRef.current) return

    const toScroll = event.target as HTMLDivElement
    const scroll = scrollRef.current as HTMLDivElement

    scroll.scrollLeft = toScroll.scrollLeft
  })

  return (
    <div 
      className={"top-scroll-bar"} 
      ref={scrollRef} 
      onScroll={onScroll}
    >
      <div 
        className={"top-scroll-bar-content"} 
        ref={scrollContentRef}
      ></div>
    </div>
  )
}