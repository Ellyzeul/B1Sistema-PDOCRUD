export interface TrackingTableProp {
  data: {
    tracking_code: string,
    online_order_number: string,
    delivery_method: string,
    status: string,
    last_update_date: string,
    details: string,
    expected_date: string,
    delivery_expected_date: string,
    observation: string,
  }[]
}
