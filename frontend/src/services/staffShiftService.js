import api from '../config/api';

export async function getCurrentShift() {
  const { data } = await api.get('/karyawan/shift/current');
  return data;
}

export async function checkInShift(payload) {
  const { data } = await api.post('/karyawan/shift/check-in', payload);
  return data;
}

export async function checkOutShift(payload) {
  const { data } = await api.post('/karyawan/shift/check-out', payload);
  return data;
}

export async function getShiftHistory(params) {
  const { data } = await api.get('/karyawan/shift/history', { params });
  return data;
}
