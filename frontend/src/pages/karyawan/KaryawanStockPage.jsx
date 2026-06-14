import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { Search, Plus } from 'lucide-react';
import { getStockMaterials } from '../../services/karyawanStockService';
import { formatDate } from '../../utils/formatDate';
import './KaryawanStockPage.css';

export default function KaryawanStockPage() {
  const [searchTerm, setSearchTerm] = useState('');

  const { data: response, isLoading, error } = useQuery({
    queryKey: ['karyawanStocks'],
    queryFn: getStockMaterials
  });

  const stocks = Array.isArray(response) ? response : (response?.data || []);
  
  const filteredStocks = stocks.filter(stock => 
    (stock.name || stock.item_name || '').toLowerCase().includes(searchTerm.toLowerCase())
  );

  const getStatus = (qty, unit) => {
    // Basic threshold logic based on unit
    let min = 10;
    if (unit === 'kg' || unit === 'liter') min = 5;
    
    if (qty <= min / 2) return 'danger';
    if (qty <= min) return 'warning';
    return 'safe';
  };

  const getStatusText = (status) => {
    if (status === 'danger') return 'Kritis';
    if (status === 'warning') return 'Menipis';
    return 'Aman';
  };

  if (isLoading) return <div style={{ padding: '2rem' }}>Memuat data stok...</div>;
  if (error) return <div style={{ padding: '2rem', color: 'red' }}>Gagal memuat stok bahan.</div>;

  const totalItems = stocks.length;
  const criticalItems = stocks.filter(s => getStatus(s.quantity, s.unit) === 'danger').length;
  const warningItems = stocks.filter(s => getStatus(s.quantity, s.unit) === 'warning').length;
  const safeItems = stocks.filter(s => getStatus(s.quantity, s.unit) === 'safe').length;

  return (
    <div className="karyawan-stock-page">
      <div className="karyawan-page-header">
        <div>
          <h1 className="karyawan-page-title">Gudang & Stok Bahan</h1>
          <p className="karyawan-page-subtitle">Pantau ketersediaan bahan baku untuk bar dan dapur</p>
        </div>
      </div>

      <div className="karyawan-stock-summary">
        <div className="stock-summary-card total">
          <span className="stock-summary-label">Total Item</span>
          <span className="stock-summary-value">{totalItems}</span>
        </div>
        <div className="stock-summary-card safe">
          <span className="stock-summary-label">Stok Aman</span>
          <span className="stock-summary-value">{safeItems}</span>
        </div>
        <div className="stock-summary-card warning">
          <span className="stock-summary-label">Stok Menipis</span>
          <span className="stock-summary-value">{warningItems}</span>
        </div>
        <div className="stock-summary-card danger">
          <span className="stock-summary-label">Stok Kritis</span>
          <span className="stock-summary-value">{criticalItems}</span>
        </div>
      </div>

      <div className="karyawan-table-container">
        <div className="karyawan-table-toolbar">
          <div className="karyawan-table-search">
            <Search size={20} color="var(--color-text-muted)" />
            <input 
              type="text" 
              placeholder="Cari nama bahan baku..." 
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
            />
          </div>
        </div>

        <table className="karyawan-stock-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nama Bahan</th>
              <th>Kategori</th>
              <th>Sisa Stok</th>
              <th>Status</th>
              <th>Terakhir Update</th>
            </tr>
          </thead>
          <tbody>
            {filteredStocks.map((stock) => {
              const status = getStatus(stock.quantity, stock.unit);
              return (
                <tr key={stock.id}>
                  <td style={{ color: 'var(--color-text-muted)' }}>#{stock.id}</td>
                  <td className="stock-item-name">{stock.name || stock.item_name}</td>
                  <td>-</td>
                  <td style={{ fontWeight: 600 }}>{stock.quantity} {stock.unit}</td>
                  <td>
                    <span className={`stock-qty-badge ${status}`}>
                      {getStatusText(status)}
                    </span>
                  </td>
                  <td style={{ fontSize: '0.85rem', color: 'var(--color-text-muted)' }}>
                    {formatDate(stock.updated_at)}
                  </td>
                </tr>
              );
            })}
            
            {filteredStocks.length === 0 && (
              <tr>
                <td colSpan="6" style={{ textAlign: 'center', padding: '3rem' }}>
                  Bahan baku tidak ditemukan.
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
