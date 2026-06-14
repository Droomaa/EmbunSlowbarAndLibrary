import api from '../config/api';

export const getAdminTransactions = (params = {}) =>
  api.get('/admin/data-transaksi/data', { params }).then(res => res.data);

export const updateAdminTransactionStatus = (id, payload) =>
  api.put(`/admin/data-transaksi/${id}`, payload).then(res => res.data);

export const deleteAdminTransaction = (id) =>
  api.delete(`/admin/data-transaksi/${id}`).then(res => res.data);
