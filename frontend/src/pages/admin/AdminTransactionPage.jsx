import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Banknote, ShoppingBag, TrendingUp, ShoppingCart, Plus, FileDown, Filter, Eye, Pencil, Trash2, Calendar } from 'lucide-react';
import { getAdminTransactions, updateAdminTransactionStatus, deleteAdminTransaction } from '../../services/adminTransactionService';
import MetricCard from '../../components/owner/MetricCard';
import StatusBadge from '../../components/owner/StatusBadge';
import { formatCurrency } from '../../utils/formatCurrency';
import { formatDate } from '../../utils/formatDate';
import { showToast } from '../../components/ui/Toast';

export default function AdminTransactionPage() {
  const queryClient = useQueryClient();
  const [dateFilter, setDateFilter] = useState('');
  const [statusFilter, setStatusFilter] = useState('all');
  const [search, setSearch] = useState('');
  const [limit, setLimit] = useState(10);

  // Confirmation modal state
  const [confirmModal, setConfirmModal] = useState(null); // { type: 'status'|'delete', id, newStatus? }

  const params = { limit };
  if (dateFilter) params.date = dateFilter;
  if (statusFilter !== 'all') params.status = statusFilter;
  if (search) params.search = search;

  const { data, isLoading, error } = useQuery({
    queryKey: ['adminTransactions', params],
    queryFn: () => getAdminTransactions(params),
  });

  const updateMutation = useMutation({
    mutationFn: ({ id, status }) => updateAdminTransactionStatus(id, { status }),
    onSuccess: () => {
      showToast('Status transaksi berhasil diubah.', 'success');
      queryClient.invalidateQueries(['adminTransactions']);
      queryClient.invalidateQueries(['adminDashboard']);
      setConfirmModal(null);
    },
    onError: () => {
      showToast('Gagal mengubah status transaksi.', 'error');
      setConfirmModal(null);
    },
  });

  const deleteMutation = useMutation({
    mutationFn: (id) => deleteAdminTransaction(id),
    onSuccess: () => {
      showToast('Transaksi berhasil dihapus.', 'success');
      queryClient.invalidateQueries(['adminTransactions']);
      queryClient.invalidateQueries(['adminDashboard']);
      setConfirmModal(null);
    },
    onError: () => {
      showToast('Gagal menghapus transaksi.', 'error');
      setConfirmModal(null);
    },
  });

  const handleConfirm = () => {
    if (!confirmModal) return;
    if (confirmModal.type === 'status') {
      updateMutation.mutate({ id: confirmModal.id, status: confirmModal.newStatus });
    } else if (confirmModal.type === 'delete') {
      deleteMutation.mutate(confirmModal.id);
    }
  };

  const metrics = data?.metrics || {};
  const tableData = data?.table_data || [];

  const normalizeStatus = (s) => {
    const lower = String(s).toLowerCase();
    if (['completed', 'success', 'selesai'].includes(lower)) return 'Berhasil';
    if (['pending', 'waiting', 'proses', 'processing'].includes(lower)) return 'Menunggu';
    if (['cancelled', 'failed', 'batal', 'reject'].includes(lower)) return 'Batal';
    return s;
  };

  if (isLoading) return <div style={{ padding: '2rem' }}>Memuat data transaksi...</div>;
  if (error) return <div style={{ padding: '2rem', color: 'var(--color-danger)' }}>Gagal memuat data transaksi.</div>;

  return (
    <div>
      {/* Action Buttons */}
      <div style={{ display: 'flex', gap: '0.75rem', marginBottom: '1.5rem', flexWrap: 'wrap' }}>
        <button className="admin-btn admin-btn-primary" onClick={() => showToast('Endpoint catat transaksi belum tersedia.', 'error')}>
          <Plus size={16} /> Catat Transaksi Baru
        </button>
        <button className="admin-btn admin-btn-outline" onClick={() => showToast('Fitur ekspor laporan belum tersedia.', 'error')}>
          <FileDown size={16} /> Ekspor Laporan
        </button>
        <div style={{ marginLeft: 'auto', display: 'flex', gap: '0.75rem', alignItems: 'center' }}>
          <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
            <Calendar size={16} style={{ color: 'var(--color-text-muted)' }} />
            <input type="date" className="admin-filter-input" value={dateFilter} onChange={e => setDateFilter(e.target.value)} />
          </div>
          <select className="admin-filter-input" value={statusFilter} onChange={e => setStatusFilter(e.target.value)}>
            <option value="all">Semua Status</option>
            <option value="Completed">Berhasil</option>
            <option value="Pending">Menunggu</option>
            <option value="Reject">Batal</option>
          </select>
        </div>
      </div>

      {/* KPI */}
      <div className="admin-kpi-grid">
        <MetricCard title="Total Penjualan Hari Ini" value={formatCurrency(metrics.total_penjualan || 0)} icon={Banknote} />
        <MetricCard title="Jumlah Transaksi" value={`${metrics.jumlah_transaksi || 0}`} icon={ShoppingBag} subtitle={`${tableData.length} Pesanan`} />
        <MetricCard title="Produk Terlaris" value={metrics.produk_terlaris || '-'} icon={TrendingUp} />
        <MetricCard title="Rata-rata Keranjang" value={formatCurrency(metrics.rata_keranjang || 0)} icon={ShoppingCart} />
      </div>

      {/* Search + Table */}
      <div className="admin-card">
        <div className="admin-card-header">
          <h3>Riwayat Transaksi</h3>
          <div style={{ display: 'flex', alignItems: 'center', gap: '0.75rem' }}>
            <span style={{ fontSize: '0.85rem', color: 'var(--color-text-muted)' }}>Tampilkan</span>
            <select className="admin-filter-input" value={limit} onChange={e => setLimit(Number(e.target.value))} style={{ width: '70px' }}>
              <option value={10}>10</option>
              <option value={25}>25</option>
              <option value={50}>50</option>
            </select>
            <span style={{ fontSize: '0.85rem', color: 'var(--color-text-muted)' }}>data</span>
          </div>
        </div>

        <div style={{ marginBottom: '1rem' }}>
          <input
            type="text"
            className="admin-filter-input"
            placeholder="Cari ID transaksi atau nama pelanggan..."
            value={search}
            onChange={e => setSearch(e.target.value)}
            style={{ width: '100%', maxWidth: '350px' }}
          />
        </div>

        {tableData.length > 0 ? (
          <div className="admin-table-wrap">
            <table className="admin-table">
              <thead>
                <tr>
                  <th>ID Transaksi</th>
                  <th>Waktu</th>
                  <th>Metode</th>
                  <th>Item</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                {tableData.map((row, i) => (
                  <tr key={row.order_id || i}>
                    <td style={{ color: 'var(--color-primary)', fontWeight: 600 }}>
                      #TRX-{String(row.order_id).padStart(4, '0')}
                    </td>
                    <td style={{ fontSize: '0.85rem', color: 'var(--color-text-muted)' }}>{formatDate(row.created_at)}</td>
                    <td>{row.payment_method || 'Tunai'}</td>
                    <td style={{ maxWidth: '200px', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>{row.items_text || '-'}</td>
                    <td style={{ fontWeight: 700 }}>{formatCurrency(row.total_price)}</td>
                    <td><StatusBadge status={normalizeStatus(row.status)} /></td>
                    <td>
                      <div style={{ display: 'flex', gap: '0.5rem' }}>
                        <button style={{ background: 'none', border: 'none', cursor: 'pointer', color: 'var(--color-primary)' }} title="Lihat detail">
                          <Eye size={16} />
                        </button>
                        {row.status !== 'Completed' && (
                          <button
                            style={{ background: 'none', border: 'none', cursor: 'pointer', color: 'var(--color-primary)' }}
                            title="Ubah status"
                            onClick={() => setConfirmModal({ type: 'status', id: row.order_id, newStatus: 'Completed' })}
                          >
                            <Pencil size={16} />
                          </button>
                        )}
                        <button
                          style={{ background: 'none', border: 'none', cursor: 'pointer', color: 'var(--color-danger)' }}
                          title="Hapus"
                          onClick={() => setConfirmModal({ type: 'delete', id: row.order_id })}
                        >
                          <Trash2 size={16} />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        ) : (
          <p style={{ textAlign: 'center', color: 'var(--color-text-muted)', padding: '3rem 0' }}>Belum ada transaksi.</p>
        )}
      </div>

      {/* Confirmation Modal */}
      {confirmModal && (
        <div className="admin-modal-overlay" onClick={() => setConfirmModal(null)}>
          <div className="admin-modal" onClick={e => e.stopPropagation()}>
            <h3>{confirmModal.type === 'delete' ? 'Hapus Transaksi?' : 'Ubah Status?'}</h3>
            <p>
              {confirmModal.type === 'delete'
                ? `Anda akan menghapus transaksi #${confirmModal.id} secara permanen. Tindakan ini tidak bisa dibatalkan.`
                : `Anda akan mengubah status transaksi #${confirmModal.id} menjadi "${confirmModal.newStatus}".`
              }
            </p>
            <div className="admin-modal-actions">
              <button className="admin-btn admin-btn-outline" onClick={() => setConfirmModal(null)}>Batal</button>
              <button
                className={`admin-btn ${confirmModal.type === 'delete' ? 'admin-btn-danger' : 'admin-btn-primary'}`}
                onClick={handleConfirm}
                disabled={updateMutation.isPending || deleteMutation.isPending}
                style={confirmModal.type === 'delete' ? { backgroundColor: 'var(--color-danger)', color: 'white' } : {}}
              >
                {(updateMutation.isPending || deleteMutation.isPending) ? 'Memproses...' : 'Konfirmasi'}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
