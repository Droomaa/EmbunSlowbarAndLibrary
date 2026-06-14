import api from '../config/api';

export async function getOwnerAccounts() {
  const { data } = await api.get('/owner/accounts/data');
  return data;
}

// Based on routes: Route::post('/owner/accounts/add', [OwnerDashboardController::class, 'addAccount']);
export async function addOwnerAccount(payload) {
  const { data } = await api.post('/owner/accounts/add', payload);
  return data;
}
