import api from '../config/api';

export async function getOnlineOrders() {
  const { data } = await api.get('/karyawan/orders/online');
  return data;
}

export async function updateOnlineOrderStatus(id, status) {
  // Use POST based on route check
  const { data } = await api.post(`/karyawan/orders/${id}/status`, { status });
  return data;
}
