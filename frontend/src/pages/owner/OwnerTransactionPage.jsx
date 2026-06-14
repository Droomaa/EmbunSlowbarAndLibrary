import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import StatusBadge from '../../components/owner/StatusBadge';
import OwnerTable from '../../components/owner/OwnerTable';
import { getOwnerTransactions, updateTransactionStatus } from '../../services/ownerTransactionService';
import { formatCurrency } from '../../utils/formatCurrency';
import { formatDate } from '../../utils/formatDate';
import { showToast } from '../../components/ui/Toast';

export default function OwnerTransactionPage() {
  const queryClient = useQueryClient();
  const [statusFilter, setStatusFilter] = useState('All');

  const { data, isLoading } = useQuery({
    queryKey: ['ownerTransactions'],
    queryFn: getOwnerTransactions
  });

  // Extract from { transactions: [...] } based on backend
  const transactions = data?.transactions || [];

  const updateStatusMutation = useMutation({
    mutationFn: ({ id, status }) => updateTransactionStatus(id, status),
    onSuccess: () => {
      showToast('Status transaksi berhasil diperbarui', 'success');
      queryClient.invalidateQueries({ queryKey: ['ownerTransactions'] });
    },
    onError: () => {
      showToast('Gagal memperbarui status', 'error');
    }
  });

  const handleStatusChange = (id, newStatus) => {
    updateStatusMutation.mutate({ id, status: newStatus });
  };

  const filteredTransactions = transactions.filter(t => 
    statusFilter === 'All' ? true : t.status === statusFilter
  );

  const columns = [
    { header: 'ORDER ID' },
    { header: 'DATE' },
    { header: 'CUSTOMER' },
    { header: 'ITEMS' },
    { header: 'TOTAL' },
    { header: 'STATUS' },
    { header: 'ACTION' },
  ];

  return (
    <div className="owner-transaction-page">
      <div className="owner-table-header-row" style={{ padding: '0 0 1.5rem 0', borderBottom: 'none' }}>
        <div>
          <h2 className="owner-table-title">Transaction Management</h2>
          <p className="owner-table-subtitle">View and update customer order status.</p>
        </div>
        <div className="owner-table-actions">
          <select 
            className="owner-chart-filter"
            value={statusFilter}
            onChange={(e) => setStatusFilter(e.target.value)}
          >
            <option value="All">All Status</option>
            <option value="Pending">Pending</option>
            <option value="Completed">Completed</option>
            <option value="Cancelled">Cancelled</option>
          </select>
        </div>
      </div>

      <OwnerTable 
        columns={columns}
        data={filteredTransactions}
        isLoading={isLoading}
        emptyMessage="Belum ada transaksi."
        renderRow={(tx, i) => (
          <tr key={tx.id || i}>
            <td style={{ fontWeight: '600' }}>#{tx.id}</td>
            <td>{tx.created_at ? tx.created_at.substring(0, 10) : '-'}</td>
            <td>{tx.user_id ? `User ID ${tx.user_id}` : 'Walk-in'}</td>
            <td>{tx.items_count || '-'} items</td>
            <td>{formatCurrency(tx.total_price)}</td>
            <td><StatusBadge status={tx.status} /></td>
            <td>
              <select 
                className="owner-chart-filter" 
                style={{ padding: '0.25rem 0.5rem', fontSize: '0.8rem' }}
                value={tx.status}
                onChange={(e) => handleStatusChange(tx.id, e.target.value)}
                disabled={updateStatusMutation.isPending}
              >
                <option value="Pending">Pending</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
              </select>
            </td>
          </tr>
        )}
      />
    </div>
  );
}
