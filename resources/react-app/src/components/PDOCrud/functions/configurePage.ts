import { MutableRefObject } from "react";
import setValuesOnSelects from "./setValuesOnSelects";
import setCurrencySymbols from "./setCurrencySymblos";
import setConfigurationReapply from "./setConfigurationReapply";
import setOpenModalEvent from "./setOpenModalEvent";
import setTopScrollBar from "./setTopScrollBar";
import setDeadlineColumn from "./setDeadlineColumn";
import setURLColumn from "./setURLColumn";
import setPhotoLink from "./setPhotoLink";
import setSearchTrim from "./setSearchTrim";
import configureDatepickers from "./configureDatepickers";
import configureAddressVerifiedColumn from "./configureAddressVerifiedColumn";
import setConditionalStyling from "./setConditionalStyling";

const configurePage = (elemRef: MutableRefObject<null>, refModal: MutableRefObject<null>, refModalId: MutableRefObject<null>, refOnlineOrderNumber: MutableRefObject<null>, refURLInput: MutableRefObject<null>) => {
	if(!elemRef.current) return
	const elem = elemRef.current as HTMLDivElement

	if(elem.children.length === 0) return
	if(!document.querySelectorAll('.pdocrud-data-row')[0].children[1]) return

	const phase = Number(window.location.search.split(/=/)[1]) || 0

	const h1 = document.querySelector(".panel-title") as HTMLHeadingElement
	h1.textContent = "Controle de fases"
	setValuesOnSelects()
	setCurrencySymbols()
	setOpenModalEvent(refModal, refModalId, refOnlineOrderNumber, refURLInput)
	setTopScrollBar(document.querySelector(".panel-body") as HTMLDivElement)
	setConfigurationReapply(elemRef, refModal, refModalId, refOnlineOrderNumber, refURLInput)
	setPhotoLink()
	setSearchTrim()
	configureDatepickers()
	configureAddressVerifiedColumn()
	setConditionalStyling()
	if(phase < 7) setDeadlineColumn()
	if(phase === 2.1) setURLColumn()
}

export default configurePage
