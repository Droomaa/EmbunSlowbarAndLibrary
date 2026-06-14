import { useQuery } from '@tanstack/react-query';
import { Link } from 'react-router-dom';
import { getKaryawanDashboard } from '../../services/karyawanDashboardService';
import { formatCurrency } from '../../utils/formatCurrency';
import { formatDate } from '../../utils/formatDate';
import MetricCard from '../../components/owner/MetricCard';
import StatusBadge from '../../components/owner/StatusBadge';
import { Package, Clock, CalendarCheck, ShoppingBag, Truck, CheckCircle2 } from 'lucide-react';
import ShiftAttendanceButton from '../../components/ui/ShiftAttendanceButton';
import './KaryawanDashboardPage.css';

export default function KaryawanDashboardPage() {
  const { data: dashboard, isLoading, error } = useQuery({
    queryKey: ['karyawanDashboard'],
    queryFn: getKaryawanDashboard
  });

  if (isLoading) return <div style={{ padding: '2rem' }}>Memuat data dashboard...</div>;
  if (error) return <div style={{ padding: '2rem', color: 'red' }}>Gagal memuat dashboard.</div>;

  const { user, metrics, online_orders, reservations } = dashboard;
  
  // Dummy low stock array to showcase layout since API only returns count for now
  const dummyLowStocks = [
    { id: 1, name: 'Biji Kopi Arabica', qty: 2, min: 5, unit: 'kg' },
    { id: 2, name: 'Susu UHT', qty: 3, min: 10, unit: 'liter' },
    { id: 3, name: 'Gula Aren', qty: 1, min: 5, unit: 'kg' },
    { id: 4, name: 'Sirup Karamel', qty: 1, min: 3, unit: 'botol' },
  ];

  return (
    <div className="karyawan-dashboard-page">
      {/* Greeting Banner */}
      <div className="karyawan-greeting-card">
        <div className="karyawan-greeting-text">
          <h2>Selamat Bekerja, {user?.name || 'Tim Karyawan'}!</h2>
          <p>Semoga shift hari ini berjalan lancar. Pantau selalu pesanan online dan stok bahan di bar. Jangan lupa melayani pelanggan dengan senyuman.</p>
        </div>
        <div className="karyawan-greeting-actions">
          <Link to="/karyawan/kasir">
            <button className="btn-karyawan-light">Buka Kasir POS</button>
          </Link>
          <ShiftAttendanceButton className="btn-karyawan-outline-light" />
        </div>
      </div>

      {/* KPI Section */}
      <div className="karyawan-kpi-grid">
        <MetricCard
          title="Pesanan Online Pending"
          value={metrics?.pending_orders || 0}
          icon={ShoppingBag}
          trend="Perlu Disiapkan"
          trendUp={false}
          color="var(--color-warning)"
        />
        <MetricCard
          title="Reservasi Hari Ini"
          value={metrics?.today_reservations || 0}
          icon={CalendarCheck}
          trend="Cek Meja Tersedia"
          trendUp={true}
          color="var(--color-primary)"
        />
        <MetricCard
          title="Stok Hampir Habis"
          value={metrics?.low_stock || 0}
          icon={Package}
          trend="Segera Restock"
          trendUp={false}
          color="var(--color-danger)"
        />
      </div>

      <div className="karyawan-dashboard-panels">
        {/* Pesanan Online Terbaru */}
        <div className="karyawan-panel">
          <div className="karyawan-panel-header">
            <h3><ShoppingBag size={20} /> Pesanan Online Masuk</h3>
            <Link to="/karyawan/pesanan-online" className="karyawan-link">Lihat Semua</Link>
          </div>
          
          <div className="karyawan-panel-list">
            {online_orders?.length > 0 ? (
              online_orders.map((order, idx) => (
                <div className="karyawan-list-item" key={order.order_id || idx}>
                  <div className="karyawan-item-info">
                    <div style={{ padding: '0.5rem', backgroundColor: '#fdf6e3', borderRadius: '8px', color: 'var(--color-warning)' }}>
                      <Truck size={20} />
                    </div>
                    <div className="karyawan-item-text">
                      <h4>{order.customer_name}</h4>
                      <p>{order.items_text || 'Loading items...'}</p>
                    </div>
                  </div>
                  <div className="karyawan-item-price">
                    <span>{formatCurrency(order.total_price)}</span>
                    <span style={{ fontSize: '0.75rem', color: 'var(--color-text-muted)' }}>{order.order_type || 'Takeaway'}</span>
                  </div>
                </div>
              ))
            ) : (
              <div style={{ textAlign: 'center', padding: '2rem 0', color: 'var(--color-text-muted)' }}>
                <CheckCircle2 size={40} style={{ margin: '0 auto 1rem', opacity: 0.5, color: 'var(--color-primary)' }} />
                <p>Tidak ada pesanan online pending.</p>
              </div>
            )}
          </div>
        </div>

        {/* Reservasi Hari Ini */}
        <div className="karyawan-panel">
          <div className="karyawan-panel-header">
            <h3><CalendarCheck size={20} /> Reservasi Mendatang</h3>
            <Link to="/karyawan/reservasi" className="karyawan-link">Kelola Reservasi</Link>
          </div>
          
          <div className="karyawan-panel-list">
            {reservations?.length > 0 ? (
              reservations.map((res, idx) => (
                <div className="karyawan-list-item" key={res.reservation_id || idx}>
                  <div className="karyawan-item-info">
                    <div className="karyawan-item-text">
                      <h4>{res.customer_name}</h4>
                      <p>{res.pax} Orang</p>
                    </div>
                  </div>
                  <div className="karyawan-item-price">
                    <span>{formatDate(res.reservation_date).split(' ')[1]}</span>
                    <StatusBadge status={res.status} />
                  </div>
                </div>
              ))
            ) : (
              <div style={{ textAlign: 'center', padding: '2rem 0', color: 'var(--color-text-muted)' }}>
                <p>Belum ada reservasi hari ini.</p>
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Peringatan Stok Habis (Hanya muncul jika ada stok <= batas) */}
      {(metrics?.low_stock > 0 || dummyLowStocks.length > 0) && (
        <div className="karyawan-panel karyawan-low-stock-panel">
          <div className="karyawan-panel-header" style={{ marginBottom: '1rem' }}>
            <h3 style={{ color: 'var(--color-danger)' }}><Package size={20} /> Peringatan Stok Menipis</h3>
            <Link to="/karyawan/stok-bahan" className="karyawan-link">Cek Gudang</Link>
          </div>
          
          <div className="karyawan-low-stock-grid">
            {dummyLowStocks.map(stock => (
              <div className="low-stock-card" key={stock.id}>
                <div className="low-stock-category">Bahan Baku</div>
                <div className="low-stock-name">{stock.name}</div>
                <div className="low-stock-qty">
                  <div className="low-stock-qty-val">{stock.qty} <span style={{ fontSize: '0.8rem', fontWeight: 500 }}>{stock.unit}</span></div>
                  <div className="low-stock-qty-min">Min: {stock.min}</div>
                </div>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}
