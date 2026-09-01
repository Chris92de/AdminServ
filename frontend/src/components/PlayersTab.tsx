import { h } from 'preact';
import { useState, useEffect } from 'preact/hooks';
import { Player, api } from '../services/api';
import { TmText } from './TmText';
import {
  Users,
  Eye,
  UserX,
  ShieldAlert,
  UserCheck,
  RefreshCw,
  Search,
} from 'lucide-preact';

interface PlayersTabProps {
  onShowToast: (msg: string, type?: 'success' | 'error' | 'info') => void;
}

export function PlayersTab({ onShowToast }: PlayersTabProps) {
  const [players, setPlayers] = useState<Player[]>([]);
  const [isLoading, setIsLoading] = useState(false);
  const [filterText, setFilterText] = useState('');
  const [subTab, setSubTab] = useState<'active' | 'bans'>('active');
  const [bansData, setBansData] = useState<{ bans: any[]; blacklisted: any[]; guests: any[] }>({
    bans: [],
    blacklisted: [],
    guests: [],
  });

  // Action Modal state
  const [modalType, setModalType] = useState<'kick' | 'ban' | null>(null);
  const [targetLogin, setTargetLogin] = useState('');
  const [actionReason, setActionReason] = useState('');

  const fetchPlayers = async () => {
    setIsLoading(true);
    try {
      const res = await api.getPlayers();
      if (res.success) {
        setPlayers(res.players);
      } else {
        onShowToast(res.error || 'Failed to fetch players', 'error');
      }
    } catch (err: any) {
      onShowToast(err.message, 'error');
    } finally {
      setIsLoading(false);
    }
  };

  const fetchBans = async () => {
    try {
      const res = await api.getBans();
      if (res.success) {
        setBansData(res);
      }
    } catch {
      // ignore
    }
  };

  useEffect(() => {
    fetchPlayers();
    fetchBans();
  }, []);

  const openActionModal = (type: 'kick' | 'ban', login: string) => {
    setModalType(type);
    setTargetLogin(login);
    setActionReason(type === 'kick' ? 'Kicked by administrator' : 'Banned by administrator');
  };

  const handleExecuteModalAction = async () => {
    if (!modalType || !targetLogin) return;

    try {
      let res;
      if (modalType === 'kick') {
        res = await api.kickPlayer(targetLogin, actionReason);
      } else {
        res = await api.banPlayer(targetLogin, actionReason);
      }

      if (res.success) {
        onShowToast(`Player ${targetLogin} ${modalType}ed successfully`, 'success');
        setModalType(null);
        fetchPlayers();
        fetchBans();
      } else {
        onShowToast(res.error || `Failed to ${modalType} player`, 'error');
      }
    } catch (err: any) {
      onShowToast(err.message, 'error');
    }
  };

  const handleForceSpec = async (login: string) => {
    try {
      const res = await api.forceSpectator(login);
      if (res.success) {
        onShowToast(`Moved ${login} to spectator`, 'success');
        fetchPlayers();
      } else {
        onShowToast(res.error || 'Failed action', 'error');
      }
    } catch (err: any) {
      onShowToast(err.message, 'error');
    }
  };

  const handleForcePlayer = async (login: string) => {
    try {
      const res = await api.forcePlayer(login);
      if (res.success) {
        onShowToast(`Moved ${login} to active player`, 'success');
        fetchPlayers();
      } else {
        onShowToast(res.error || 'Failed action', 'error');
      }
    } catch (err: any) {
      onShowToast(err.message, 'error');
    }
  };

  const handleUnban = async (login: string) => {
    try {
      const res = await api.unbanPlayer(login);
      if (res.success) {
        onShowToast(`Unbanned ${login}`, 'success');
        fetchBans();
      } else {
        onShowToast(res.error || 'Failed to unban', 'error');
      }
    } catch (err: any) {
      onShowToast(err.message, 'error');
    }
  };

  const filteredPlayers = players.filter(
    (p) =>
      p.login.toLowerCase().includes(filterText.toLowerCase()) ||
      p.nickname_clean.toLowerCase().includes(filterText.toLowerCase())
  );

  return (
    <div className="flex flex-col gap-6">
      {/* Sub-tab switcher */}
      <div className="flex items-center justify-between">
        <div role="tablist" className="tabs tabs-box">
          <button
            role="tab"
            className={`tab ${subTab === 'active' ? 'tab-active font-semibold' : ''}`}
            onClick={() => setSubTab('active')}
          >
            <Users className="w-4 h-4 mr-2" />
            Active Players ({players.length})
          </button>
          <button
            role="tab"
            className={`tab ${subTab === 'bans' ? 'tab-active font-semibold' : ''}`}
            onClick={() => setSubTab('bans')}
          >
            <ShieldAlert className="w-4 h-4 mr-2" />
            Bans & Blacklist ({bansData.bans.length + bansData.blacklisted.length})
          </button>
        </div>

        <button
          className="btn btn-sm btn-ghost gap-1"
          onClick={() => {
            fetchPlayers();
            fetchBans();
          }}
          disabled={isLoading}
        >
          <RefreshCw className={`w-4 h-4 ${isLoading ? 'animate-spin' : ''}`} />
          Refresh
        </button>
      </div>

      {subTab === 'active' ? (
        <div className="card bg-base-100 shadow-sm border border-base-300">
          <div className="card-body p-6">
            {/* Search filter bar */}
            <div className="flex items-center gap-2 mb-4 max-w-sm">
              <div className="relative w-full">
                <Search className="w-4 h-4 absolute left-3 top-2.5 text-base-content/40" />
                <input
                  type="text"
                  placeholder="Filter players by name or login..."
                  className="input input-bordered input-sm w-full pl-9"
                  value={filterText}
                  onInput={(e) => setFilterText((e.target as HTMLInputElement).value)}
                />
              </div>
            </div>

            {/* Players Table */}
            <div className="overflow-x-auto">
              <table className="table table-zebra table-sm">
                <thead>
                  <tr className="border-b border-base-300">
                    <th>Status</th>
                    <th>Nickname</th>
                    <th>Login</th>
                    <th>Ladder Rank</th>
                    <th>Team</th>
                    <th className="text-right">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {filteredPlayers.length === 0 ? (
                    <tr>
                      <td colSpan={6} className="text-center py-8 text-base-content/50 italic text-sm">
                        {players.length === 0 ? 'No players currently on server.' : 'No players match your search filter.'}
                      </td>
                    </tr>
                  ) : (
                    filteredPlayers.map((p) => (
                      <tr key={p.login}>
                        <td>
                          {p.is_spectator ? (
                            <span className="badge badge-secondary badge-sm gap-1">
                              <Eye className="w-3 h-3" /> Spec
                            </span>
                          ) : (
                            <span className="badge badge-success badge-sm gap-1">
                              <Users className="w-3 h-3" /> Player
                            </span>
                          )}
                        </td>
                        <td className="font-semibold">
                          <TmText html={p.nickname_html} fallback={p.nickname_clean} />
                        </td>
                        <td className="font-mono text-xs text-base-content/70">{p.login}</td>
                        <td className="font-mono text-xs">{p.ladder_ranking > 0 ? `#${p.ladder_ranking}` : '-'}</td>
                        <td>{p.team_id >= 0 ? `Team ${p.team_id + 1}` : 'Free'}</td>
                        <td className="text-right">
                          <div className="flex items-center justify-end gap-1">
                            {p.is_spectator ? (
                              <button
                                className="btn btn-ghost btn-xs text-success"
                                title="Force Player"
                                onClick={() => handleForcePlayer(p.login)}
                              >
                                <UserCheck className="w-3.5 h-3.5" />
                              </button>
                            ) : (
                              <button
                                className="btn btn-ghost btn-xs text-secondary"
                                title="Force Spectator"
                                onClick={() => handleForceSpec(p.login)}
                              >
                                <Eye className="w-3.5 h-3.5" />
                              </button>
                            )}
                            <button
                              className="btn btn-ghost btn-xs text-warning"
                              title="Kick Player"
                              onClick={() => openActionModal('kick', p.login)}
                            >
                              <UserX className="w-3.5 h-3.5" />
                            </button>
                            <button
                              className="btn btn-ghost btn-xs text-error"
                              title="Ban Player"
                              onClick={() => openActionModal('ban', p.login)}
                            >
                              <ShieldAlert className="w-3.5 h-3.5" />
                            </button>
                          </div>
                        </td>
                      </tr>
                    ))
                  )}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      ) : (
        /* Bans / Blacklist Table */
        <div className="card bg-base-100 shadow-sm border border-base-300">
          <div className="card-body p-6">
            <h3 className="font-bold text-base mb-4">Banned Players</h3>
            <div className="overflow-x-auto">
              <table className="table table-zebra table-sm">
                <thead>
                  <tr className="border-b border-base-300">
                    <th>Login</th>
                    <th>IP / Client</th>
                    <th className="text-right">Action</th>
                  </tr>
                </thead>
                <tbody>
                  {bansData.bans.length === 0 ? (
                    <tr>
                      <td colSpan={3} className="text-center py-6 text-base-content/50 italic text-sm">
                        No active server bans.
                      </td>
                    </tr>
                  ) : (
                    bansData.bans.map((b: any, idx: number) => (
                      <tr key={idx}>
                        <td className="font-mono text-xs">{b.Login || b.login || b}</td>
                        <td className="font-mono text-xs">{b.IPAddress || '-'}</td>
                        <td className="text-right">
                          <button
                            className="btn btn-outline btn-xs btn-error"
                            onClick={() => handleUnban(b.Login || b.login || b)}
                          >
                            Unban
                          </button>
                        </td>
                      </tr>
                    ))
                  )}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}

      {/* Action Modal (Kick / Ban) */}
      {modalType && (
        <div className="modal modal-open">
          <div className="modal-box">
            <h3 className="font-bold text-lg">
              {modalType === 'kick' ? 'Kick Player' : 'Ban Player'}
            </h3>
            <p className="py-2 text-sm text-base-content/70">
              Are you sure you want to {modalType} player <span className="font-mono font-bold text-base-content">{targetLogin}</span>?
            </p>
            <div className="form-control mt-2">
              <label className="label">
                <span className="label-text text-xs">Reason message</span>
              </label>
              <input
                type="text"
                className="input input-bordered input-sm"
                value={actionReason}
                onInput={(e) => setActionReason((e.target as HTMLInputElement).value)}
              />
            </div>
            <div className="modal-action">
              <button className="btn btn-sm btn-ghost" onClick={() => setModalType(null)}>
                Cancel
              </button>
              <button
                className={`btn btn-sm ${modalType === 'kick' ? 'btn-warning' : 'btn-error'}`}
                onClick={handleExecuteModalAction}
              >
                Confirm {modalType === 'kick' ? 'Kick' : 'Ban'}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
