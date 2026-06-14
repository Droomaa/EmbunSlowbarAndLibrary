import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { AlertTriangle, AlertCircle, CheckCircle2, Package, FileDown, TrendingUp, ClipboardList } from 'lucide-react';
import { getAdminStockReport } from '../../services/adminStockService';
import StatusBadge from '../../components/owner/StatusBadge';
import { showToast } from '../../components/ui/Toast';
import { formatDate } from '../../utils/formatDate';

const CATEGORIES = ['Semua', 'Kopi', 'Susu & Krim', 'Bahan Makanan'];

export default function AdminStockPage() {
  const [activeCategory, setActiveCategory] = useState('Semua');
  const [statusFilter, setStatusFilter] = useState('all');
  const [search, setSearch] = useState('');

  const params = {};
  if (activeCategory !== 'Semua') params.kategori = activeCategory;
  if (statusFilter !== 'all') params.status = statusFilter;
  if (search) params.search = search;

  const { data, isLoading, error } = useQuery({
    queryKey: ['adminStock', params],
    queryFn: () => getAdminStockReport(params),
  });

  const metrics = data?.metrics || {};
  const tableData = data?.table_data || [];

  const getStockStatus = (qty) => {
    if (qty <= 0) return { label: 'HABIS', color: 'var(--color-danger)' };
    if (qty <= 10) return { label: 'MENIPIS', color: '#e6a817' };
    return { label: 'AMAN', color: 'var(--color-primary)' };
  };

  if (isLoading) return <div style={{ padding: '2rem' }}>Memuat laporan stok bahan...</div>;
  if (error) return <div style={{ padding: '2rem', color: 'var(--color-danger)' }}>Gagal memuat data stok.</div>;

  return (
    <div>
      {/* Summary Cards */}
      <div className="admin-kpi-grid" style={{ gridTemplateColumns: 'repeat(3, 1fr) auto' }}>
        <div className="admin-card" style={{ textAlign: 'center' }}>
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '0.75rem' }}>
            <AlertTriangle size={24} color="var(--color-danger)" />
            <span style={{ fontSize: '0.7rem', fontWeight: 700, color: 'var(--color-danger)', backgroundColor: '#fde8e8', padding: '0.2rem 0.5rem', borderRadius: '100px' }}>CRITICAL</span>
          </div>
          <div style={{ fontSize: '2rem', fontWeight: 800, color: 'var(--color-text-main)' }}>{String(metrics.habis || 0).padStart(2, '0')}</div>
          <div style={{ fontSize: '0.85rem', color: 'var(--color-text-muted)' }}>Bahan Habis</div>
        </div>

        <div className="admin-card" style={{ textAlign: 'center' }}>
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '0.75rem' }}>
            <AlertCircle size={24} color="#e6a817" />
            <span style={{ fontSize: '0.7rem', fontWeight: 700, color: '#e6a817', backgroundColor: '#fef3c7', padding: '0.2rem 0.5rem', borderRadius: '100px' }}>LOW STOCK</span>
          </div>
          <div style={{ fontSize: '2rem', fontWeight: 800, color: 'var(--color-text-main)' }}>{String(metrics.hampir_habis || 0).padStart(2, '0')}</div>
          <div style={{ fontSize: '0.85rem', color: 'var(--color-text-muted)' }}>Hampir Habis</div>
        </div>

        <div className="admin-card" style={{ textAlign: 'center' }}>
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '0.75rem' }}>
            <CheckCircle2 size={24} color="var(--color-primary)" />
            <span style={{ fontSize: '0.7rem', fontWeight: 700, color: 'var(--color-primary)', backgroundColor: '#e8f5e9', padding: '0.2rem 0.5rem', borderRadius: '100px' }}>SAFE</span>
          </div>
          <div style={{ fontSize: '2rem', fontWeight: 800, color: 'var(--color-text-main)' }}>{metrics.aman || 0}</div>
          <div style={{ fontSize: '0.85rem', color: 'var(--color-text-muted)' }}>Bahan Tersedia Aman</div>
          <div style={{ fontSize: '0.75rem', color: 'var(--color-text-muted)', marginTop: '0.25rem' }}>Total Inventaris: <strong>{metrics.total || 0} Item</strong></div>
        </div>
      </div>

      {/* Detail Table */}
      <div className="admin-card" style={{ marginBottom: '1.5rem' }}>
        <div className="admin-card-header">
          <h3>Detail Persediaan</h3>
          <div style={{ display: 'flex', gap: '0.5rem', alignItems: 'center' }}>
            <select className="admin-filter-input" value={statusFilter} onChange={e => setStatusFilter(e.target.value)}>
              <option value="all">Filter Status</option>
              <option value="habis">Habis</option>
              <option value="menipis">Menipis</option>
              <option value="aman">Aman</option>
            </select>
            <button className="admin-btn admin-btn-outline" onClick={() => showToast('Fitur Ekspor PDF belum tersedia.', 'error')}>
              <FileDown size={16} /> Ekspor PDF
            </button>
          </div>
        </div>

        {/* Category pills */}
        <div className="admin-pills" style={{ marginBottom: '1rem' }}>
          {CATEGORIES.map(c => (
            <button key={c} className={`admin-pill ${activeCategory === c ? 'active' : ''}`} onClick={() => setActiveCategory(c)}>{c}</button>
          ))}
        </div>

        {/* Search */}
        <div style={{ marginBottom: '1rem' }}>
          <input
            type="text"
            className="admin-filter-input"
            placeholder="Cari bahan..."
            value={search}
            onChange={e => setSearch(e.target.value)}
            style={{ width: '100%', maxWidth: '300px' }}
          />
        </div>

        {tableData.length > 0 ? (
          <div className="admin-table-wrap">
            <table className="admin-table">
              <thead>
                <tr>
                  <th>Bahan Baku</th>
                  <th>Kategori</th>
                  <th>Stok Saat Ini</th>
                  <th>Stok Minimum</th>
                  <th>Status</th>
                  <th>Update Terakhir</th>
                </tr>
              </thead>
              <tbody>
                {tableData.map((item, i) => {
                  const qty = item.quantity ?? item.stock_quantity ?? 0;
                  const status = getStockStatus(qty);
                  return (
                    <tr key={item.id || i}>
                      <td>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '0.75rem' }}>
                          <div style={{ width: '36px', height: '36px', borderRadius: 'var(--radius-sm)', backgroundColor: 'var(--color-bg-main)', display: 'flex', alignItems: 'center', justifyContent: 'center', color: 'var(--color-primary)' }}>
                            <Package size={18} />
                          </div>
                          <span style={{ fontWeight: 600 }}>{item.item_name || item.name || '-'}</span>
                        </div>
                      </td>
                      <td><span style={{ padding: '0.2rem 0.6rem', borderRadius: '100px', backgroundColor: 'var(--color-bg-main)', fontSize: '0.8rem' }}>{item.category || 'Umum'}</span></td>
                      <td style={{ fontWeight: 700, color: status.color }}>{qty} {item.unit || ''}</td>
                      <td style={{ color: 'var(--color-text-muted)' }}>{item.minimum_stock ?? item.min_stock ?? 10} {item.unit || ''}</td>
                      <td>
                        <span style={{ display: 'inline-flex', alignItems: 'center', gap: '0.3rem', fontSize: '0.8rem', fontWeight: 700, color: status.color }}>
                          <span style={{ width: '6px', height: '6px', borderRadius: '50%', backgroundColor: status.color }} />
                          {status.label}
                        </span>
                      </td>
                      <td style={{ fontSize: '0.85rem', color: 'var(--color-text-muted)' }}>{item.updated_at ? formatDate(item.updated_at) : '-'}</td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
            <p style={{ fontSize: '0.75rem', color: 'var(--color-text-muted)', marginTop: '0.75rem', fontStyle: 'italic' }}>
              * Status stok dihitung sementara berdasarkan threshold frontend (minimum: 10). Kategori diperkirakan dari nama bahan.
            </p>
          </div>
        ) : (
          <p style={{ textAlign: 'center', color: 'var(--color-text-muted)', padding: '3rem 0' }}>Belum ada data stok bahan.</p>
        )}
      </div>

      {/* Bottom: Prediction + Notes */}
      <div className="admin-bottom-row">
        <div className="admin-card">
          <div style={{ display: 'flex', alignItems: 'center', gap: '0.75rem', marginBottom: '1rem' }}>
            <TrendingUp size={22} color="var(--color-primary)" />
            <h3 style={{ margin: 0, fontFamily: 'var(--font-heading)' }}>Prediksi Kebutuhan Minggu Depan</h3>
          </div>
          <p style={{ color: 'var(--color-text-muted)', lineHeight: 1.6 }}>
            Prediksi belum tersedia karena data pemakaian bahan belum lengkap. Untuk mengaktifkan prediksi, backend perlu menyimpan riwayat pemakaian bahan per transaksi.
          </p>
        </div>

        <div className="admin-card">
          <div style={{ display: 'flex', alignItems: 'center', gap: '0.75rem', marginBottom: '1rem' }}>
            <ClipboardList size={22} color="var(--color-primary)" />
            <h3 style={{ margin: 0, fontFamily: 'var(--font-heading)' }}>Catatan Inventaris Terakhir</h3>
          </div>
          <p style={{ color: 'var(--color-text-muted)', lineHeight: 1.6 }}>
            Catatan inventaris belum tersedia dari backend. Fitur stock opname memerlukan endpoint terpisah.
          </p>
        </div>
      </div>
    </div>
  );
}
