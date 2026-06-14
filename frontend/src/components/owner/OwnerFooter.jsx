import './OwnerFooter.css';

export default function OwnerFooter() {
  return (
    <footer className="owner-footer">
      <div className="owner-footer-brand">Embun Cafe</div>
      <p>&copy; {new Date().getFullYear()} Embun Cafe. Rooted in Flavor.</p>
      
      <div className="owner-footer-links">
        <a href="#">About Us</a>
        <a href="#">Contact</a>
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
      </div>
    </footer>
  );
}
