import { Outlet, NavLink, useNavigate, useLocation } from 'react-router-dom';
import { LayoutDashboard, TrendingUp, Package, Receipt, ShoppingCart, Settings, LogOut, Search } from 'lucide-react';
import useAuthStore from '../stores/authStore';
import NotificationDropdown from '../components/ui/NotificationDropdown';
import './AdminLayout.css';

const NAV_ITEMS = [
  { to: '/admin/dashboard', icon: LayoutDashboard, label: 'Dashboard' },
  { to: '/admin/laporan-penjualan', icon: TrendingUp, label: 'Laporan Penjualan' },
  { to: '/admin/laporan-stok-bahan', icon: Package, label: 'Laporan Stok Bahan' },
  { to: '/admin/data-transaksi', icon: Receipt, label: 'Data Transaksi' },
  { to: '/admin/kasir', icon: ShoppingCart, label: 'Kasir' },
];

const PAGE_TITLES = {
  '/admin/dashboard': 'Dashboard Overview',
  '/admin/laporan-penjualan': 'Laporan Penjualan',
  '/admin/laporan-stok-bahan': 'Laporan Stok Bahan',
  '/admin/data-transaksi': 'Data Transaksi',
  '/admin/kasir': 'Kasir',
  '/admin/settings': 'Settings',
};

const SEARCH_PLACEHOLDERS = {
  '/admin/dashboard': 'Cari transaksi...',
  '/admin/laporan-penjualan': 'Cari transaksi...',
  '/admin/laporan-stok-bahan': 'Cari bahan...',
  '/admin/data-transaksi': 'Cari transaksi...',
  '/admin/kasir': 'Cari menu...',
};

export default function AdminLayout() {
  const navigate = useNavigate();
  const location = useLocation();
  const { logout } = useAuthStore();

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  const currentPath = location.pathname;
  const pageTitle = PAGE_TITLES[currentPath] || 'Admin';
  const searchPlaceholder = SEARCH_PLACEHOLDERS[currentPath] || 'Cari...';

  return (
    <div className="admin-layout">
      {/* Sidebar */}
      <aside className="admin-sidebar">
        <div className="admin-sidebar-brand">Embun Cafe</div>

        <div className="admin-sidebar-profile">
          <div className="admin-profile-avatar">AD</div>
          <div className="admin-profile-info">
            <h4>Admin</h4>
            <span>Embun Cafe Management</span>
          </div>
        </div>

        <nav className="admin-nav">
          {NAV_ITEMS.map(item => (
            <NavLink
              key={item.to}
              to={item.to}
              className={({ isActive }) => `admin-nav-item ${isActive ? 'active' : ''}`}
            >
              <item.icon size={20} />
              {item.label}
            </NavLink>
          ))}
        </nav>

        <div className="admin-sidebar-footer">
          <NavLink
            to="/admin/settings"
            className={({ isActive }) => `admin-nav-item ${isActive ? 'active' : ''}`}
          >
            <Settings size={20} /> Settings
          </NavLink>
          <button className="admin-nav-item logout" onClick={handleLogout}>
            <LogOut size={20} /> Logout
          </button>
        </div>
      </aside>

      {/* Main */}
      <div className="admin-main">
        <header className="admin-topbar">
          <h1 className="admin-topbar-title">{pageTitle}</h1>
          <div className="admin-topbar-actions">
            <div className="admin-search-box">
              <Search size={16} style={{ color: 'var(--color-text-muted)' }} />
              <input type="text" placeholder={searchPlaceholder} disabled title="Search belum terhubung" />
            </div>
            <NotificationDropdown />
            <div className="admin-topbar-avatar">AD</div>
          </div>
        </header>

        <div className="admin-content-area">
          <Outlet />
        </div>
      </div>
    </div>
  );
}
