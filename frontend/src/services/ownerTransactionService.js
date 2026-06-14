import api from '../config/api';

export async function getOwnerTransactions() {
  const { data } = await api.get('/owner/transactions/data');
  return data;
}

export async function updateTransactionStatus(id, status) {
  const { data } = await api.put(`/owner/transactions/${id}/status`, { status });
  return data;
}
