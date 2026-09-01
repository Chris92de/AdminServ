import { h } from 'preact';
import { useState, useEffect } from 'preact/hooks';
import { Navbar } from './components/Navbar';
import { Sidebar, TabType } from './components/Sidebar';
import { DashboardTab } from './components/DashboardTab';
import { PlayersTab } from './components/PlayersTab';
import { MapsTab } from './components/MapsTab';
import { MatchSettingsTab } from './components/MatchSettingsTab';
import { GameSettingsTab } from './components/GameSettingsTab';
import { ConfigTab } from './components/ConfigTab';
import { FrontOffice } from './components/FrontOffice';
import { AuthMeResponse, DashboardStatus, api } from './services/api';
import { CheckCircle2, AlertTriangle, Info, X } from 'lucide-preact';

export function App() {
  const [activeTab, setActiveTab] = useState<TabType>('dashboard');
  const [authData, setAuthData] = useState<AuthMeResponse | null>(null);
  const [status, setStatus] = useState<DashboardStatus | null>(null);
  const [theme, setTheme] = useState<'dark' | 'light'>('dark');
  const [isConfigView, setIsConfigView] = useState(false);
  const [isInitialLoading, setIsInitialLoading] = useState(true);
  const [toast, setToast] = useState<{ msg: string; type: 'success' | 'error' | 'info' } | null>(null);

  // Initialize theme from localStorage or system preference
  useEffect(() => {
    const savedTheme = localStorage.getItem('adminserv_theme') as 'dark' | 'light' | null;
    const initialTheme = savedTheme || 'dark';
    setTheme(initialTheme);
    document.documentElement.setAttribute('data-theme', initialTheme);
  }, []);

  const handleThemeToggle = () => {
    const nextTheme = theme === 'dark' ? 'light' : 'dark';
    setTheme(nextTheme);
    localStorage.setItem('adminserv_theme', nextTheme);
    document.documentElement.setAttribute('data-theme', nextTheme);
  };

  const showToast = (msg: string, type: 'success' | 'error' | 'info' = 'info') => {
    setToast({ msg, type });
    setTimeout(() => setToast(null), 4000);
  };

  const fetchAuth = async () => {
    try {
      const res = await api.getMe();
      setAuthData(res);
      // If no servers exist, automatically open configuration setup
      if (res.servers && res.servers.length === 0) {
        setIsConfigView(true);
      }
    } catch {
      // ignore
    }
  };

  const fetchStatus = async () => {
    try {
      const res = await api.getStatus();
      setStatus(res);
    } catch {
      // ignore
    }
  };

  useEffect(() => {
    Promise.all([fetchAuth(), fetchStatus()]).finally(() => {
      setIsInitialLoading(false);
    });

    const interval = setInterval(fetchStatus, 5000);
    return () => clearInterval(interval);
  }, []);

  const handleSwitchServer = async (name: string) => {
    try {
      const res = await api.switchServer(name);
      if (res.success) {
        if (res.require_auth) {
          showToast(`Switched to ${name}. Please log in with this server's credentials.`, 'info');
        } else {
          showToast(`Active server: ${name}`, 'success');
        }
        await fetchAuth();
        await fetchStatus();
      } else {
        showToast(res.error || 'Failed to switch server', 'error');
      }
    } catch (err: any) {
      showToast(err.message, 'error');
    }
  };

  const handleLogin = async (server: string, password: string, level: string = 'SuperAdmin') => {
    try {
      const res = await api.login(server, password, level);
      if (res.success) {
        showToast(`Successfully authenticated as ${res.user?.level || level}! Welcome to AdminServ.`, 'success');
        setIsConfigView(false);
        await fetchAuth();
        await fetchStatus();
      } else {
        showToast(res.error || 'Authentication failed. Invalid password for selected role.', 'error');
      }
    } catch (err: any) {
      showToast(err.message, 'error');
    }
  };

  const handleLogout = async () => {
    try {
      await api.logout();
      showToast('Logged out', 'info');
      setActiveTab('dashboard');
      setIsConfigView(false);
      fetchAuth();
      fetchStatus();
    } catch (err: any) {
      showToast(err.message, 'error');
    }
  };

  const isAuthenticated = !!authData?.authenticated;

  return (
    <div className="min-h-screen flex flex-col bg-base-200">
      {/* Top Navigation */}
      <Navbar
        appTitle={authData?.app_info?.title || 'AdminServ'}
        servers={authData?.servers || []}
        activeServer={authData?.active_server || null}
        user={authData?.user || null}
        theme={theme}
        isConfigView={isConfigView}
        onThemeToggle={handleThemeToggle}
        onSwitchServer={handleSwitchServer}
        onOpenConfig={() => setIsConfigView(!isConfigView)}
        onLogin={handleLogin}
        onLogout={handleLogout}
      />

      {/* Main Content Area */}
      {isInitialLoading ? (
        <div className="flex-1 flex flex-col items-center justify-center py-28">
          <div className="flex flex-col items-center gap-3">
            <span className="loading loading-spinner loading-lg text-primary"></span>
            <span className="text-xs font-semibold text-base-content/60 tracking-wider uppercase">Loading AdminServ...</span>
          </div>
        </div>
      ) : isConfigView ? (
        /* Direct Master Configuration View (Accessible without game server login) */
        <div className="flex-1 flex flex-col max-w-5xl w-full mx-auto p-4 md:p-6 gap-4">
          <div className="flex items-center justify-between">
            <button
              className="btn btn-sm btn-ghost gap-1.5 text-xs font-semibold"
              onClick={() => setIsConfigView(false)}
            >
              ← Back to {isAuthenticated ? 'Dashboard' : 'Login'}
            </button>
          </div>
          <ConfigTab
            onShowToast={showToast}
            onConfigChanged={fetchAuth}
          />
        </div>
      ) : !isAuthenticated ? (
        /* FrontOffice (Unauthenticated) */
        <FrontOffice
          appTitle={authData?.app_info?.title || 'AdminServ'}
          servers={authData?.servers || []}
          activeServer={authData?.active_server || null}
          onSwitchServer={handleSwitchServer}
          onOpenConfig={() => setIsConfigView(true)}
          onShowToast={showToast}
        />
      ) : (
        /* BackOffice (Authenticated) */
        <div className="flex-1 flex max-w-[1600px] w-full mx-auto p-4 md:p-6 gap-6">
          <Sidebar
            activeTab={activeTab}
            user={authData?.user || null}
            onSelectTab={(tab) => {
              if (tab === 'config') {
                setIsConfigView(true);
              } else {
                setActiveTab(tab);
              }
            }}
          />

          <main className="flex-1 min-w-0">
            {activeTab === 'dashboard' && (
              <DashboardTab status={status} onRefresh={fetchStatus} onShowToast={showToast} />
            )}
            {activeTab === 'players' && <PlayersTab onShowToast={showToast} />}
            {activeTab === 'maps' && <MapsTab onShowToast={showToast} />}
            {activeTab === 'matchsettings' && <MatchSettingsTab onShowToast={showToast} />}
            {activeTab === 'gamesettings' && <GameSettingsTab onShowToast={showToast} />}
            {activeTab === 'config' && <ConfigTab onShowToast={showToast} onConfigChanged={fetchAuth} />}
          </main>
        </div>
      )}

      {/* Toast Notification Container */}
      {toast && (
        <div className="toast toast-bottom toast-end z-50">
          <div
            className={`alert shadow-lg py-2.5 px-4 text-xs font-medium flex items-center gap-2 ${
              toast.type === 'success'
                ? 'alert-success text-success-content'
                : toast.type === 'error'
                ? 'alert-error text-error-content'
                : 'alert-info text-info-content'
            }`}
          >
            {toast.type === 'success' && <CheckCircle2 className="w-4 h-4" />}
            {toast.type === 'error' && <AlertTriangle className="w-4 h-4" />}
            {toast.type === 'info' && <Info className="w-4 h-4" />}
            <span>{toast.msg}</span>
            <button className="btn btn-ghost btn-2xs btn-circle ml-2" onClick={() => setToast(null)}>
              <X className="w-3 h-3" />
            </button>
          </div>
        </div>
      )}
    </div>
  );
}
