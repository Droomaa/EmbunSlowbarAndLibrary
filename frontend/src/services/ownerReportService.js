import api from '../config/api';

export async function getSalesReports() {
  const { data } = await api.get('/owner/reports/data');
  return data;
}
