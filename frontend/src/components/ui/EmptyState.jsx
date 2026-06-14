import { Coffee } from 'lucide-react';

export default function EmptyState({ title = 'Tidak ada data', description = '', icon: Icon = Coffee }) {
  return (
    <div style={{
      display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center',
      padding: '4rem 2rem', textAlign: 'center', color: 'var(--color-text-muted)',
    }}>
      <Icon size={48} strokeWidth={1.5} style={{ marginBottom: '1rem', opacity: 0.5 }} />
      <h3 style={{ fontFamily: 'var(--font-heading)', fontSize: 'var(--text-lg)', color: 'var(--color-text-secondary)', marginBottom: '0.5rem' }}>{title}</h3>
      {description && <p style={{ fontSize: 'var(--text-sm)' }}>{description}</p>}
    </div>
  );
}
