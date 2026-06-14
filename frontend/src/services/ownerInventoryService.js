import api from '../config/api';

export async function getStockReports() {
  const { data } = await api.get('/owner/stock/data');
  // API returns { data: [...] } for this endpoint based on PRD/Backend
  return data;
}

export async function addInventory(payload) {
  // Uses the global /api/inventory route
  const { data } = await api.post('/inventory', payload);
  return data;
}
