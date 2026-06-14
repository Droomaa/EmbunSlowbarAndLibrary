import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Plus } from 'lucide-react';
import OwnerTable from '../../components/owner/OwnerTable';
import StatusBadge from '../../components/owner/StatusBadge';
import Modal from '../../components/ui/Modal';
import { getOwnerAccounts, addOwnerAccount } from '../../services/ownerAccountService';
import { showToast } from '../../components/ui/Toast';

export default function OwnerAccountsPage() {
  const queryClient = useQueryClient();
  const [isAddModalOpen, setIsAddModalOpen] = useState(false);
  const [formData, setFormData] = useState({
    username: '',
    password: '',
    role: 'Admin'
  });

  const { data, isLoading } = useQuery({
    queryKey: ['ownerAccounts'],
    queryFn: getOwnerAccounts
  });

  const accounts = data?.accounts || [];

  const addMutation = useMutation({
    mutationFn: addOwnerAccount,
    onSuccess: () => {
      showToast('Akun berhasil ditambahkan', 'success');
      setIsAddModalOpen(false);
      setFormData({ username: '', password: '', role: 'Admin' });
      queryClient.invalidateQueries({ queryKey: ['ownerAccounts'] });
    },
    onError: () => {
      showToast('Gagal menambahkan akun', 'error');
    }
  });

  const handleAddSubmit = (e) => {
    e.preventDefault();
    if (!formData.username || !formData.password) {
      showToast('Username dan password wajib diisi', 'error');
      return;
    }
    addMutation.mutate(formData);
  };

  const columns = [
    { header: 'ID' },
    { header: 'USERNAME' },
    { header: 'ROLE' },
    { header: 'CREATED AT' },
  ];

  return (
    <div className="owner-accounts-page">
      <div className="owner-table-header-row" style={{ padding: '0 0 1.5rem 0', borderBottom: 'none' }}>
        <div>
          <h2 className="owner-table-title">Staff Accounts</h2>
          <p className="owner-table-subtitle">Manage system access for Admin and Karyawan.</p>
        </div>
        <div className="owner-table-actions">
          <button className="btn btn-primary" onClick={() => setIsAddModalOpen(true)} style={{ display: 'flex', gap: '0.5rem', alignItems: 'center' }}>
            <Plus size={18} /> Add Account
          </button>
        </div>
      </div>

      <OwnerTable 
        columns={columns}
        data={accounts}
        isLoading={isLoading}
        emptyMessage="Data akun belum tersedia."
        renderRow={(acc, i) => (
          <tr key={acc.id || i}>
            <td style={{ fontWeight: '600' }}>{acc.id}</td>
            <td>{acc.username}</td>
            <td><StatusBadge status={acc.role} tone={acc.role === 'Owner' ? 'warning' : 'default'} /></td>
            <td>{acc.created_at ? acc.created_at.substring(0, 10) : '-'}</td>
          </tr>
        )}
      />

      <Modal 
        isOpen={isAddModalOpen} 
        onClose={() => setIsAddModalOpen(false)} 
        title="Add Staff Account"
      >
        <form onSubmit={handleAddSubmit} style={{ display: 'flex', flexDirection: 'column', gap: '1rem' }}>
          <div>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Username</label>
            <input 
              type="text" 
              className="owner-search-input" 
              style={{ paddingLeft: '1rem' }}
              value={formData.username}
              onChange={e => setFormData({...formData, username: e.target.value})}
              required
            />
          </div>
          <div>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Password</label>
            <input 
              type="password" 
              className="owner-search-input" 
              style={{ paddingLeft: '1rem' }}
              value={formData.password}
              onChange={e => setFormData({...formData, password: e.target.value})}
              required
            />
          </div>
          <div>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Role</label>
            <select 
              className="owner-chart-filter" 
              style={{ width: '100%', padding: '0.6rem 1rem' }}
              value={formData.role}
              onChange={e => setFormData({...formData, role: e.target.value})}
            >
              <option value="Admin">Admin</option>
              <option value="Karyawan">Karyawan</option>
            </select>
          </div>
          <button type="submit" className="btn btn-primary" style={{ marginTop: '1rem' }} disabled={addMutation.isPending}>
            {addMutation.isPending ? 'Saving...' : 'Save Account'}
          </button>
        </form>
      </Modal>
    </div>
  );
}
