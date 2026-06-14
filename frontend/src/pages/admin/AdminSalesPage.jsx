import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { Banknote, ShoppingBag, Users, XCircle, Eye, Filter, FileDown, Printer, TrendingUp } from 'lucide-react';
import { getAdminSalesReport } from '../../services/adminSalesService';
import MetricCard from '../../components/owner/MetricCard';
import StatusBadge from '../../components/owner/StatusBadge';
import { formatCurrency } from '../../utils/formatCurrency';
import { formatDate } from '../../utils/formatDate';
import { showToast } from '../../components/ui/Toast';

export default function AdminSalesPage() {
  const [startDate, setStartDate] = useState('');
  const [endDate, setEndDate] = useState('');
  const [appliedFilter, setAppliedFilter] = useState({});

  const { data, isLoading, error } = useQuery({
    queryKey: ['adminSales', appliedFilter],
    queryFn: () => getAdminSalesReport(appliedFilter),
  });

  const handleApplyFilter = () => {
    const params = {};
    if (startDate) params.start_date = startDate;
    if (endDate) params.end_date = endDate;
    setAppliedFilter(params);
  };

  const handleExport = (type) => {
    showToast(`Fitur ${type} belum tersedia.`, 'error');
  };

  const metrics = data?.metrics || {};
  const tableData = data?.table_data || [];
  const bestSeller = data?.best_seller;

  if (isLoading) return <div style={{ padding: '2rem' }}>Memuat laporan penjualan...</div>;
  if (error) return <div style={{ padding: '2rem', color: 'var(--color-danger)' }}>Gagal memuat laporan.</div>;

  return (
    <div>
      {/* KPI */}
      <div className="admin-kpi-grid">
        <MetricCard title="TOTAL PENJUALAN" value={formatCurrency(metrics.total_penjualan || 0)} icon={Banknote} trend={metrics.total_penjualan > 0 ? '+12%' : undefined} />
        <MetricCard title="TOTAL TRANSAKSI" value={`${metrics.total_transaksi || 0} Pesanan`} icon={ShoppingBag} />
        <MetricCard title="TOTAL PELANGGAN" value={`${metrics.total_pelanggan || 0} Orang`} icon={Users} />
        <MetricCard title="DIBATALKAN" value={`${metrics.dibatalkan || 0} Pesanan`} icon={XCircle} tone={metrics.dibatalkan > 0 ? 'danger' : 'default'} />
      </div>

      {/* Filter Bar */}
      <div className="admin-filter-bar">
        <input type="date" className="admin-filter-input" value={startDate} onChange={e => setStartDate(e.target.value)} />
        <span style={{ color: 'var(--color-text-muted)', fontSize: '0.85rem' }}>sampai</span>
        <input type="date" className="admin-filter-input" value={endDate} onChange={e => setEndDate(e.target.value)} />
        <button className="admin-btn admin-btn-primary" onClick={handleApplyFilter}>
          <Filter size={16} /> Terapkan Filter
        </button>
        <div style={{ marginLeft: 'auto', display: 'flex', gap: '0.5rem' }}>
          <button className="admin-btn admin-btn-outline" onClick={() => handleExport('Export CSV')}>
            <FileDown size={16} /> Export CSV
          </button>
          <button className="admin-btn admin-btn-outline" onClick={() => handleExport('Cetak PDF')}>
            <Printer size={16} /> Cetak PDF
          </button>
        </div>
      </div>

      {/* Table */}
      <div className="admin-card" style={{ marginBottom: '1.5rem' }}>
        {tableData.length > 0 ? (
          <div className="admin-table-wrap">
            <table className="admin-table">
              <thead>
                <tr>
                  <th>No Transaksi</th>
                  <th>Waktu</th>
                  <th>Pelanggan</th>
                  <th>Item Terjual</th>
                  <th>Total Harga</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                {tableData.map((row, i) => (
                  <tr key={row.order_id || i}>
                    <td style={{ color: 'var(--color-primary)', fontWeight: 600 }}>#{row.order_id}</td>
                    <td style={{ fontSize: '0.85rem', color: 'var(--color-text-muted)' }}>{formatDate(row.created_at)}</td>
                    <td>{row.customer_name}</td>
                    <td style={{ maxWidth: '200px', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>{row.items_text || '-'}</td>
                    <td style={{ fontWeight: 700 }}>{formatCurrency(row.total_price)}</td>
                    <td><StatusBadge status={row.status} /></td>
                    <td>
                      <button style={{ background: 'none', border: 'none', cursor: 'pointer', color: 'var(--color-primary)' }}><Eye size={18} /></button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        ) : (
          <p style={{ textAlign: 'center', color: 'var(--color-text-muted)', padding: '3rem 0' }}>Belum ada data penjualan.</p>
        )}
      </div>

      {/* Bottom: Best Seller + Insight */}
      <div className="admin-bottom-row">
        {/* Item Paling Laris */}
        <div className="admin-card">
          <h3 style={{ margin: '0 0 1rem', fontFamily: 'var(--font-heading)' }}>Item Paling Laris</h3>
          {bestSeller ? (
            <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
              <div style={{ width: '48px', height: '48px', borderRadius: 'var(--radius-md)', backgroundColor: 'var(--color-bg-main)', display: 'flex', alignItems: 'center', justifyContent: 'center', color: 'var(--color-primary)' }}>
                <TrendingUp size={24} />
              </div>
              <div style={{ flex: 1 }}>
                <div style={{ fontWeight: 700, fontSize: '1.05rem' }}>{bestSeller.menuName}</div>
                <div style={{ fontSize: '0.8rem', color: 'var(--color-text-muted)' }}>{bestSeller.category || 'Menu'}</div>
              </div>
              <div style={{ fontWeight: 700, color: 'var(--color-primary)' }}>{bestSeller.total_sold} Terjual</div>
            </div>
          ) : (
            <p style={{ color: 'var(--color-text-muted)' }}>Data item terlaris belum tersedia.</p>
          )}
        </div>

        {/* Insight */}
        <div className="admin-insight-card">
          <h3>Insight Penjualan</h3>
          {tableData.length >= 5 ? (
            <p>Berdasarkan data {tableData.length} transaksi, terdapat aktivitas penjualan yang bisa dianalisis lebih lanjut. Gunakan filter tanggal untuk melihat tren spesifik.</p>
          ) : (
            <p>Insight belum tersedia karena data transaksi belum cukup.</p>
          )}
          <button className="admin-btn" style={{ backgroundColor: 'rgba(255,255,255,0.15)', color: 'white' }} onClick={() => showToast('Fitur analisa lengkap belum tersedia.', 'error')}>
            Lihat Analisa Lengkap
          </button>
        </div>
      </div>
    </div>
  );
}
