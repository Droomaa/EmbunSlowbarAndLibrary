import { Search, Bell, Menu } from 'lucide-react';
import useAuthStore from '../../stores/authStore';
import NotificationDropdown from '../ui/NotificationDropdown';
import './OwnerTopbar.css';

export default function OwnerTopbar({ 
  title, 
  searchValue, 
  onSearchChange, 
  searchPlaceholder = 'Search...',
  onToggleMobileMenu 
}) {
  const role = useAuthStore((state) => state.role);
  
  // Fake name based on role or fallback
  const displayName = role === 'Owner' ? 'Aris Setiawan' : 'User';

  return (
    <header className="owner-topbar">
      <div className="owner-topbar-left">
        <button className="mobile-menu-btn" onClick={onToggleMobileMenu}>
          <Menu size={24} />
        </button>
        <h1 className="owner-topbar-title">{title}</h1>
      </div>

      <div className="owner-topbar-right">
        {onSearchChange !== undefined && (
          <div className="owner-search-wrapper">
            <Search size={18} className="owner-search-icon" />
            <input 
              type="text"
              className="owner-search-input"
              placeholder={searchPlaceholder}
              value={searchValue}
              onChange={(e) => onSearchChange(e.target.value)}
            />
          </div>
        )}

        <div className="owner-topbar-actions">
          <NotificationDropdown />
          
          <div className="owner-profile">
            <div className="owner-avatar">
              {displayName.charAt(0)}
            </div>
            <div className="owner-profile-info">
              <span className="owner-profile-name">{displayName}</span>
              <span className="owner-profile-role">Cafe {role || 'Owner'}</span>
            </div>
          </div>
        </div>
      </div>
    </header>
  );
}
