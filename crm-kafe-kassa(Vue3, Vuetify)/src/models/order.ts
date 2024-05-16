export interface TOrder {
  id: number;
  table_id: number;
  number: number;
  status_name: string;
  created_at: string;
  amount: number;
  discount: number;
  customer: {
    id: 5;
    name: string | null;
    deposit: number;
  };
  products: [
    {
      id: number;
      name: string;
      pivot: {
        order_id: number;
        quantity: number;
        price: string | number;
        total: number | string;
      };
    }
  ];
}

export interface TCustomer {
  id: number;
  name: string;
  phone: string;
  deposit: number;
}

export interface API_TOrder_One {
  id: number;
  customer: TCustomer;
  persons_count: number;
  duration: number;
  booking_at: string;
  bar_id: number;
  table_id: number;
  comment: string;
}
