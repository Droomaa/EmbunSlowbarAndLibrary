import api from '../config/api';

export async function getOwnerDashboard() {
  const { data } = await api.get('/owner/dashboard/data');
  return data;
}
