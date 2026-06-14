import api from '../config/api';

export async function getCashierMenus() {
  const { data } = await api.get('/karyawan/pos/menus');
  return data;
}

export async function checkoutCashier(payload) {
  const { data } = await api.post('/karyawan/pos/checkout', payload);
  return data;
}
