import api from '../config/api';

export async function loginOwner(credentials) {
  const { data } = await api.post('/login', credentials);
  return data;
}
