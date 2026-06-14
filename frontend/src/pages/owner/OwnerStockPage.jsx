import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Download, Plus } from 'lucide-react';
import OwnerTable from '../../components/owner/OwnerTable';
import StatusBadge from '../../components/owner/StatusBadge';
import Modal from '../../components/ui/Modal';
import { getStockReports, addInventory } from '../../services/ownerInventoryService';
import { showToast } from '../../components/ui/Toast';
import './OwnerStockPage.css';

export default function OwnerStockPage() {
  const queryClient = useQueryClient();
  const [isAddModalOpen, setIsAddModalOpen] = useState(false);
  const [formData, setFormData] = useState({
    name: '',
    quantity: '',
    unit: 'kg',
    min_stock_level: ''
  });

  const { data, isLoading } = useQuery({
    queryKey: ['ownerStock'],
    queryFn: getStockReports
  });

  const inventories = data?.data || [];

  const addMutation = useMutation({
    mutationFn: addInventory,
    onSuccess: () => {
      showToast('Stok berhasil ditambahkan', 'success');
      setIsAddModalOpen(false);
      setFormData({ name: '', quantity: '', unit: 'kg', min_stock_level: '' });
      queryClient.invalidateQueries({ queryKey: ['ownerStock'] });
    },
    onError: () => {
      showToast('Gagal menambahkan stok', 'error');
    }
  });

  const handleExport = () => {
    showToast('Fitur export belum tersedia.', 'warning');
  };

  const handleAddSubmit = (e) => {
    e.preventDefault();
    if (!formData.name || !formData.quantity || !formData.min_stock_level) {
      showToast('Harap isi semua field', 'error');
      return;
    }
    addMutation.mutate({
      item_name: formData.name,
      quantity: Number(formData.quantity),
      unit: formData.unit,
      min_stock_level: Number(formData.min_stock_level)
    });
  };

  const getStockStatus = (qty, min) => {
    if (qty <= 0) return 'danger';
    if (qty <= min) return 'warning';
    return 'safe';
  };

  const columns = [
    { header: 'ITEM NAME' },
    { header: 'CURRENT STOCK' },
    { header: 'MIN. LEVEL' },
    { header: 'UNIT' },
    { header: 'STATUS' },
    { header: 'STOCK LEVEL' },
  ];

  return (
    <div className="owner-stock-page">
      <div className="owner-table-header-row" style={{ padding: '0 0 1.5rem 0', borderBottom: 'none' }}>
        <div>
          <h2 className="owner-table-title">Inventory Health</h2>
          <p className="owner-table-subtitle">Monitor raw materials and set reorder alerts.</p>
        </div>
        <div className="owner-table-actions">
          <button className="btn btn-primary" onClick={() => setIsAddModalOpen(true)} style={{ display: 'flex', gap: '0.5rem', alignItems: 'center' }}>
            <Plus size={18} /> Add Stock
          </button>
          <button className="btn btn-outline" onClick={handleExport} style={{ display: 'flex', gap: '0.5rem', alignItems: 'center' }}>
            <Download size={18} /> Export PDF
          </button>
        </div>
      </div>

      <OwnerTable 
        columns={columns}
        data={inventories}
        isLoading={isLoading}
        emptyMessage="Data inventory belum tersedia."
        renderRow={(item, i) => {
          const statusLevel = getStockStatus(item.quantity, item.min_stock_level);
          const percent = Math.min((item.quantity / (item.min_stock_level * 3)) * 100, 100);
          
          return (
            <tr key={i}>
              <td style={{ fontWeight: '600' }}>{item.name}</td>
              <td style={{ fontSize: '1.1rem', fontWeight: '700' }}>{item.quantity}</td>
              <td style={{ color: 'var(--color-text-muted)' }}>{item.min_stock_level}</td>
              <td>{item.unit}</td>
              <td>
                <StatusBadge 
                  status={statusLevel === 'safe' ? 'Safe' : (statusLevel === 'warning' ? 'Low Stock' : 'Out of Stock')} 
                  tone={statusLevel}
                />
              </td>
              <td>
                <div className="stock-bar-wrapper">
                  <div className={`stock-bar-fill ${statusLevel}`} style={{ width: `${percent}%` }}></div>
                </div>
              </td>
            </tr>
          );
        }}
      />

      <Modal 
        isOpen={isAddModalOpen} 
        onClose={() => setIsAddModalOpen(false)} 
        title="Add Inventory"
      >
        <form onSubmit={handleAddSubmit} style={{ display: 'flex', flexDirection: 'column', gap: '1rem' }}>
          <div>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Item Name</label>
            <input 
              type="text" 
              className="owner-search-input" 
              style={{ paddingLeft: '1rem' }}
              value={formData.name}
              onChange={e => setFormData({...formData, name: e.target.value})}
              required
            />
          </div>
          <div style={{ display: 'flex', gap: '1rem' }}>
            <div style={{ flex: 1 }}>
              <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Quantity</label>
              <input 
                type="number" 
                className="owner-search-input" 
                style={{ paddingLeft: '1rem' }}
                value={formData.quantity}
                onChange={e => setFormData({...formData, quantity: e.target.value})}
                required
              />
            </div>
            <div style={{ flex: 1 }}>
              <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Unit</label>
              <select 
                className="owner-chart-filter" 
                style={{ width: '100%', padding: '0.6rem 1rem' }}
                value={formData.unit}
                onChange={e => setFormData({...formData, unit: e.target.value})}
              >
                <option value="kg">kg</option>
                <option value="gr">gr</option>
                <option value="liter">liter</option>
                <option value="ml">ml</option>
                <option value="pcs">pcs</option>
              </select>
            </div>
          </div>
          <div>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Min. Stock Level</label>
            <input 
              type="number" 
              className="owner-search-input" 
              style={{ paddingLeft: '1rem' }}
              value={formData.min_stock_level}
              onChange={e => setFormData({...formData, min_stock_level: e.target.value})}
              required
            />
          </div>
          <button type="submit" className="btn btn-primary" style={{ marginTop: '1rem' }} disabled={addMutation.isPending}>
            {addMutation.isPending ? 'Saving...' : 'Save Inventory'}
          </button>
        </form>
      </Modal>
    </div>
  );
}
