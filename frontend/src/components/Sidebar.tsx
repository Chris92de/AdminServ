import { h } from 'preact';
import {
  LayoutDashboard,
  Users,
  Map,
  FileText,
  Sliders,
  Settings,
} from 'lucide-preact';

import { AppUser } from '../services/api';

export type TabType = 'dashboard' | 'players' | 'maps' | 'matchsettings' | 'gamesettings' | 'config';

interface SidebarProps {
  activeTab: TabType;
  user: AppUser | null;
  onSelectTab: (tab: TabType) => void;
}

export function Sidebar({ activeTab, user, onSelectTab }: SidebarProps) {
  const allItems: { id: TabType; label: string; icon: any; minLevel: 'User' | 'Admin' | 'SuperAdmin' }[] = [
    { id: 'dashboard', label: 'Dashboard', icon: LayoutDashboard, minLevel: 'User' },
    { id: 'players', label: 'Players & Bans', icon: Users, minLevel: 'Admin' },
    { id: 'maps', label: 'Maps & Playlists', icon: Map, minLevel: 'Admin' },
    { id: 'matchsettings', label: 'MatchSettings', icon: FileText, minLevel: 'Admin' },
    { id: 'gamesettings', label: 'Game & Server Options', icon: Sliders, minLevel: 'Admin' },
    { id: 'config', label: 'Configuration', icon: Settings, minLevel: 'SuperAdmin' },
  ];

  const userLevel = user?.level || 'User';
  const isSuper = userLevel === 'SuperAdmin';
  const isAdmin = isSuper || userLevel === 'Admin';

  const items = allItems.filter((it) => {
    if (it.minLevel === 'SuperAdmin') return isSuper;
    if (it.minLevel === 'Admin') return isAdmin;
    return true;
  });

  return (
    <aside className="w-64 bg-base-100 border-r border-base-300 flex flex-col p-4">
      <ul className="menu menu-md w-full gap-1 p-0">
        {items.map((item) => {
          const Icon = item.icon;
          const isActive = activeTab === item.id;
          return (
            <li key={item.id}>
              <button
                className={`flex items-center gap-3 rounded-lg font-medium transition-all ${isActive
                    ? 'bg-primary text-primary-content shadow-sm'
                    : 'hover:bg-base-200 text-base-content/80'
                  }`}
                onClick={() => onSelectTab(item.id)}
              >
                <Icon className={`w-5 h-5 ${isActive ? 'text-primary-content' : 'text-base-content/70'}`} />
                <span>{item.label}</span>
              </button>
            </li>
          );
        })}
      </ul>

      <div className="mt-auto pt-4 border-t border-base-300 text-xs text-base-content/50 text-center">
        AdminServ 4.0
      </div>
    </aside>
  );
}
