import { h } from 'preact';
import { useState, useEffect } from 'preact/hooks';
import { ServerInfo, AppUser } from '../services/api';
import { Sun, Moon, LogIn, LogOut, Server, ShieldCheck, Settings, UserCheck } from 'lucide-preact';

interface NavbarProps {
  appTitle: string;
  servers: ServerInfo[];
  activeServer: string | null;
  user: AppUser | null;
  theme: 'dark' | 'light';
  isConfigView: boolean;
  onThemeToggle: () => void;
  onSwitchServer: (name: string) => void;
  onOpenConfig: () => void;
  onLogin: (server: string, password: string, level: string) => Promise<void>;
  onLogout: () => void;
}

export function Navbar({
  appTitle,
  servers,
  activeServer,
  user,
  theme,
  isConfigView,
  onThemeToggle,
  onSwitchServer,
  onOpenConfig,
  onLogin,
  onLogout,
}: NavbarProps) {
  const isAuthenticated = !!user;

  // Unauthenticated navbar login state
  const [navServer, setNavServer] = useState(activeServer || servers[0]?.name || '');
  const [navLevel, setNavLevel] = useState('SuperAdmin');
  const [navPassword, setNavPassword] = useState('');
  const [isLoggingIn, setIsLoggingIn] = useState(false);

  useEffect(() => {
    if (activeServer) {
      setNavServer(activeServer);
    } else if (servers.length > 0 && !navServer) {
      setNavServer(servers[0].name);
    }
  }, [activeServer, servers]);

  const handleNavLoginSubmit = async (e: h.JSX.TargetedEvent) => {
    e.preventDefault();
    if (!navPassword.trim() || isLoggingIn) return;

    setIsLoggingIn(true);
    try {
      await onLogin(navServer, navPassword, navLevel);
      setNavPassword('');
    } finally {
      setIsLoggingIn(false);
    }
  };

  return (
    <header className="navbar bg-base-100 shadow-sm border-b border-base-300 px-4 z-30 sticky top-0 flex flex-row items-center justify-between min-h-16 gap-4">
      {/* Brand & Logo */}
      <div className="flex flex-row items-center gap-3">
        <div className="w-9 h-9 rounded-lg bg-primary text-primary-content flex items-center justify-center font-bold text-base shadow-sm flex-shrink-0">
          AS
        </div>
        <div>
          <h1 className="font-black text-lg leading-tight tracking-tight">{appTitle}</h1>
          <p className="text-2xs text-base-content/60 font-medium">Server Administration</p>
        </div>

        {/* Server Selector in BackOffice (when logged in with multiple servers) */}
        {isAuthenticated && servers.length > 0 && (
          <div className="dropdown dropdown-bottom ml-4 hidden sm:block">
            <label tabIndex={0} className="btn btn-xs sm:btn-sm btn-outline gap-1.5 font-medium">
              <Server className="w-3.5 h-3.5 text-primary" />
              <span>{activeServer || 'Select Server'}</span>
            </label>
            <ul tabIndex={0} className="dropdown-content z-20 menu p-1.5 shadow-lg bg-base-100 rounded-box w-52 border border-base-300 mt-1">
              <li className="menu-title text-2xs uppercase font-semibold">Active Server</li>
              {servers.map((s) => (
                <li key={s.name}>
                  <button
                    className={activeServer === s.name ? 'active font-semibold' : ''}
                    onClick={() => onSwitchServer(s.name)}
                  >
                    {s.name}
                  </button>
                </li>
              ))}
            </ul>
          </div>
        )}
      </div>

      {/* Right Controls Area */}
      <div className="flex flex-row items-center gap-2">
        {/* Navbar Login Form (when unauthenticated and servers exist) */}
        {!isAuthenticated && servers.length > 0 && (
          <form onSubmit={handleNavLoginSubmit} className="flex flex-row items-center gap-2">
            {/* Server choice dropdown */}
            <select
              className="select select-bordered select-sm font-medium max-w-[130px] sm:max-w-[160px] text-xs"
              value={navServer}
              onChange={(e) => {
                const val = (e.target as HTMLSelectElement).value;
                setNavServer(val);
                onSwitchServer(val);
              }}
            >
              {servers.map((s) => (
                <option key={s.name} value={s.name}>
                  {s.name}
                </option>
              ))}
            </select>

            {/* Role / User Level dropdown */}
            <select
              className="select select-bordered select-sm font-medium max-w-[120px] text-xs"
              value={navLevel}
              onChange={(e) => setNavLevel((e.target as HTMLSelectElement).value)}
            >
              <option value="SuperAdmin">SuperAdmin</option>
              <option value="Admin">Admin</option>
              <option value="User">User</option>
            </select>

            {/* Password input */}
            <input
              type="password"
              className="input input-bordered input-sm w-24 sm:w-36 text-xs font-mono"
              placeholder="Password..."
              value={navPassword}
              onInput={(e) => setNavPassword((e.target as HTMLInputElement).value)}
            />

            <button
              type="submit"
              className="btn btn-primary btn-sm gap-1 shadow-sm font-semibold"
              disabled={isLoggingIn || !navPassword.trim()}
            >
              <LogIn className="w-3.5 h-3.5" />
              <span className="hidden sm:inline">{isLoggingIn ? '...' : 'Sign In'}</span>
            </button>
          </form>
        )}

        <div className="divider divider-horizontal my-2 py-0 mx-0.5"></div>

        {/* Settings / Configuration Shortcut Button */}
        <button
          className={`btn btn-ghost btn-sm btn-circle ${isConfigView ? 'btn-active text-primary' : 'text-base-content/70'}`}
          onClick={onOpenConfig}
          title="Server & System Configuration"
        >
          <Settings className="w-4 h-4" />
        </button>

        {/* Light / Dark Mode Toggle */}
        <label className="swap swap-rotate btn btn-ghost btn-circle btn-sm" title="Toggle Theme">
          <input
            type="checkbox"
            checked={theme === 'dark'}
            onChange={onThemeToggle}
          />
          <Sun className="swap-off w-4 h-4 text-warning" />
          <Moon className="swap-on w-4 h-4 text-primary" />
        </label>

        {/* Logged in User Badge & Logout */}
        {isAuthenticated && (
          <div className="flex flex-row items-center gap-2 ml-1">
            <div className="badge badge-primary badge-outline gap-1 font-semibold text-xs py-2 px-2.5">
              <ShieldCheck className="w-3.5 h-3.5" />
              {user.level}
            </div>
            <button
              className="btn btn-ghost btn-sm btn-circle text-error"
              onClick={onLogout}
              title="Sign out"
            >
              <LogOut className="w-4 h-4" />
            </button>
          </div>
        )}
      </div>
    </header>
  );
}
