import './StatusBadge.css';

export default function StatusBadge({ status, tone }) {
  // Auto tone if tone not provided
  let determinedTone = tone || 'default';
  
  if (!tone && status) {
    const s = status.toLowerCase();
    if (s === 'success' || s === 'available' || s === 'safe' || s === 'completed' || s === 'paid') {
      determinedTone = 'success';
    } else if (s === 'warning' || s === 'low' || s === 'pending') {
      determinedTone = 'warning';
    } else if (s === 'danger' || s === 'error' || s === 'out of stock' || s === 'failed' || s === 'cancelled' || s === 'unavailable') {
      determinedTone = 'danger';
    } else if (s === 'modified') {
      determinedTone = 'modified';
    }
  }

  return (
    <span className={`status-badge ${determinedTone}`}>
      {status}
    </span>
  );
}
