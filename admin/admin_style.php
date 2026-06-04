<style>
body { font-family: 'Segoe UI', sans-serif; background: #f4f6fb; }
.sidebar {
  width: 250px; min-height: 100vh;
  background: linear-gradient(180deg, #1a3c6e 0%, #0f2447 100%);
  position: fixed; top: 0; left: 0; z-index: 100;
}
.sidebar-brand {
  padding: 20px 20px 16px;
  border-bottom: 1px solid rgba(255,255,255,0.1);
  text-align: center;
}
.sidebar-brand img { height: 32px; margin: 0 4px; }
.sidebar-brand p { color: rgba(255,255,255,0.6); font-size: 0.72rem; margin: 6px 0 0; }
.sidebar-nav { padding: 16px 12px; }
.sidebar-label {
  color: rgba(255,255,255,0.4); font-size: 0.68rem;
  font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
  padding: 8px 12px 4px;
}
.nav-link-admin {
  display: flex; align-items: center; gap: 10px;
  color: rgba(255,255,255,0.75); text-decoration: none;
  padding: 9px 12px; border-radius: 8px;
  font-size: 0.88rem; margin-bottom: 2px;
  transition: all 0.2s;
}
.nav-link-admin:hover, .nav-link-admin.active {
  background: rgba(255,255,255,0.12); color: white;
}
.nav-link-admin i { width: 18px; text-align: center; font-size: 1rem; }
.badge-baru {
  font-size: 0.68rem; background: #dc3545; color: white;
  padding: 1px 7px; border-radius: 20px; margin-left: auto;
}
.main-content { margin-left: 250px; padding: 28px; }
.topbar {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 24px;
}
.topbar h4 { font-weight: 700; color: #1a3c6e; margin: 0; }
.table-card {
  background: white; border-radius: 16px;
  padding: 24px; border: 1px solid #e8eef8;
  box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}
.form-card {
  background: white; border-radius: 16px;
  padding: 32px; border: 1px solid #e8eef8;
  box-shadow: 0 2px 12px rgba(0,0,0,0.04);
  max-width: 700px;
}
.gambar-preview {
  max-height: 140px; border-radius: 10px;
  border: 1px solid #eee; margin-top: 8px;
  display: block;
}
@media (max-width: 768px) { .sidebar { display: none; } .main-content { margin-left: 0; } }
</style>
