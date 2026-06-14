import api from '../config/api';

export async function fetchMenus() {
  const { data } = await api.get('/menus');
  // API returns: { message, data: [...menus], menus: [...], metrics }
  // Extract the menu array from the wrapper
  if (Array.isArray(data)) return data;
  if (data?.data && Array.isArray(data.data)) return data.data;
  if (data?.menus && Array.isArray(data.menus)) return data.menus;
  return [];
}
