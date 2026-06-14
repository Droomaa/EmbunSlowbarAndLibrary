import api from '../config/api';

export async function getOwnerMenus() {
  const { data } = await api.get('/owner/menus/data');
  return data;
}

export async function addOwnerMenu(formData) {
  // formData is a FormData object (multipart/form-data)
  const { data } = await api.post('/owner/menus/add', formData, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  });
  return data;
}

export async function updateOwnerMenu(id, formData) {
  // Use POST with _method=PUT for Laravel multipart form handling
  formData.append('_method', 'PUT');
  const { data } = await api.post(`/owner/menus/${id}`, formData, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  });
  return data;
}

export async function deleteOwnerMenu(id) {
  const { data } = await api.delete(`/owner/menus/${id}`);
  return data;
}
