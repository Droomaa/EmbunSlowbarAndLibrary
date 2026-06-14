import api from '../config/api';

export const getAdminDashboard = () => api.get('/admin/dashboard/data').then(res => res.data);
