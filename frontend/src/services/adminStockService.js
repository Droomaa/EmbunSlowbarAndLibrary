import api from '../config/api';

export const getAdminStockReport = (params = {}) =>
  api.get('/admin/laporan-stok/data', { params }).then(res => res.data);
