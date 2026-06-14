import api from '../config/api';

export async function getStockMaterials() {
  const { data } = await api.get('/inventory');
  return data;
}

export async function createStock(payload) {
  const { data } = await api.post('/inventory', payload);
  return data;
}
