import api from '../config/api';

export async function getReservations() {
  const { data } = await api.get('/karyawan/reservations/data');
  return data;
}

export async function verifyReservation(id, payload) {
  // Use POST based on route check
  const { data } = await api.post(`/karyawan/reservations/${id}/status`, payload);
  return data;
}

export async function rejectReservation(id, payload) {
  const { data } = await api.post(`/karyawan/reservations/${id}/status`, payload);
  return data;
}
