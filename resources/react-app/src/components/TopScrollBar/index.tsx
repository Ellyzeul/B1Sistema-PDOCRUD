import { UIEventHandler, useEffect, useRef } from "react"
import "./style.css"

export const TopScrollBar = () => {
  const scrollRef = useRef(null)
  const scrollContentRef = useRef(null)
  const table = document.querySelector(".table-responsive") as HTMLTableElement

  useEffect(() => {
    if(!scrollContentRef.current) return
    const scrollContent = scrollContentRef.current as HTMLDivElement
    const scrollWidth = table.scrollWidth

    scrollContent.style.width = `${scrollWidth}px`
  }, [scrollContentRef])

  const onScroll: UIEventHandler<HTMLDivElement> = (event) => {
    const scroll = event.target as HTMLDivElement
    table.scrollLeft = scroll.scrollLeft
  }

  table.addEventListener("scroll", (event) => {
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