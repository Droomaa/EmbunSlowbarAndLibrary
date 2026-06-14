import { Truck, Search, Plus } from 'lucide-react';
import './KaryawanStockPage.css'; // Reuse table CSS if needed

export default function KaryawanDeliveryPage() {
  return (
    <div className="karyawan-stock-page">
      <div className="karyawan-page-header">
        <div>
          <h1 className="karyawan-page-title">Pengiriman Stok Bahan</h1>
          <p className="karyawan-page-subtitle">Verifikasi bahan baku yang baru tiba dari supplier</p>
        </div>
      </div>

      <div className="karyawan-table-container" style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', padding: '6rem 2rem', textAlign: 'center' }}>
        <div style={{ backgroundColor: '#f1f5f9', width: '80px', height: '80px', borderRadius: '50%', display: 'flex', alignItems: 'center', justifyContent: 'center', marginBottom: '1.5rem', color: 'var(--color-primary)' }}>
          <Truck size={40} />
        </div>
        <h2 style={{ fontFamily: 'var(--font-heading)', margin: '0 0 0.5rem 0' }}>Fitur Belum Tersedia</h2>
        <p style={{ color: 'var(--color-text-muted)', maxWidth: '400px', margin: '0 0 1.5rem 0', lineHeight: 1.5 }}>
          Endpoint backend untuk modul pengiriman stok bahan (Deliveries) belum dikembangkan. Halaman ini disiapkan untuk integrasi di masa mendatang.
        </p>
        <button className="btn-karyawan-light" disabled style={{ opacity: 0.5, cursor: 'not-allowed', backgroundColor: 'var(--color-bg-main)', border: '1px solid var(--color-border)' }}>
          Menunggu Update Backend
        </button>
      </div>
    </div>
  );
}
