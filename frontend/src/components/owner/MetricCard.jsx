import { TrendingUp, TrendingDown } from 'lucide-react';
import './MetricCard.css';

export default function MetricCard({ 
  title, 
  value, 
  subtitle, 
  icon: Icon, 
  tone = 'default', // default, danger, warning, success
  trend // e.g. "+12%" or "-5%"
}) {
  return (
    <div className={`metric-card ${tone}`}>
      <div className="metric-card-header">
        {Icon && (
          <div className="metric-icon-wrapper">
            <Icon size={20} />
          </div>
        )}
        {trend && (
          <div className={`metric-trend ${trend.startsWith('+') ? 'up' : 'down'}`}>
            {trend.startsWith('+') ? <TrendingUp size={14} /> : <TrendingDown size={14} />}
            {trend}
          </div>
        )}
      </div>
      
      <div className="metric-title">{title}</div>
      <div className="metric-value">{value}</div>
      
      {subtitle && <div className="metric-subtitle">{subtitle}</div>}
    </div>
  );
}
