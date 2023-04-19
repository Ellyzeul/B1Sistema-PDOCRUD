import { tabContainerProp } from "./types"
import "./style.css"

export const TabContainer = (props: tabContainerProp) => {
    const { tabContent } = props
    return (
        <div className="info-rotine-container">
            <>{tabContent}</>
        </div>
    )
}