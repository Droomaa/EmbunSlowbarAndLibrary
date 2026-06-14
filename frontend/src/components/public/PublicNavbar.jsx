import { Link, useLocation } from 'react-router-dom';
import { ShoppingBag } from 'lucide-react';
import useCartStore from '../../stores/cartStore';
import './PublicNavbar.css';

export default function PublicNavbar() {
  const location = useLocation();
  const totalItems = useCartStore((s) => s.getTotalItems());

  const navItems = [
    { label: 'Home', path: '/' },
    { label: 'Menu', path: '/online-order' },
    { label: 'Online Order', path: '/online-order' },
    { label: 'Reservation', path: '/reservation' },
  ];

  return (
    <nav className="navbar" id="public-navbar">
      <div className="navbar-inner container">
        <Link to="/" className="navbar-logo">Embun Cafe</Link>

        <ul className="navbar-links">
          {navItems.map((item) => (
            <li key={item.label}>
              <Link
                to={item.path}
                className={`navbar-link ${location.pathname === item.path ? 'active' : ''}`}
              >
                {item.label}
              </Link>
            </li>
          ))}
        </ul>

        <div className="navbar-actions">
          <Link to="/online-order" className="navbar-cart" aria-label={`Keranjang belanja, ${totalItems} item`}>
            <ShoppingBag size={22} />
            {totalItems > 0 && <span className="cart-badge">{totalItems}</span>}
          </Link>
          <Link to="/online-order" className="btn btn-primary navbar-cta">Order Now</Link>
        </div>
      </div>
    </nav>
  );
}
