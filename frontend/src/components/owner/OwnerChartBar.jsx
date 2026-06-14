import './OwnerChartBar.css';

export default function OwnerChartBar({ 
  title = 'Weekly Sales Trends', 
  subtitle = 'Revenue performance over the last 7 days',
  data = [] // Array of { label: 'Mon', value: 120 }
}) {
  // Find max value to calculate height percentage
  const maxVal = data.length > 0 ? Math.max(...data.map(d => d.value)) : 0;

  return (
    <div className="owner-chart-wrapper">
      <div className="owner-chart-header">
        <div>
          <h2 className="owner-chart-title">{title}</h2>
          <p className="owner-chart-subtitle">{subtitle}</p>
        </div>
        <select className="owner-chart-filter">
          <option>Last 7 Days</option>
        </select>
      </div>

      {data.length === 0 ? (
        <div className="owner-chart-empty">
          Data grafik belum tersedia dari backend.
        </div>
      ) : (
        <div className="owner-chart-area">
          <div className="owner-chart-grid">
            <div className="owner-chart-grid-line"></div>
            <div className="owner-chart-grid-line"></div>
            <div className="owner-chart-grid-line"></div>
            <div className="owner-chart-grid-line"></div>
          </div>
          
          {data.map((item, index) => {
            const heightPercent = maxVal > 0 ? (item.value / maxVal) * 100 : 0;
            return (
              <div key={index} className="owner-chart-bar-group">
                <div 
                  className="owner-chart-bar" 
                  style={{ height: `${heightPercent}%` }}
                  title={`Rp ${item.value.toLocaleString()}`}
                ></div>
                <div className="owner-chart-label">{item.label}</div>
              </div>
            );
          })}
        </div>
      )}
    </div>
  );
}
