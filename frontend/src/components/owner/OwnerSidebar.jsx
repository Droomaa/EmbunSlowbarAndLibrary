import { NavLink, useNavigate } from 'react-router-dom';
import { 
  LayoutDashboard, 
  TrendingUp, 
  Package, 
  Users, 
  UtensilsCrossed, 
  Receipt,
  Settings,
  LogOut
} from 'lucide-react';
import useAuthStore from '../../stores/authStore';
import { showToast } from '../ui/Toast';
import './OwnerSidebar.css';

export default function OwnerSidebar({ isOpen }) {
  const logout = useAuthStore((state) => state.logout);
  const navigate = useNavigate();

  const handleLogout = (e) => {
    e.preventDefault();
    if (window.confirm('Are you sure you want to logout?')) {
      logout();
      showToast('Logout berhasil', 'success');
      navigate('/login');
    }
  };

  return (
    <aside className={`owner-sidebar ${isOpen ? 'open' : ''}`}>
      <div className="owner-sidebar-header">
        <div className="owner-sidebar-logo">Embun Cafe</div>
        <div className="owner-sidebar-subtitle">Owner Dashboard</div>
      </div>

      <nav className="owner-sidebar-nav">
        <NavLink to="/owner/dashboard" className="owner-nav-link" end>
          <LayoutDashboard size={20} />
          Dashboard
        </NavLink>
        <NavLink to="/owner/sales-reports" className="owner-nav-link">
          <TrendingUp size={20} />
          Sales Reports
        </NavLink>
        <NavLink to="/owner/stock-reports" className="owner-nav-link">
          <Package size={20} />
          Stock Reports
        </NavLink>
        <NavLink to="/owner/accounts" className="owner-nav-link">
          <Users size={20} />
          Accounts
        </NavLink>
        <NavLink to="/owner/menu" className="owner-nav-link">
          <UtensilsCrossed size={20} />
          Menu
        </NavLink>
        <NavLink to="/owner/transactions" className="owner-nav-link">
          <Receipt size={20} />
          Transactions
        </NavLink>
      </nav>

      <div className="owner-sidebar-footer">
        <NavLink to="/owner/settings" className="owner-nav-link">
          <Settings size={20} />
          Settings
        </NavLink>
        <a href="#" className="owner-nav-link logout" onClick={handleLogout}>
          <LogOut size={20} />
          Logout
        </a>
      </div>
    </aside>
  );
}
