import api from '../config/api';

/**
 * POST /api/reservations
 * Payload: { customer_name, phone_number, reservation_date, pax, notes }
 * reservation_date must be "YYYY-MM-DD HH:mm:ss"
 */
export async function createReservation(payload) {
  const { data } = await api.post('/reservations', payload);
  return data;
}
