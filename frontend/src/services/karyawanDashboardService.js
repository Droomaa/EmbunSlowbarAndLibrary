import api from '../config/api';

export async function getKaryawanDashboard() {
  const { data } = await api.get('/karyawan/dashboard/data');
  return data;
}
