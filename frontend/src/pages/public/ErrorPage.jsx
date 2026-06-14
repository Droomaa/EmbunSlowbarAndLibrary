import { Link } from 'react-router-dom';

export default function ErrorPage() {
  return (
    <div style={{ minHeight: '60vh', display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', textAlign: 'center' }}>
      <h1 style={{ fontSize: '4rem', color: 'var(--color-primary)', margin: 0, fontFamily: 'var(--font-heading)' }}>404</h1>
      <p style={{ fontSize: '1.25rem', color: 'var(--color-text-main)', marginBottom: '2rem' }}>Oops! Halaman yang Anda cari tidak ditemukan.</p>
      <Link to="/" className="btn btn-primary">Kembali ke Beranda</Link>
    </div>
  );
}
