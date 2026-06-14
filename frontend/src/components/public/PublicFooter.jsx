import { Link } from 'react-router-dom';
import './PublicFooter.css';

export default function PublicFooter() {
  return (
    <footer className="footer">
      <div className="container footer-inner">
        <Link to="/" className="footer-logo">Embun Cafe</Link>
        <div className="footer-links">
          <a href="#about">About Us</a>
          <a href="#contact">Contact</a>
          <a href="#privacy">Privacy Policy</a>
          <a href="#terms">Terms of Service</a>
        </div>
        <p className="footer-copy">&copy; 2024 Embun Cafe. Rooted in Flavor.</p>
      </div>
    </footer>
  );
}
