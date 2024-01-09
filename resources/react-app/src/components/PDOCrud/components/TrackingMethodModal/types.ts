import { MutableRefObject } from "react";

export type TrackingMethodModalProp = {
  refTrackingMethodModal: MutableRefObject<HTMLDivElement | null>,
  refOnlineOrderNumber: MutableRefObject<null>,
}

export type TrackingMethodParams = {
  orderNumber?: string,
  sellercentral?: string,
  company?: string,
  trackingNumber?: string,
  shipDate?: string,
}
