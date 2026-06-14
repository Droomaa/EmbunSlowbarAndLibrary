export default function OwnerSettingsPage() {
  return (
    <div className="owner-settings-page">
      <div className="owner-table-header-row" style={{ padding: '0 0 1.5rem 0', borderBottom: 'none' }}>
        <div>
          <h2 className="owner-table-title">System Settings</h2>
          <p className="owner-table-subtitle">Configure your cafe preferences.</p>
        </div>
      </div>
      <div style={{ backgroundColor: 'var(--color-bg-surface)', padding: '3rem', borderRadius: 'var(--radius-lg)', textAlign: 'center', color: 'var(--color-text-muted)' }}>
        Fitur pengaturan akan segera hadir di update berikutnya.
      </div>
    </div>
  );
}
