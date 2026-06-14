import api from '../config/api';

export const getAdminSalesReport = (params = {}) =>
  api.get('/admin/laporan-penjualan/data', { params }).then(res => res.data);
