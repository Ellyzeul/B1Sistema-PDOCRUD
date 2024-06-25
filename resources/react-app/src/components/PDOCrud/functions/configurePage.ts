import { MutableRefObject } from "react";
import setValuesOnSelects from "./setValuesOnSelects";
import setCurrencySymbols from "./setCurrencySymblos";
import setConfigurationReapply from "./setConfigurationReapply";
import setOpenModalEvent from "./setOpenModalEvent";
import setTopScrollBar from "./setTopScrollBar";
import setDeadlineColumn from "./setDeadlineColumn";
import setURLColumn from "./setURLColumn";
import configureInvoiceField from "./configureInvoiceField";
import setSearchTrim from "./setSearchTrim";
import configureDatepickers from "./configureDatepickers";
import configureAddressVerifiedColumn from "./configureAddressVerifiedColumn";
import setConditionalStyling from "./setConditionalStyling";
import setCompaniesIcons from "./setCompaniesIcons";
import configureSellercentralColumn from "./configureSellercentralColumn";
import setSendEmailColumn from "./setSendEmailColumn";
import setTrackingCodeUpdateButton from "./setTrackingCodeUpdateButton";
import configureDeliveryMethodField from "./configureDeliveryMethodField";
import configureReadyForShipColumn from "./configureReadyForShipColumn";
import setInvoiceNumberUpdateButton from "./setInvoiceNumberUpdateButton";
import setBlacklistIcon from "./setBlacklistIcon";
import setInventoryIcon from "./setInventoryIcon";
import configureCancelInvoiceColumn from "./configureCancelInvoiceColumn";
import setSendCancellationNoticeColumn from "./setSendCancellationNoticeColumn";
import configureInternalTrackingCodeColumn from "./configureInternalTrackingCodeColumn";
import setSaveEvent from "./setSaveEvent";

const configurePage = (elemRef: MutableRefObject<null>, refModal: MutableRefObject<null>, refModalId: MutableRefObject<null>, refOnlineOrderNumber: MutableRefObject<null>, refURLInput: MutableRefObject<null>, refTrackingMethodModal: MutableRefObject<HTMLDivElement | null>) => {
	if(!elemRef.current) return
	const elem = elemRef.current as HTMLDivElement

	if(elem.children.length === 0) return
	if(!document.querySelectorAll('.pdocrud-data-row')[0].children[1]) return

	const rawPhase = window.location.search.split(/=/)[1]
	const phase = Number(rawPhase) || 0

	const h1 = document.querySelector(".panel-title") as HTMLHeadingElement
	h1.textContent = "Controle de fases"
	setValuesOnSelects()
	setCurrencySymbols()
	setOpenModalEvent(refModal, refModalId, refOnlineOrderNumber, refURLInput)
	setTopScrollBar(document.querySelector(".panel-body") as HTMLDivElement)
	setConfigurationReapply(elemRef, refModal, refModalId, refOnlineOrderNumber, refURLInput, refTrackingMethodModal)
	configureInvoiceField()
	setSaveEvent()
	setSearchTrim()
	configureDatepickers()
	configureAddressVerifiedColumn()
	configureCancelInvoiceColumn()
	configureReadyForShipColumn()
	configureSellercentralColumn()
	configureInternalTrackingCodeColumn()
	setConditionalStyling()
	setCompaniesIcons()
	configureDeliveryMethodField()
	setTrackingCodeUpdateButton(refTrackingMethodModal, refOnlineOrderNumber)
	setInvoiceNumberUpdateButton(phase)
	setBlacklistIcon(phase, rawPhase)
	if(phase === 0) setInventoryIcon()
	if(phase < 7 && (phase !== 6.2 && phase !== 6.21)) setDeadlineColumn(phase)
	if(phase >= 6.2 && phase < 6.3) setSendEmailColumn(phase)
	if(phase === 2.1) setURLColumn()
	setSendCancellationNoticeColumn(phase)
}

export default configurePage
