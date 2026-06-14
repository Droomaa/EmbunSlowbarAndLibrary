import { Outlet, NavLink, useNavigate } from 'react-router-dom';
import { Coffee, Settings, Bell, Search, User, LogOut } from 'lucide-react';
import useAuthStore from '../stores/authStore';
import NotificationDropdown from '../components/ui/NotificationDropdown';
import './CashierLayout.css';
import { showToast } from '../components/ui/Toast';

export default function CashierLayout() {
  const navigate = useNavigate();
  const { logout, role } = useAuthStore();

  const handleEndShift = () => {
    if (window.confirm('Akhiri shift dan kembali ke Dashboard?')) {
      navigate('/karyawan/dashboard');
    }
  };

  return (
    <div className="cashier-layout-container">
      {/* Topbar Horizontal */}
      <header className="cashier-topbar">
        <div className="cashier-topbar-left">
          <NavLink to="/karyawan/dashboard" className="cashier-logo">
            <div className="cashier-logo-icon">
              <Coffee size={20} />
            </div>
            Embun Cafe
          </NavLink>
          
          <nav className="cashier-nav-list">
            <NavLink to="/karyawan/kasir" className={({ isActive }) => `cashier-nav-item ${isActive ? 'active' : ''}`}>
              Cashier
            </NavLink>
            <NavLink to="/karyawan/dashboard" className="cashier-nav-item">
              Orders
            </NavLink>
            <NavLink to="/karyawan/stok-bahan" className="cashier-nav-item">
              Inventory
            </NavLink>
          </nav>
        </div>

        <div className="cashier-topbar-right">
          <button className="cashier-shift-btn" onClick={handleEndShift}>
            <LogOut size={16} /> End Shift
          </button>
          
          <div style={{ display: 'flex', alignItems: 'center', gap: '1rem', marginLeft: '1rem', borderLeft: '1px solid var(--color-border)', paddingLeft: '1.5rem' }}>
            <Search size={20} style={{ color: 'var(--color-text-muted)', cursor: 'pointer' }} />
            <NotificationDropdown />
            <Settings size={20} style={{ color: 'var(--color-text-muted)', cursor: 'pointer' }} />
            <div className="karyawan-user-profile" style={{ marginLeft: '1rem' }}>
              <div className="karyawan-user-avatar">
                <User size={20} />
              </div>
            </div>
          </div>
        </div>
      </header>

      {/* Main POS Content (3 Columns usually) */}
      <main className="cashier-main-content">
        <Outlet />
      </main>
    </div>
  );
}
