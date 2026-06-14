import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { Link } from 'react-router-dom';
import { Banknote, ShoppingBag, AlertTriangle, Star, Eye, MoreVertical, Coffee, Droplets, Leaf } from 'lucide-react';
import { getAdminDashboard } from '../../services/adminDashboardService';
import MetricCard from '../../components/owner/MetricCard';
import StatusBadge from '../../components/owner/StatusBadge';
import { formatCurrency } from '../../utils/formatCurrency';
import { formatDate } from '../../utils/formatDate';

export default function AdminDashboardPage() {
  const [chartMode, setChartMode] = useState('mingguan');

  const { data, isLoading, error } = useQuery({
    queryKey: ['adminDashboard'],
    queryFn: getAdminDashboard,
  });

  if (isLoading) return <div style={{ padding: '2rem' }}>Memuat data dashboard...</div>;
  if (error) return <div style={{ padding: '2rem', color: 'var(--color-danger)' }}>Gagal memuat dashboard admin.</div>;

  const chartData = chartMode === 'mingguan' ? (data?.grafik_mingguan || []) : (data?.grafik_bulanan || []);
  const maxChart = Math.max(...chartData.map(d => d.total), 1);
  const stokKritisList = data?.stok_kritis_list || [];
  const transaksiTerbaru = data?.transaksi_terbaru || [];

  const stockIcons = [Coffee, Droplets, Leaf];

  return (
    <div>
      {/* KPI Row */}
      <div className="admin-kpi-grid" style={{ gridTemplateColumns: 'repeat(3, 1fr) auto' }}>
        <MetricCard title="TOTAL PENJUALAN" value={formatCurrency(data?.total_penjualan || 0)} icon={Banknote} subtitle="Dibandingkan bulan lalu" />
        <MetricCard title="TOTAL TRANSAKSI" value={data?.total_transaksi || 0} icon={ShoppingBag} subtitle="Minggu ini" />
        <MetricCard title="STOK KRITIS" value={`${data?.stok_kritis_count || 0} Bahan`} icon={AlertTriangle} tone={data?.stok_kritis_count > 0 ? 'danger' : 'default'} subtitle="Perlu segera dipesan" />

        {/* Top Performer card */}
        <div className="admin-top-performer">
          <div className="tp-label"><Star size={16} /> Top Performer</div>
          <div className="tp-title">Produk Terlaris</div>
          <div className="tp-name">
            {transaksiTerbaru.length > 0 ? (transaksiTerbaru[0]?.customer_name || 'N/A') : 'Belum ada data'}
          </div>
          <div className="tp-badge">Data dari transaksi terbaru</div>
        </div>
      </div>

      {/* Chart + Stock Kritis */}
      <div className="admin-two-col">
        {/* Chart */}
        <div className="admin-card">
          <div className="admin-card-header">
            <h3>Statistik Penjualan 7 Hari</h3>
            <div className="admin-pills">
              <button className={`admin-pill ${chartMode === 'mingguan' ? 'active' : ''}`} onClick={() => setChartMode('mingguan')}>Mingguan</button>
              <button className={`admin-pill ${chartMode === 'bulanan' ? 'active' : ''}`} onClick={() => setChartMode('bulanan')}>Bulanan</button>
            </div>
          </div>
          {chartData.length > 0 ? (
            <div style={{ display: 'flex', alignItems: 'flex-end', gap: '0.75rem', height: '220px', padding: '1rem 0' }}>
              {chartData.map((d, i) => {
                const h = maxChart > 0 ? (d.total / maxChart) * 180 : 0;
                const isHighest = d.total === maxChart && d.total > 0;
                return (
                  <div key={i} style={{ flex: 1, display: 'flex', flexDirection: 'column', alignItems: 'center', gap: '0.5rem' }}>
                    {isHighest && <span style={{ fontSize: '0.7rem', fontWeight: 700, color: 'var(--color-primary)' }}>{formatCurrency(d.total)}</span>}
                    <div style={{
                      width: '100%', maxWidth: '48px',
                      height: `${Math.max(h, 4)}px`,
                      backgroundColor: isHighest ? 'var(--color-primary)' : '#c8ddd0',
                      borderRadius: '6px 6px 0 0',
                      transition: 'height 0.3s'
                    }} />
                    <span style={{ fontSize: '0.7rem', color: 'var(--color-text-muted)', fontWeight: 600 }}>{d.label}</span>
                  </div>
                );
              })}
            </div>
          ) : (
            <p style={{ textAlign: 'center', color: 'var(--color-text-muted)', padding: '3rem 0' }}>Data statistik belum tersedia dari backend.</p>
          )}
        </div>

        {/* Stok Kritis */}
        <div className="admin-card">
          <div className="admin-card-header">
            <h3>Laporan Stok Kritis</h3>
          </div>
          {stokKritisList.length > 0 ? (
            <div>
              {stokKritisList.slice(0, 4).map((s, i) => {
                const qty = s.quantity ?? s.stock_quantity ?? 0;
                const pct = Math.min((qty / 500) * 100, 100);
                const barColor = qty <= 0 ? 'var(--color-danger)' : qty <= 100 ? '#e6a817' : 'var(--color-primary)';
                const IconComp = stockIcons[i % stockIcons.length];
                return (
                  <div className="admin-stock-item" key={s.id || i}>
                    <div className="admin-stock-info">
                      <div className="admin-stock-icon"><IconComp size={18} /></div>
                      <span style={{ fontWeight: 600 }}>{s.item_name || s.name || 'Bahan'}</span>
                    </div>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '0.75rem' }}>
                      <div className="admin-stock-bar">
                        <div className="admin-stock-bar-fill" style={{ width: `${pct}%`, backgroundColor: barColor }} />
                      </div>
                      <span className="admin-stock-qty" style={{ color: barColor }}>{qty} {s.unit || ''}</span>
                    </div>
                  </div>
                );
              })}
              <Link to="/admin/laporan-stok-bahan" className="admin-btn admin-btn-outline" style={{ width: '100%', justifyContent: 'center', marginTop: '1rem' }}>
                Lihat Semua Stok
              </Link>
            </div>
          ) : (
            <p style={{ textAlign: 'center', color: 'var(--color-text-muted)', padding: '2rem 0' }}>Belum ada stok kritis.</p>
          )}
        </div>
      </div>

      {/* Transaksi Terbaru */}
      <div className="admin-card">
        <div className="admin-card-header">
          <h3>Data Transaksi Terbaru</h3>
          <div style={{ display: 'flex', gap: '0.5rem' }}>
            <Link to="/admin/data-transaksi" className="admin-btn admin-btn-outline" style={{ fontSize: '0.8rem' }}>Lihat Seluruh Riwayat Transaksi</Link>
          </div>
        </div>
        {transaksiTerbaru.length > 0 ? (
          <div className="admin-table-wrap">
            <table className="admin-table">
              <thead>
                <tr>
                  <th>ID Transaksi</th>
                  <th>Pelanggan</th>
                  <th>Waktu</th>
                  <th>Status</th>
                  <th>Total</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                {transaksiTerbaru.slice(0, 5).map((t, i) => (
                  <tr key={t.order_id || i}>
                    <td style={{ color: 'var(--color-primary)', fontWeight: 600 }}>#{t.order_id}</td>
                    <td>
                      <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                        <div style={{ width: '28px', height: '28px', borderRadius: '50%', backgroundColor: 'var(--color-bg-main)', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '0.7rem', fontWeight: 700, color: 'var(--color-primary)' }}>
                          {(t.customer_name || 'W')[0]}
                        </div>
                        {t.customer_name}
                      </div>
                    </td>
                    <td style={{ fontSize: '0.85rem', color: 'var(--color-text-muted)' }}>{formatDate(t.created_at)}</td>
                    <td><StatusBadge status={t.status} /></td>
                    <td style={{ fontWeight: 700 }}>{formatCurrency(t.total_price)}</td>
                    <td>
                      <button style={{ background: 'none', border: 'none', cursor: 'pointer', color: 'var(--color-text-muted)' }}><MoreVertical size={16} /></button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        ) : (
          <p style={{ textAlign: 'center', color: 'var(--color-text-muted)', padding: '2rem 0' }}>Belum ada transaksi terbaru.</p>
        )}
      </div>
    </div>
  );
}
