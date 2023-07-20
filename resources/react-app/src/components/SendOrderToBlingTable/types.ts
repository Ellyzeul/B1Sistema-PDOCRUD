export type SendOrderToBlingTableProp = {
    data: {
      orderId: number;
      company: number;
      sellerChannel: string;
      origin: string;
      orderDate: string;
    }[]
}
