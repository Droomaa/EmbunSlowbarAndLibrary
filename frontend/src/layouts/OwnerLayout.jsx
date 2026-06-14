import { useState } from 'react';
import { Outlet } from 'react-router-dom';
import OwnerSidebar from '../components/owner/OwnerSidebar';
import OwnerTopbar from '../components/owner/OwnerTopbar';
import OwnerFooter from '../components/owner/OwnerFooter';
import './OwnerLayout.css';

export default function OwnerLayout() {
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

  return (
    <div className="owner-layout">
      <OwnerSidebar isOpen={isMobileMenuOpen} />
      
      <div className="owner-main-content">
        <OwnerTopbar 
          title="Embun Cafe"
          onToggleMobileMenu={() => setIsMobileMenuOpen(!isMobileMenuOpen)} 
        />
        
        <main className="owner-page-content">
          <Outlet />
        </main>
        
        <OwnerFooter />
      </div>
    </div>
  );
}
