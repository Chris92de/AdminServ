import { h } from 'preact';
import { useState, useEffect, useMemo } from 'preact/hooks';
import { api } from '../services/api';
import {
  Sliders,
  Save,
  RefreshCw,
  Lock,
  Gamepad2,
  Search,
} from 'lucide-preact';

interface GameSettingsTabProps {
  onShowToast: (msg: string, type?: 'success' | 'error' | 'info') => void;
}

export function GameSettingsTab({ onShowToast }: GameSettingsTabProps) {
  const [serverOpts, setServerOpts] = useState<any>({
    Name: '',
    Comment: '',
    Password: '',
    PasswordForSpectator: '',
    CurrentMaxPlayers: 32,
    CurrentMaxSpectators: 32,
  });

  const [gameInfos, setGameInfos] = useState<any>({
    GameMode: 1, // 0: Script, 1: Rounds, 2: TimeAttack, 3: Team, 4: Laps, 5: Cup, 6: Stunts
    ChatTime: 10000,
    FinishTimeout: 1,
    AllWarmUpDuration: 0,
    DisableRespawn: false,
    ForceShowAllOpponents: 0,
    RoundsPointsLimit: 50,
    TimeAttackLimit: 300000,
    TeamPointsLimit: 5,
    LapsNbLaps: 5,
  });

  const [scriptSettings, setScriptSettings] = useState<Record<string, any>>({});
  const [isScriptMode, setIsScriptMode] = useState(false);
  const [scriptSearch, setScriptSearch] = useState('');

  const [isLoading, setIsLoading] = useState(false);
  const [isSavingServer, setIsSavingServer] = useState(false);
  const [isSavingGame, setIsSavingGame] = useState(false);
  const [isSavingScript, setIsSavingScript] = useState(false);

  const fetchConfigs = async () => {
    setIsLoading(true);
    try {
      const [optsRes, infosRes, scriptRes] = await Promise.all([
        api.getServerOptions(),
        api.getGameInfos(),
        api.getScriptSettings(),
      ]);

      if (optsRes.success && optsRes.options) {
        setServerOpts(optsRes.options);
      }
      if (infosRes.success && infosRes.game_infos) {
        setGameInfos(infosRes.game_infos);
      }
      if (scriptRes.success && scriptRes.settings) {
        setScriptSettings(scriptRes.settings);
        setIsScriptMode(scriptRes.is_script_mode);
      }
    } catch (err: any) {
      onShowToast(err.message, 'error');
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    fetchConfigs();
  }, []);

  const handleSaveServerOpts = async (e: h.JSX.TargetedEvent) => {
    e.preventDefault();
    setIsSavingServer(true);
    try {
      const res = await api.setServerOptions(serverOpts);
      if (res.success) {
        onShowToast('Server options updated successfully', 'success');
      } else {
        onShowToast(res.error || 'Failed updating server options', 'error');
      }
    } catch (err: any) {
      onShowToast(err.message, 'error');
    } finally {
      setIsSavingServer(false);
    }
  };

  const handleSaveGameInfos = async (e: h.JSX.TargetedEvent) => {
    e.preventDefault();
    setIsSavingGame(true);
    try {
      const res = await api.setGameInfos(gameInfos);
      if (res.success) {
        onShowToast('Game mode settings updated successfully', 'success');
      } else {
        onShowToast(res.error || 'Failed updating game mode settings', 'error');
      }
    } catch (err: any) {
      onShowToast(err.message, 'error');
    } finally {
      setIsSavingGame(false);
    }
  };

  const handleSaveScriptSettings = async (e: h.JSX.TargetedEvent) => {
    e.preventDefault();
    setIsSavingScript(true);
    try {
      const res = await api.setScriptSettings(scriptSettings);
      if (res.success) {
        onShowToast('Mode script settings saved successfully!', 'success');
      } else {
        onShowToast(res.error || 'Failed saving script settings', 'error');
      }
    } catch (err: any) {
      onShowToast(err.message, 'error');
    } finally {
      setIsSavingScript(false);
    }
  };

  const gameModes = [
    { id: 1, name: 'Rounds' },
    { id: 2, name: 'Time Attack' },
    { id: 3, name: 'Team' },
    { id: 4, name: 'Laps' },
    { id: 5, name: 'Cup' },
    { id: 6, name: 'Stunts' },
    { id: 0, name: 'Script (TrackMania / ManiaPlanet)' },
  ];

  // Filter script settings by search query
  const filteredScriptSettings = useMemo(() => {
    const entries = Object.entries(scriptSettings);
    const searchLower = scriptSearch.trim().toLowerCase();
    if (!searchLower) return entries;

    return entries.filter(([key, val]) =>
      key.toLowerCase().includes(searchLower) || String(val).toLowerCase().includes(searchLower)
    );
  }, [scriptSettings, scriptSearch]);

  return (
    <div className="flex flex-col gap-6">
      {/* Page Header */}
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-xl font-extrabold tracking-tight">Server & Game Configuration</h2>
          <p className="text-xs text-base-content/60 mt-0.5">Configure dedicated server options and game rules</p>
        </div>
        <button className="btn btn-sm btn-ghost gap-1.5 border border-base-300" onClick={fetchConfigs} disabled={isLoading}>
          <RefreshCw className={`w-3.5 h-3.5 ${isLoading ? 'animate-spin' : ''}`} />
          Reload
        </button>
      </div>

      {/* Top 2-Column Cards Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
        {/* Card 1: Server Options */}
        <div className="card bg-base-100 shadow-sm border border-base-300 flex flex-col justify-between">
          <form onSubmit={handleSaveServerOpts} className="card-body p-6 flex flex-col justify-between h-full">
            <div className="space-y-4">
              <div className="flex items-center justify-between border-b border-base-300 pb-3">
                <div className="flex items-center gap-2 font-bold text-base">
                  <Lock className="w-5 h-5 text-primary" />
                  <span>Server Options</span>
                </div>
              </div>

              <div>
                <label className="block text-xs font-semibold text-base-content/80 mb-1.5">Server Name</label>
                <input
                  type="text"
                  className="input input-bordered input-sm w-full font-medium"
                  placeholder="e.g. My TrackMania Server"
                  value={serverOpts.Name || ''}
                  onInput={(e) => setServerOpts({ ...serverOpts, Name: (e.target as HTMLInputElement).value })}
                />
              </div>

              <div>
                <label className="block text-xs font-semibold text-base-content/80 mb-1.5">Comment / Message</label>
                <textarea
                  className="textarea textarea-bordered textarea-sm w-full h-20 leading-relaxed resize-y"
                  placeholder="Optional server description or message shown to players"
                  value={serverOpts.Comment || ''}
                  onInput={(e) => setServerOpts({ ...serverOpts, Comment: (e.target as HTMLTextAreaElement).value })}
                />
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                <div>
                  <label className="block text-xs font-semibold text-base-content/80 mb-1.5">Player Password</label>
                  <input
                    type="text"
                    className="input input-bordered input-sm w-full"
                    placeholder="Empty for public"
                    value={serverOpts.Password || ''}
                    onInput={(e) => setServerOpts({ ...serverOpts, Password: (e.target as HTMLInputElement).value })}
                  />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-base-content/80 mb-1.5">Spectator Password</label>
                  <input
                    type="text"
                    className="input input-bordered input-sm w-full"
                    placeholder="Empty for public"
                    value={serverOpts.PasswordForSpectator || ''}
                    onInput={(e) =>
                      setServerOpts({ ...serverOpts, PasswordForSpectator: (e.target as HTMLInputElement).value })
                    }
                  />
                </div>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                  <label className="block text-xs font-semibold text-base-content/80 mb-1.5">Max Players</label>
                  <input
                    type="number"
                    min="1"
                    max="255"
                    className="input input-bordered input-sm w-full font-mono"
                    value={serverOpts.CurrentMaxPlayers || 32}
                    onInput={(e) =>
                      setServerOpts({ ...serverOpts, CurrentMaxPlayers: parseInt((e.target as HTMLInputElement).value, 10) || 0 })
                    }
                  />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-base-content/80 mb-1.5">Max Spectators</label>
                  <input
                    type="number"
                    min="0"
                    max="255"
                    className="input input-bordered input-sm w-full font-mono"
                    value={serverOpts.CurrentMaxSpectators || 32}
                    onInput={(e) =>
                      setServerOpts({
                        ...serverOpts,
                        CurrentMaxSpectators: parseInt((e.target as HTMLInputElement).value, 10) || 0,
                      })
                    }
                  />
                </div>
              </div>
            </div>

            <div className="pt-4 mt-6 border-t border-base-300 flex justify-end">
              <button type="submit" className="btn btn-primary btn-sm gap-1.5 shadow-sm" disabled={isSavingServer}>
                <Save className="w-4 h-4" /> Save Server Options
              </button>
            </div>
          </form>
        </div>

        {/* Card 2: Game Mode & Rules */}
        <div className="card bg-base-100 shadow-sm border border-base-300 flex flex-col justify-between">
          <form onSubmit={handleSaveGameInfos} className="card-body p-6 flex flex-col justify-between h-full">
            <div className="space-y-4">
              <div className="flex items-center justify-between border-b border-base-300 pb-3">
                <div className="flex items-center gap-2 font-bold text-base">
                  <Gamepad2 className="w-5 h-5 text-secondary" />
                  <span>Game Mode & Rules</span>
                </div>
                <span className="badge badge-secondary badge-outline badge-sm text-2xs font-semibold">
                  {gameModes.find((m) => m.id === gameInfos.GameMode)?.name || 'Custom'}
                </span>
              </div>

              <div>
                <label className="block text-xs font-semibold text-base-content/80 mb-1.5">Active Game Mode</label>
                <select
                  className="select select-bordered select-sm font-medium w-full"
                  value={gameInfos.GameMode}
                  onChange={(e) =>
                    setGameInfos({ ...gameInfos, GameMode: parseInt((e.target as HTMLSelectElement).value, 10) })
                  }
                >
                  {gameModes.map((m) => (
                    <option key={m.id} value={m.id}>
                      {m.name}
                    </option>
                  ))}
                </select>
              </div>

              {/* Mode-Specific Parameters */}
              {gameInfos.GameMode === 2 && (
                <div>
                  <label className="block text-xs font-semibold text-base-content/80 mb-1.5">Time Attack Limit (seconds)</label>
                  <input
                    type="number"
                    className="input input-bordered input-sm w-full font-mono"
                    value={Math.floor((gameInfos.TimeAttackLimit || 300000) / 1000)}
                    onInput={(e) =>
                      setGameInfos({
                        ...gameInfos,
                        TimeAttackLimit: (parseInt((e.target as HTMLInputElement).value, 10) || 0) * 1000,
                      })
                    }
                  />
                </div>
              )}

              {gameInfos.GameMode === 1 && (
                <div>
                  <label className="block text-xs font-semibold text-base-content/80 mb-1.5">Rounds Points Limit</label>
                  <input
                    type="number"
                    className="input input-bordered input-sm w-full font-mono"
                    value={gameInfos.RoundsPointsLimit || 50}
                    onInput={(e) =>
                      setGameInfos({
                        ...gameInfos,
                        RoundsPointsLimit: parseInt((e.target as HTMLInputElement).value, 10) || 0,
                      })
                    }
                  />
                </div>
              )}

              {gameInfos.GameMode === 4 && (
                <div>
                  <label className="block text-xs font-semibold text-base-content/80 mb-1.5">Number of Laps</label>
                  <input
                    type="number"
                    className="input input-bordered input-sm w-full font-mono"
                    value={gameInfos.LapsNbLaps || 5}
                    onInput={(e) =>
                      setGameInfos({
                        ...gameInfos,
                        LapsNbLaps: parseInt((e.target as HTMLInputElement).value, 10) || 0,
                      })
                    }
                  />
                </div>
              )}

              {gameInfos.GameMode === 3 && (
                <div>
                  <label className="block text-xs font-semibold text-base-content/80 mb-1.5">Team Points Limit</label>
                  <input
                    type="number"
                    className="input input-bordered input-sm w-full font-mono"
                    value={gameInfos.TeamPointsLimit || 5}
                    onInput={(e) =>
                      setGameInfos({
                        ...gameInfos,
                        TeamPointsLimit: parseInt((e.target as HTMLInputElement).value, 10) || 0,
                      })
                    }
                  />
                </div>
              )}

              {/* General Timing Rules */}
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                <div>
                  <label className="block text-xs font-semibold text-base-content/80 mb-1.5">Chat Time (ms)</label>
                  <input
                    type="number"
                    className="input input-bordered input-sm w-full font-mono"
                    value={gameInfos.ChatTime || 10000}
                    onInput={(e) =>
                      setGameInfos({ ...gameInfos, ChatTime: parseInt((e.target as HTMLInputElement).value, 10) || 0 })
                    }
                  />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-base-content/80 mb-1.5">Warmup Duration</label>
                  <input
                    type="number"
                    className="input input-bordered input-sm w-full font-mono"
                    value={gameInfos.AllWarmUpDuration || 0}
                    onInput={(e) =>
                      setGameInfos({
                        ...gameInfos,
                        AllWarmUpDuration: parseInt((e.target as HTMLInputElement).value, 10) || 0,
                      })
                    }
                  />
                </div>
              </div>
            </div>

            <div className="pt-4 mt-6 border-t border-base-300 flex justify-end">
              <button type="submit" className="btn btn-secondary btn-sm gap-1.5 shadow-sm" disabled={isSavingGame}>
                <Save className="w-4 h-4" /> Save Game Mode
              </button>
            </div>
          </form>
        </div>
      </div>

      {/* Mode Script Settings Section (Trackmania 2020 / Script Mode) */}
      {isScriptMode && Object.keys(scriptSettings).length > 0 && (
        <div className="card bg-base-100 shadow-sm border border-base-300">
          <form onSubmit={handleSaveScriptSettings} className="card-body p-6">
            {/* Header with Title, Search, and Save */}
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-base-300 pb-4">
              <div className="flex items-center gap-2">
                <Sliders className="w-5 h-5 text-accent" />
                <h3 className="font-bold text-base">Mode Script Settings (Trackmania 2020 / Script Mode)</h3>
              </div>

              {/* Action Bar: Search & Save */}
              <div className="flex items-center gap-3">
                <div className="relative">
                  <Search className="w-3.5 h-3.5 text-base-content/40 absolute left-2.5 top-1/2 -translate-y-1/2" />
                  <input
                    type="text"
                    className="input input-bordered input-xs pl-8 w-44 font-mono text-xs"
                    placeholder="Search S_..."
                    value={scriptSearch}
                    onInput={(e) => setScriptSearch((e.target as HTMLInputElement).value)}
                  />
                </div>

                <button type="submit" className="btn btn-accent btn-sm gap-1.5 shadow-sm" disabled={isSavingScript}>
                  <Save className="w-4 h-4" /> Save Script Settings
                </button>
              </div>
            </div>

            {/* Script Settings Grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5 mt-4">
              {filteredScriptSettings.map(([key, val]) => {
                const isBool = typeof val === 'boolean';
                return (
                  <div
                    key={key}
                    className={`flex flex-col justify-between p-3.5 rounded-xl border border-base-300 ${
                      isBool ? 'bg-base-200/50 hover:bg-base-200/70' : 'bg-base-200/40'
                    }`}
                  >
                    <div className="flex items-center justify-between gap-3">
                      <span className="font-mono text-xs font-bold text-base-content/90 truncate" title={key}>
                        {key}
                      </span>

                      {isBool && (
                        <input
                          type="checkbox"
                          className="toggle toggle-primary toggle-sm flex-shrink-0"
                          checked={val}
                          onChange={(e) =>
                            setScriptSettings({
                              ...scriptSettings,
                              [key]: (e.target as HTMLInputElement).checked,
                            })
                          }
                        />
                      )}
                    </div>

                    {!isBool && (
                      <div className="mt-2.5">
                        <input
                          type={typeof val === 'number' ? 'number' : 'text'}
                          className="input input-bordered input-xs w-full font-mono text-xs"
                          value={String(val)}
                          onInput={(e) => {
                            const raw = (e.target as HTMLInputElement).value;
                            const parsed = typeof val === 'number' ? parseInt(raw, 10) || 0 : raw;
                            setScriptSettings({
                              ...scriptSettings,
                              [key]: parsed,
                            });
                          }}
                        />
                      </div>
                    )}
                  </div>
                );
              })}

              {filteredScriptSettings.length === 0 && (
                <div className="col-span-full text-center py-10 text-base-content/40 italic text-xs">
                  No script settings match your search.
                </div>
              )}
            </div>
          </form>
        </div>
      )}
    </div>
  );
}
