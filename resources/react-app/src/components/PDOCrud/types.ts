import { MutableRefObject } from "react";

export interface PDOCrudProp {
	refModal: MutableRefObject<null>,
	refModalId: MutableRefObject<null>,
	refOnlineOrderNumber: MutableRefObject<null>,
	refURLInput: MutableRefObject<null>,
	refTrackingMethodModal: MutableRefObject<HTMLDivElement | null>,
}
