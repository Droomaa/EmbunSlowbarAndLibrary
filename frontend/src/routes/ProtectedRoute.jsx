import { Navigate, Outlet } from 'react-router-dom';
import useAuthStore from '../stores/authStore';

export default function ProtectedRoute({ allowedRoles = [] }) {
  const { isAuthenticated, role } = useAuthStore();

  if (!isAuthenticated) {
    return <Navigate to="/login" replace />;
  }

  if (allowedRoles.length > 0 && !allowedRoles.includes(role)) {
    // If logged in but doesn't have the right role, send to a safe place (or login)
    return <Navigate to="/login" replace />;
  }

  return <Outlet />;
}
