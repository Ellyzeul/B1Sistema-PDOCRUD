import { TopScrollBar } from "../../TopScrollBar";
import { createRoot } from "react-dom/client";

const setTopScrollBar = (panelBody: HTMLDivElement) => {
	const toScroll = panelBody.children[2] as HTMLDivElement
	const scrollbarContainer = document.createElement("div")

	scrollbarContainer.style.height= "20px"
	panelBody.insertBefore(scrollbarContainer, toScroll)
	const scrollbarRoot = createRoot(scrollbarContainer)
	scrollbarRoot.render(<TopScrollBar/>)
}

export default setTopScrollBar
