import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import ToastProvider from './components/ui/Toast';
import './styles/base.css';
import './styles/components.css';
import './styles/responsive.css';

// Layouts & Routing
import PublicLayout from './layouts/PublicLayout';
import OwnerLayout from './layouts/OwnerLayout';
import KaryawanLayout from './layouts/KaryawanLayout';
import CashierLayout from './layouts/CashierLayout';
import AdminLayout from './layouts/AdminLayout';
import ProtectedRoute from './routes/ProtectedRoute';

// Public Pages
import HomePage from './pages/public/HomePage';
import OnlineOrderPage from './pages/public/OnlineOrderPage';
import ReservationPage from './pages/public/ReservationPage';
import ErrorPage from './pages/public/ErrorPage';

// Auth & Owner Pages
import LoginPage from './pages/auth/LoginPage';
import OwnerDashboard from './pages/owner/OwnerDashboard';
import OwnerSalesPage from './pages/owner/OwnerSalesPage';
import OwnerStockPage from './pages/owner/OwnerStockPage';
import OwnerAccountsPage from './pages/owner/OwnerAccountsPage';
import OwnerMenuPage from './pages/owner/OwnerMenuPage';
import OwnerTransactionPage from './pages/owner/OwnerTransactionPage';
import OwnerSettingsPage from './pages/owner/OwnerSettingsPage';

// Karyawan Pages
import KaryawanDashboardPage from './pages/karyawan/KaryawanDashboardPage';
import KaryawanStockPage from './pages/karyawan/KaryawanStockPage';
import KaryawanReservationPage from './pages/karyawan/KaryawanReservationPage';
import KaryawanOnlineOrderPage from './pages/karyawan/KaryawanOnlineOrderPage';
import KaryawanDeliveryPage from './pages/karyawan/KaryawanDeliveryPage';
import KaryawanCashierPage from './pages/karyawan/KaryawanCashierPage';

// Admin Pages
import AdminDashboardPage from './pages/admin/AdminDashboardPage';
import AdminSalesPage from './pages/admin/AdminSalesPage';
import AdminStockPage from './pages/admin/AdminStockPage';
import AdminTransactionPage from './pages/admin/AdminTransactionPage';
import AdminCashierPage from './pages/admin/AdminCashierPage';

const queryClient = new QueryClient({
  defaultOptions: {
    queries: { staleTime: 5 * 60 * 1000, retry: 1 },
  },
});

export default function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <BrowserRouter>
        <ToastProvider />
        <Routes>
          {/* Auth */}
          <Route path="/login" element={<LoginPage />} />

          {/* Public Routes */}
          <Route path="/" element={<PublicLayout />}>
            <Route index element={<HomePage />} />
            <Route path="online-order" element={<OnlineOrderPage />} />
            <Route path="reservation" element={<ReservationPage />} />
          </Route>

          {/* Owner Protected Routes */}
          <Route element={<ProtectedRoute allowedRoles={['Owner']} />}>
            <Route path="/owner" element={<OwnerLayout />}>
              <Route path="dashboard" element={<OwnerDashboard />} />
              <Route path="sales-reports" element={<OwnerSalesPage />} />
              <Route path="stock-reports" element={<OwnerStockPage />} />
              <Route path="accounts" element={<OwnerAccountsPage />} />
              <Route path="menu" element={<OwnerMenuPage />} />
              <Route path="transactions" element={<OwnerTransactionPage />} />
              <Route path="settings" element={<OwnerSettingsPage />} />
            </Route>
          </Route>

          {/* Karyawan / Staff Protected Routes */}
          <Route element={<ProtectedRoute allowedRoles={['Staff', 'Karyawan', 'Owner']} />}>
            <Route path="/karyawan" element={<KaryawanLayout />}>
              <Route path="dashboard" element={<KaryawanDashboardPage />} />
              <Route path="stok-bahan" element={<KaryawanStockPage />} />
              <Route path="reservasi" element={<KaryawanReservationPage />} />
              <Route path="pesanan-online" element={<KaryawanOnlineOrderPage />} />
              <Route path="pengiriman-stok" element={<KaryawanDeliveryPage />} />
              <Route path="settings" element={<div>Settings Placeholder</div>} />
            </Route>
            
            <Route path="/karyawan/kasir" element={<CashierLayout />}>
              <Route index element={<KaryawanCashierPage />} />
            </Route>
          </Route>

          {/* Admin Protected Routes */}
          <Route element={<ProtectedRoute allowedRoles={['Admin']} />}>
            <Route path="/admin" element={<AdminLayout />}>
              <Route path="dashboard" element={<AdminDashboardPage />} />
              <Route path="laporan-penjualan" element={<AdminSalesPage />} />
              <Route path="laporan-stok-bahan" element={<AdminStockPage />} />
              <Route path="data-transaksi" element={<AdminTransactionPage />} />
              <Route path="kasir" element={<AdminCashierPage />} />
              <Route path="settings" element={<div style={{ padding: '2rem', color: 'var(--color-text-muted)' }}>Settings Admin — Coming Soon</div>} />
            </Route>
          </Route>

          {/* Catch All */}
          <Route path="*" element={<ErrorPage />} />
        </Routes>
      </BrowserRouter>
    </QueryClientProvider>
  );
}

