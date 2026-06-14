import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { Download, FileText, Banknote, TrendingUp } from 'lucide-react';
import OwnerTable from '../../components/owner/OwnerTable';
import MetricCard from '../../components/owner/MetricCard';
import StatusBadge from '../../components/owner/StatusBadge';
import { formatCurrency } from '../../utils/formatCurrency';
import { formatDate } from '../../utils/formatDate';
import { getSalesReports } from '../../services/ownerReportService';
import { showToast } from '../../components/ui/Toast';

export default function OwnerSalesPage() {
  const [dateFilter, setDateFilter] = useState('This Month');

  const { data, isLoading } = useQuery({
    queryKey: ['ownerSales'],
    queryFn: getSalesReports
  });

  const d = data || { total_revenue: 0, avg_order_value: 0, transactions: [] };
  const transactions = d.transactions || [];

  const handleExport = () => {
    showToast('Fitur export belum tersedia.', 'warning');
  };

  const columns = [
    { header: 'ORDER ID' },
    { header: 'DATE' },
    { header: 'CUSTOMER' },
    { header: 'AMOUNT' },
    { header: 'STATUS' },
  ];

  return (
    <div className="owner-sales-page">
      <div className="owner-table-header-row" style={{ padding: '0 0 1.5rem 0', borderBottom: 'none' }}>
        <div>
          <h2 className="owner-table-title">Sales Reports</h2>
          <p className="owner-table-subtitle">Track your revenue, transaction history, and sales performance.</p>
        </div>
        <div className="owner-table-actions">
          <select 
            className="owner-chart-filter"
            value={dateFilter}
            onChange={(e) => setDateFilter(e.target.value)}
          >
            <option>Today</option>
            <option>This Week</option>
            <option>This Month</option>
            <option>All Time</option>
          </select>
          <button className="btn btn-outline" onClick={handleExport} style={{ display: 'flex', gap: '0.5rem', alignItems: 'center' }}>
            <FileText size={18} /> Export CSV
          </button>
        </div>
      </div>

      <div className="dashboard-kpi-grid" style={{ gridTemplateColumns: 'repeat(2, 1fr)' }}>
        <MetricCard 
          title="Total Revenue" 
          value={isLoading ? '...' : formatCurrency(d.total_revenue)} 
          icon={Banknote} 
          trend="+15%" 
        />
        <MetricCard 
          title="Avg. Order Value" 
          value={isLoading ? '...' : formatCurrency(d.avg_order_value)} 
          icon={TrendingUp} 
          trend="+2%" 
        />
      </div>

      <OwnerTable 
        title="Transaction History"
        columns={columns}
        data={transactions}
        isLoading={isLoading}
        emptyMessage="Data transaksi belum tersedia."
        renderRow={(tx, i) => (
          <tr key={i}>
            <td style={{ fontWeight: '600' }}>#{tx.id}</td>
            <td>{formatDate(tx.created_at)}</td>
            <td>{tx.user_id ? `User ID ${tx.user_id}` : 'Walk-in'}</td>
            <td>{formatCurrency(tx.total_price)}</td>
            <td><StatusBadge status={tx.status} /></td>
          </tr>
        )}
      />
    </div>
  );
}
