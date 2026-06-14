import { useQuery } from '@tanstack/react-query';
import { Banknote, ShoppingBag, AlertTriangle, CalendarDays } from 'lucide-react';
import MetricCard from '../../components/owner/MetricCard';
import OwnerChartBar from '../../components/owner/OwnerChartBar';
import OwnerTable from '../../components/owner/OwnerTable';
import StatusBadge from '../../components/owner/StatusBadge';
import { formatCurrency } from '../../utils/formatCurrency';
import { getOwnerDashboard } from '../../services/ownerDashboardService';
import './OwnerDashboard.css';

export default function OwnerDashboard() {
  const { data, isLoading, isError } = useQuery({
    queryKey: ['ownerDashboard'],
    queryFn: getOwnerDashboard
  });

  const d = data || {
    today_sales: 0,
    total_orders: 0,
    low_stock_alerts: 0,
    active_reservations: 0,
    stock_status: { available: 0, running_low: 0, out_of_stock: 0 }
  };

  // Activity Log placeholder columns
  const activityColumns = [
    { header: 'USER' },
    { header: 'ACTION' },
    { header: 'STATUS' },
    { header: 'TIMESTAMP' },
  ];

  return (
    <div className="owner-dashboard">
      <div className="dashboard-kpi-grid">
        <MetricCard 
          title="Today's Sales" 
          value={isLoading ? '...' : formatCurrency(d.today_sales)} 
          icon={Banknote} 
          trend="+12%" 
          tone="default"
        />
        <MetricCard 
          title="Total Orders" 
          value={isLoading ? '...' : d.total_orders} 
          icon={ShoppingBag} 
          trend="+5%" 
          tone="default"
        />
        <MetricCard 
          title="Low Stock Alerts" 
          value={isLoading ? '...' : `${d.low_stock_alerts} Items`} 
          subtitle={d.low_stock_alerts > 0 ? 'Action Needed' : 'All Good'}
          icon={AlertTriangle} 
          tone={d.low_stock_alerts > 0 ? 'danger' : 'default'}
        />
        <MetricCard 
          title="Active Reservations" 
          value={isLoading ? '...' : `${d.active_reservations} Tables`} 
          subtitle="Next: 14:00"
          icon={CalendarDays} 
          tone="default"
        />
      </div>

      <div className="dashboard-middle-row">
        <OwnerChartBar 
          data={[]} // Weekly sales data not provided by backend yet
        />
        
        <div className="stock-status-card">
          <div className="stock-status-header">
            <h2>Stock Status</h2>
            <p>Inventory health overview</p>
          </div>
          <div className="stock-list">
            <div className="stock-item">
              <div className="stock-item-left">
                <div className="stock-dot safe"></div>
                <div className="stock-item-info">
                  <span className="stock-item-title">Available</span>
                </div>
              </div>
              <span className="stock-item-count">{isLoading ? '...' : `${d.stock_status.available} items`}</span>
            </div>
            
            <div className="stock-item">
              <div className="stock-item-left">
                <div className="stock-dot low"></div>
                <div className="stock-item-info">
                  <span className="stock-item-title">Running Low</span>
                  {/* Backend only returns count right now, no item list */}
                  <span className="stock-item-sub">Data detail item belum tersedia</span>
                </div>
              </div>
              <span className="stock-item-count">{isLoading ? '...' : `${d.stock_status.running_low} items`}</span>
            </div>

            <div className="stock-item">
              <div className="stock-item-left">
                <div className="stock-dot out"></div>
                <div className="stock-item-info">
                  <span className="stock-item-title">Out of Stock</span>
                </div>
              </div>
              <span className="stock-item-count">{isLoading ? '...' : `${d.stock_status.out_of_stock} items`}</span>
            </div>
          </div>
        </div>
      </div>

      {/* Recent Activities */}
      <OwnerTable 
        title="Recent Activities"
        subtitle="Last 5 system actions from all users"
        actions={<a href="#" style={{ color: 'var(--color-primary)', fontSize: '0.85rem', fontWeight: '600' }}>View All Activities</a>}
        columns={activityColumns}
        data={[]} // Empty because endpoint doesn't exist
        isLoading={false}
        emptyMessage="Data belum tersedia dari backend."
        renderRow={() => null}
      />
    </div>
  );
}
