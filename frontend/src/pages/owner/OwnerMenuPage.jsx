import { useState } from 'react';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Plus, Edit2, Trash2 } from 'lucide-react';
import OwnerTable from '../../components/owner/OwnerTable';
import StatusBadge from '../../components/owner/StatusBadge';
import MetricCard from '../../components/owner/MetricCard';
import Modal from '../../components/ui/Modal';
import { formatCurrency } from '../../utils/formatCurrency';
import { getOwnerMenus, addOwnerMenu, deleteOwnerMenu } from '../../services/ownerMenuService';
import { showToast } from '../../components/ui/Toast';
import menuPlaceholder from '../../assets/images/menu-placeholder.png';
import './OwnerMenuPage.css';

export default function OwnerMenuPage() {
  const queryClient = useQueryClient();
  const [searchTerm, setSearchTerm] = useState('');
  const [categoryFilter, setCategoryFilter] = useState('All Categories');
  const [isAddModalOpen, setIsAddModalOpen] = useState(false);

  // Form State
  const [formData, setFormData] = useState({
    menuName: '',
    category: 'Coffee',
    price: '',
    description: '',
    status: 'Available',
    image: null
  });

  const { data, isLoading } = useQuery({
    queryKey: ['ownerMenus'],
    queryFn: getOwnerMenus
  });

  // Extract from backend wrapper
  const menus = data?.menus || [];
  const metrics = data?.metrics || { total_catalog: 0, stock_alerts: 0, popular_name: 'N/A' };

  const addMutation = useMutation({
    mutationFn: addOwnerMenu,
    onSuccess: () => {
      showToast('Menu berhasil ditambahkan', 'success');
      setIsAddModalOpen(false);
      setFormData({ menuName: '', category: 'Coffee', price: '', description: '', status: 'Available', image: null });
      queryClient.invalidateQueries({ queryKey: ['ownerMenus'] });
    },
    onError: () => {
      showToast('Gagal menambahkan menu', 'error');
    }
  });

  const deleteMutation = useMutation({
    mutationFn: deleteOwnerMenu,
    onSuccess: () => {
      showToast('Menu berhasil dihapus', 'success');
      queryClient.invalidateQueries({ queryKey: ['ownerMenus'] });
    },
    onError: () => {
      showToast('Gagal menghapus menu', 'error');
    }
  });

  const handleAddSubmit = (e) => {
    e.preventDefault();
    if (!formData.menuName || !formData.price) {
      showToast('Nama dan harga wajib diisi', 'error');
      return;
    }

    const submitData = new FormData();
    submitData.append('menuName', formData.menuName);
    submitData.append('category', formData.category);
    submitData.append('price', formData.price);
    submitData.append('description', formData.description);
    submitData.append('status', formData.status);
    if (formData.image) {
      submitData.append('image', formData.image);
    }
    // ingredients empty array json string just to pass backend check if needed, 
    // or just omit it because controller checks request->has('ingredients')
    
    addMutation.mutate(submitData);
  };

  const handleDelete = (id) => {
    if (window.confirm('Yakin ingin menghapus menu ini?')) {
      deleteMutation.mutate(id);
    }
  };

  const handleEditClick = () => {
    showToast('Fitur edit menu akan tersedia di update berikutnya', 'warning');
  };

  // Filtering
  const filteredMenus = menus.filter(m => {
    const matchesSearch = m.menuName.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesCategory = categoryFilter === 'All Categories' || m.category === categoryFilter;
    return matchesSearch && matchesCategory;
  });

  const categories = ['All Categories', ...new Set(menus.map(m => m.category || 'Uncategorized'))];

  const columns = [
    { header: 'PRODUCT' },
    { header: 'CATEGORY' },
    { header: 'PRICE' },
    { header: 'STATUS' },
    { header: 'ACTIONS' },
  ];

  return (
    <div className="owner-menu-page">
      <div className="owner-table-header-row" style={{ padding: '0 0 1.5rem 0', borderBottom: 'none' }}>
        <div>
          <h2 className="owner-table-title">Catalogue Overview</h2>
          <p className="owner-table-subtitle">Manage your cafe items, pricing, and daily availability.</p>
        </div>
        <div className="owner-table-actions">
          <select 
            className="owner-chart-filter" 
            value={categoryFilter} 
            onChange={e => setCategoryFilter(e.target.value)}
          >
            {categories.map(c => <option key={c} value={c}>{c}</option>)}
          </select>
          <button className="btn btn-primary" onClick={() => setIsAddModalOpen(true)}>
            <Plus size={18} /> Add New Menu
          </button>
        </div>
      </div>

      <OwnerTable 
        columns={columns}
        data={filteredMenus}
        isLoading={isLoading}
        emptyMessage="Belum ada menu tersedia."
        renderRow={(menu) => {
          const imgUrl = menu.image ? (menu.image.startsWith('http') ? menu.image : `http://127.0.0.1:8080/storage/${menu.image}`) : menuPlaceholder;
          return (
            <tr key={menu.id}>
              <td>
                <div className="menu-product-col">
                  <img src={imgUrl} alt={menu.menuName} className="menu-img-preview" onError={(e) => e.target.src = menuPlaceholder} />
                  <div className="menu-product-info">
                    <span className="menu-product-name">{menu.menuName}</span>
                    <span className="menu-product-desc">{menu.description || '-'}</span>
                  </div>
                </div>
              </td>
              <td>{menu.category || 'Uncategorized'}</td>
              <td>{formatCurrency(menu.price)}</td>
              <td><StatusBadge status={menu.status || 'Available'} /></td>
              <td>
                <div className="owner-table-actions" style={{ gap: '0.5rem' }}>
                  <button className="owner-icon-btn" onClick={handleEditClick}><Edit2 size={16} /></button>
                  <button className="owner-icon-btn" onClick={() => handleDelete(menu.id)}><Trash2 size={16} /></button>
                </div>
              </td>
            </tr>
          );
        }}
      />

      <div className="dashboard-kpi-grid" style={{ marginTop: '2rem', gridTemplateColumns: 'repeat(3, 1fr)' }}>
        <MetricCard 
          title="Most Popular" 
          value={isLoading ? '...' : metrics.popular_name} 
          subtitle={isLoading ? '' : `${metrics.popular_sold} orders`}
          tone="warning" // Makes it yellow-ish like the screenshot
        />
        <MetricCard 
          title="Stock Alerts" 
          value={isLoading ? '...' : `${metrics.stock_alerts} Items`} 
          subtitle="Require immediate restocking"
          tone="default"
        />
        <MetricCard 
          title="Total Catalog" 
          value={isLoading ? '...' : `${metrics.total_catalog} Items`} 
          subtitle="Across all categories"
          tone="default"
        />
      </div>

      <Modal 
        isOpen={isAddModalOpen} 
        onClose={() => setIsAddModalOpen(false)} 
        title="Add New Menu"
      >
        <form onSubmit={handleAddSubmit} style={{ display: 'flex', flexDirection: 'column', gap: '1rem' }}>
          <div>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Menu Name</label>
            <input 
              type="text" 
              className="owner-search-input" 
              style={{ paddingLeft: '1rem' }}
              value={formData.menuName}
              onChange={e => setFormData({...formData, menuName: e.target.value})}
              required
            />
          </div>
          <div style={{ display: 'flex', gap: '1rem' }}>
            <div style={{ flex: 1 }}>
              <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Price</label>
              <input 
                type="number" 
                className="owner-search-input" 
                style={{ paddingLeft: '1rem' }}
                value={formData.price}
                onChange={e => setFormData({...formData, price: e.target.value})}
                required
              />
            </div>
            <div style={{ flex: 1 }}>
              <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Category</label>
              <input 
                type="text" 
                className="owner-search-input" 
                style={{ paddingLeft: '1rem' }}
                value={formData.category}
                onChange={e => setFormData({...formData, category: e.target.value})}
              />
            </div>
          </div>
          <div>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Description</label>
            <textarea 
              className="owner-search-input" 
              style={{ paddingLeft: '1rem', height: '80px', borderRadius: 'var(--radius-md)' }}
              value={formData.description}
              onChange={e => setFormData({...formData, description: e.target.value})}
            ></textarea>
          </div>
          <div>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Image</label>
            <input 
              type="file" 
              accept="image/*"
              onChange={e => setFormData({...formData, image: e.target.files[0]})}
            />
          </div>
          <button type="submit" className="btn btn-primary" style={{ marginTop: '1rem' }} disabled={addMutation.isPending}>
            {addMutation.isPending ? 'Saving...' : 'Save Menu'}
          </button>
        </form>
      </Modal>
    </div>
  );
}
