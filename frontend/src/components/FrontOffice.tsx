import { h } from 'preact';
import { useState, useEffect } from 'preact/hooks';
import { ServerInfo, ServerOverviewItem, api } from '../services/api';
import { TmText } from './TmText';
import { Server, Users, Trophy, Settings, AlertCircle } from 'lucide-preact';

interface FrontOfficeProps {
  appTitle: string;
  servers: ServerInfo[];
  activeServer: string | null;
  onSwitchServer: (name: string) => void;
  onOpenConfig: () => void;
  onShowToast: (msg: string, type?: 'success' | 'error' | 'info') => void;
}

export function FrontOffice({
  appTitle,
  servers,
  activeServer,
  onSwitchServer,
  onOpenConfig,
  onShowToast,
}: FrontOfficeProps) {
  const [overview, setOverview] = useState<ServerOverviewItem[]>([]);

  const fetchOverview = async () => {
    try {
      const res = await api.getOverview();
      if (res.success && res.overview) {
        setOverview(res.overview);
      }
    } catch {
      // ignore
    }
  };

  useEffect(() => {
    fetchOverview();
    const interval = setInterval(fetchOverview, 5000);
    return () => clearInterval(interval);
  }, [servers]);

  return (
    <div className="flex-1 flex flex-col items-center justify-center py-12 px-4">
      <div className="max-w-md w-full flex flex-col gap-6">
        {/* Header Branding */}
        <div className="text-center">
          <div className="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary text-primary-content font-black text-2xl shadow-lg mb-3">
            AS
          </div>
          <h2 className="text-2xl font-black tracking-tight">{appTitle}</h2>
          <p className="text-xs text-base-content/60 mt-0.5">TrackMania Dedicated Server Web Administration</p>
        </div>

        {/* Empty Servers Alert (Fresh Installation) */}
        {servers.length === 0 ? (
          <div className="alert alert-warning shadow-md flex flex-col items-start gap-3 p-5">
            <div className="flex items-center gap-2 font-bold text-sm">
              <AlertCircle className="w-5 h-5 flex-shrink-0" />
              <span>Initial Setup Required</span>
            </div>
            <p className="text-xs">
              No dedicated servers are currently configured. Unlock the Master Configuration to add your first server profile.
            </p>
            <button className="btn btn-sm btn-primary w-full gap-1.5 shadow-sm mt-1" onClick={onOpenConfig}>
              <Settings className="w-4 h-4" /> Open Server Configuration
            </button>
          </div>
        ) : (
          /* Server Preview Cards List (Original Style) */
          <div className="flex flex-col gap-3">
            {overview.map((srv) => (
              <div key={srv.name} className="card bg-base-100 shadow-sm border border-base-300">
                <div className="card-body p-4 flex flex-col gap-2.5">
                  <div className="flex items-center justify-between">
                    <div className="flex items-center gap-2 font-bold text-sm">
                      <Server className="w-4 h-4 text-primary" />
                      <TmText html={srv.server_title} fallback={srv.server_name} />
                    </div>
                    <span
                      className={`badge badge-xs gap-1 ${
                        srv.connected ? 'badge-success text-success-content' : 'badge-error text-error-content'
                      }`}
                    >
                      {srv.connected ? 'Online' : 'Offline'}
                    </span>
                  </div>

                  {srv.connected ? (
                    <div className="grid grid-cols-2 gap-2 text-xs bg-base-200/60 p-2.5 rounded-lg border border-base-300">
                      <div className="flex items-center gap-1.5 truncate">
                        <Trophy className="w-3.5 h-3.5 text-primary flex-shrink-0" />
                        <span className="truncate">
                          <TmText
                            html={srv.current_map?.name_html}
                            fallback={srv.current_map?.name_clean || 'No Map'}
                          />
                        </span>
                      </div>
                      <div className="flex items-center gap-1.5 justify-end">
                        <Users className="w-3.5 h-3.5 text-secondary flex-shrink-0" />
                        <span>
                          {srv.players_count} / {srv.max_players} Players
                        </span>
                      </div>
                    </div>
                  ) : (
                    <div className="text-2xs text-error/80 bg-error/10 p-2 rounded-lg font-mono truncate">
                      {srv.error || 'Server offline or unreachable'}
                    </div>
                  )}
                </div>
              </div>
            ))}
          </div>
        )}

        <div className="flex items-center justify-between text-xs text-base-content/50 px-1">
          <span>AdminServ 4.0</span>
          <button
            className="hover:text-primary transition-colors flex items-center gap-1 font-medium text-xs"
            onClick={onOpenConfig}
          >
            <Settings className="w-3.5 h-3.5" /> Setup / Server Configuration
          </button>
        </div>
      </div>
    </div>
  );
}
