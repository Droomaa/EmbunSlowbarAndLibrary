import { Outlet, NavLink, useNavigate, useLocation } from 'react-router-dom';
import { Coffee, LayoutDashboard, Package, CalendarCheck, ShoppingBag, Truck, Settings, LogOut, Bell, Search, User } from 'lucide-react';
import useAuthStore from '../stores/authStore';
import NotificationDropdown from '../components/ui/NotificationDropdown';
import './KaryawanLayout.css';

export default function KaryawanLayout() {
  const navigate = useNavigate();
  const location = useLocation();
  const { logout, userId, role } = useAuthStore();

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  // For avatar initial
  const getInitial = () => {
    return 'KS'; // Karyawan Staff
  };

  return (
    <div className="karyawan-layout-container">
      {/* Sidebar */}
      <aside className="karyawan-sidebar">
        <div className="karyawan-sidebar-logo">
          <div className="karyawan-sidebar-logo-icon">
            <Coffee size={20} />
          </div>
          <div className="karyawan-sidebar-logo-text">
            <h2>Embun Cafe</h2>
            <span>OPERATIONAL DASHBOARD</span>
          </div>
        </div>

        <nav className="karyawan-nav-list">
          <NavLink to="/karyawan/dashboard" className={({ isActive }) => `karyawan-nav-item ${isActive ? 'active' : ''}`}>
            <LayoutDashboard size={20} /> Dashboard
          </NavLink>
          <NavLink to="/karyawan/stok-bahan" className={({ isActive }) => `karyawan-nav-item ${isActive ? 'active' : ''}`}>
            <Package size={20} /> Data Stok Bahan
          </NavLink>
          <NavLink to="/karyawan/reservasi" className={({ isActive }) => `karyawan-nav-item ${isActive ? 'active' : ''}`}>
            <CalendarCheck size={20} /> Verifikasi Reservasi
          </NavLink>
          <NavLink to="/karyawan/pesanan-online" className={({ isActive }) => `karyawan-nav-item ${isActive ? 'active' : ''}`}>
            <ShoppingBag size={20} /> Pesanan Online Masuk
          </NavLink>
          <NavLink to="/karyawan/pengiriman-stok" className={({ isActive }) => `karyawan-nav-item ${isActive ? 'active' : ''}`}>
            <Truck size={20} /> Pengiriman Stok Bahan
          </NavLink>
        </nav>

        <div className="karyawan-sidebar-footer">
          <NavLink to="/karyawan/settings" className={({ isActive }) => `karyawan-nav-item ${isActive ? 'active' : ''}`} style={{ marginBottom: '1rem' }}>
            <Settings size={20} /> Settings
          </NavLink>
          <button className="karyawan-nav-item" style={{ width: '100%', background: 'none', border: 'none', cursor: 'pointer', textAlign: 'left', color: 'var(--color-danger)' }} onClick={handleLogout}>
            <LogOut size={20} /> Logout
          </button>
          
          <div style={{ display: 'flex', alignItems: 'center', gap: '0.75rem', marginTop: '1.5rem', padding: '0 1.5rem' }}>
            <div className="karyawan-user-avatar" style={{ width: '32px', height: '32px', fontSize: '0.8rem' }}>{getInitial()}</div>
            <div className="karyawan-user-info" style={{ alignItems: 'flex-start' }}>
              <span className="karyawan-user-name" style={{ fontSize: '0.8rem' }}>Staff Operasional</span>
              <span className="karyawan-user-role" style={{ fontSize: '0.65rem' }}>Morning Shift</span>
            </div>
          </div>
        </div>
      </aside>

      {/* Main Content */}
      <main className="karyawan-main-content">
        <header className="karyawan-topbar">
          <h1 className="karyawan-topbar-title">Embun Cafe Operations</h1>
          <div className="karyawan-topbar-actions">
            <div className="karyawan-search-box">
              <Search size={16} />
              <input type="text" placeholder="Search..." />
            </div>
            <NotificationDropdown />
            <div className="karyawan-user-profile">
              <div className="karyawan-user-info">
                <span className="karyawan-user-name">Staff</span>
                <span className="karyawan-user-role">{role}</span>
              </div>
              <div className="karyawan-user-avatar">
                <User size={20} />
              </div>
            </div>
          </div>
        </header>
        
        <div className="karyawan-content-area">
          <Outlet />
        </div>
      </main>
    </div>
  );
}
