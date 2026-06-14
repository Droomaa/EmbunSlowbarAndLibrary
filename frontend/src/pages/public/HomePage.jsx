import { useQuery } from '@tanstack/react-query';
import { Link } from 'react-router-dom';
import { ArrowRight, BookOpen, Users, MapPin, Clock, Phone, Mail } from 'lucide-react';
import { fetchMenus } from '../../services/menuService';
import { formatCurrency } from '../../utils/formatCurrency';
import Skeleton from '../../components/ui/Skeleton';
import heroBg from '../../assets/images/hero-cafe.png';
import sanctuaryImg from '../../assets/images/sanctuary.png';
import menuPlaceholder from '../../assets/images/menu-placeholder.png';
import './HomePage.css';

export default function HomePage() {
  const { data: menus, isLoading, isError } = useQuery({
    queryKey: ['menus'],
    queryFn: fetchMenus,
  });

  const featuredMenus = Array.isArray(menus) ? menus.slice(0, 4) : [];

  return (
    <div className="home-page">
      {/* ===== HERO ===== */}
      <section className="hero" style={{ backgroundImage: `url(${heroBg})` }}>
        <div className="hero-overlay">
          <div className="container hero-content">
            <span className="badge badge-tertiary">NOW OPEN IN DOWNTOWN</span>
            <h1 className="hero-heading">
              Savor the <span className="text-primary">Morning Mist</span> in Every Sip.
            </h1>
            <p className="hero-sub">
              Embun Cafe brings you an artisanal coffee experience rooted in sustainable
              farming and cozy, earthy atmospheres.
            </p>
            <div className="hero-cta">
              <Link to="/online-order" className="btn btn-primary btn-lg">
                Order Online <ArrowRight size={18} />
              </Link>
              <Link to="/reservation" className="btn btn-outline btn-lg">Book a Table</Link>
            </div>
          </div>
        </div>
      </section>

      {/* ===== FEATURED MENU ===== */}
      <section className="section featured-section">
        <div className="container">
          <p className="section-label">CURATED FAVORITES</p>
          <h2 className="section-heading">From Our Kitchen</h2>
          <p className="section-sub">Discover the seasonal delights and signature brews that define the Embun experience.</p>

          {isLoading && (
            <div className="featured-grid">
              {[1, 2, 3, 4].map((i) => (
                <div key={i} className={`featured-card ${i <= 2 ? 'featured-large' : 'featured-small'}`}>
                  <Skeleton width="100%" height={i <= 2 ? '320px' : '200px'} />
                </div>
              ))}
            </div>
          )}

          {isError && (
            <div style={{ textAlign: 'center', padding: '3rem', color: 'var(--color-text-muted)' }}>
              Menu gagal dimuat. Silakan coba lagi.
            </div>
          )}

          {!isLoading && !isError && featuredMenus.length === 0 && (
            <div style={{ textAlign: 'center', padding: '3rem', color: 'var(--color-text-muted)' }}>
              Menu belum tersedia.
            </div>
          )}

          {!isLoading && !isError && featuredMenus.length > 0 && (
            <div className="featured-grid">
              {featuredMenus.map((menu, i) => (
                <div key={menu.id} className={`featured-card ${i < 2 ? 'featured-large' : 'featured-small'}`}>
                  <img
                    src={menu.image_url || menuPlaceholder}
                    alt={menu.menuName}
                    className="featured-img"
                    loading="lazy"
                    onError={(e) => { e.target.src = menuPlaceholder; }}
                  />
                  <div className="featured-info">
                    <h3>{menu.menuName}</h3>
                    <p>{menu.description || 'Freshly prepared by Embun Cafe.'}</p>
                    <span className="featured-price">{formatCurrency(menu.price)}</span>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </section>

      {/* ===== SANCTUARY ===== */}
      <section className="section sanctuary-section">
        <div className="container sanctuary-grid">
          <div className="sanctuary-img-wrap">
            <img src={sanctuaryImg} alt="Interior Embun Cafe" loading="lazy" />
          </div>
          <div className="sanctuary-content">
            <h2>Find Your Sanctuary.</h2>
            <p className="sanctuary-desc">
              Whether you&rsquo;re looking for a quiet morning with a book or a vibrant brunch with
              friends, our space is designed for comfort and connection. Reserve your favorite corner today.
            </p>
            <div className="sanctuary-features">
              <div className="feature-card">
                <BookOpen size={24} className="feature-icon" />
                <div>
                  <strong>Private Booths</strong>
                  <p>Perfect for focused work or quiet talks.</p>
                </div>
              </div>
              <div className="feature-card">
                <Users size={24} className="feature-icon" />
                <div>
                  <strong>Community Tables</strong>
                  <p>Large wooden tables for gathering the whole team.</p>
                </div>
              </div>
            </div>
            <Link to="/reservation" className="btn btn-primary btn-lg">Book Reservation</Link>
          </div>
        </div>
      </section>

      {/* ===== VISIT US ===== */}
      <section className="section visit-section">
        <div className="container visit-grid">
          <div className="visit-info">
            <h2>Visit Us</h2>
            <div className="visit-detail">
              <MapPin size={18} />
              <div>
                <strong>Embun Slowbar &amp; Library</strong>
                <p>
                  <a href="https://maps.app.goo.gl/A1KVKmMfiVXLtzeJ6" target="_blank" rel="noopener noreferrer" style={{ color: 'var(--color-primary)', textDecoration: 'underline' }}>
                    Buka di Google Maps
                  </a>
                </p>
              </div>
            </div>
            <div className="visit-detail">
              <Clock size={18} />
              <div>
                <p>Setiap Hari: 7:00 AM – 11:30 PM</p>
              </div>
            </div>
            <div className="visit-detail">
              <Phone size={18} />
              <div><p>0895-3399-31433</p></div>
            </div>
            <div className="visit-detail">
              <Mail size={18} />
              <div><p>hello@embuncafe.com</p></div>
            </div>
          </div>
          <div className="visit-map" style={{ minHeight: '300px' }}>
            <iframe 
              src="https://maps.google.com/maps?q=Embun%20Slowbar%20%26%20Library,%20Malang&t=&z=15&ie=UTF8&iwloc=&output=embed" 
              width="100%" 
              height="100%" 
              style={{ border: 0, borderRadius: 'var(--radius-lg)' }} 
              allowFullScreen="" 
              loading="lazy" 
              referrerPolicy="no-referrer-when-downgrade">
            </iframe>
          </div>
        </div>
      </section>
    </div>
  );
}
