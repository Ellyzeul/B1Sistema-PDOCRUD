import { useEffect, useRef, useState } from "react"
import { TabManagerProp } from "./types"
import { TabContainer } from "./TabContainer"
import "./style.css"

export const TabManager = (props: TabManagerProp) => {
    const {contents, options} = props.props
    const [buttons, setButtons] = useState([] as JSX.Element[])
    const [tab, setTab] = useState(<p>Navegue pelas abas para identificar prioridades na rotina diária da saude da conta de cada canal de venda ativo!</p>)
    const tabsButtonsRef = useRef(null)
    
    const handleClick = (target: HTMLButtonElement, contentKey: string) => {
        setTab(contents[contentKey])

        if(!tabsButtonsRef.current) return
        const btnContainer = tabsButtonsRef.current as HTMLDivElement
        const buttons = btnContainer.childNodes as NodeListOf<HTMLButtonElement>

        buttons.forEach(button => button.className = '')
        target.className = 'selectedTab'
    }

    useEffect(() => {
        setButtons(options.map(({key, label}) => <button key={key} onClick={event => handleClick(event.target as HTMLButtonElement, key)}>{label}</button>))
    }, [])
    
    return (
        <div className="container">
            <div ref={tabsButtonsRef} className="tab-manager-container">
                {buttons}
            </div>
            <TabContainer tabContent={tab} />            
        </div>

    )
}