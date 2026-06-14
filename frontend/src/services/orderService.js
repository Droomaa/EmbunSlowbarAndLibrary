import api from '../config/api';

/**
 * POST /api/orders
 * Payload: { customer_name, table_number, items: [{ menu_id, quantity }] }
 * Success: HTTP 200 or 201 → treat as success, clear cart, show toast.
 */
export async function createOrder(payload) {
  const { data } = await api.post('/orders', payload);
  return data;
}
