import { h } from 'preact';
import { useState, useEffect } from 'preact/hooks';
import { DashboardStatus, ChatMessage, api } from '../services/api';
import { TmText } from './TmText';
import {
  RotateCcw,
  SkipForward,
  XCircle,
  Send,
  Users,
  Eye,
  Trophy,
  Server,
  Activity,
  Radio,
  Lock,
} from 'lucide-preact';

interface DashboardTabProps {
  status: DashboardStatus | null;
  onRefresh: () => void;
  onShowToast: (msg: string, type?: 'success' | 'error' | 'info') => void;
}

function formatTime(ms: number): string {
  if (!ms || ms <= 0) return '00:00.00';
  const min = Math.floor(ms / 60000);
  const sec = Math.floor((ms % 60000) / 1000);
  const hundredths = Math.floor((ms % 1000) / 10);
  return `${String(min).padStart(2, '0')}:${String(sec).padStart(2, '0')}.${String(hundredths).padStart(2, '0')}`;
}

export function DashboardTab({ status, onRefresh, onShowToast }: DashboardTabProps) {
  const [chatMessages, setChatMessages] = useState<ChatMessage[]>([]);
  const [chatInput, setChatInput] = useState('');
  const [isSending, setIsSending] = useState(false);
  const [isActionLoading, setIsActionLoading] = useState(false);

  // Poll chat messages every 3 seconds
  useEffect(() => {
    let mounted = true;
    const fetchChat = async () => {
      try {
        const res = await api.getChat();
        if (res.success && mounted) {
          setChatMessages(res.messages);
        }
      } catch {
        // Silently catch polling error
      }
    };

    fetchChat();
    const timer = setInterval(fetchChat, 3000);
    return () => {
      mounted = false;
      clearInterval(timer);
    };
  }, [status?.server_name]);

  const handleSendChat = async (e?: h.JSX.TargetedEvent) => {
    if (e) e.preventDefault();
    if (!chatInput.trim() || isSending) return;

    setIsSending(true);
    try {
      const res = await api.sendChat(chatInput);
      if (res.success) {
        setChatInput('');
        const chatRes = await api.getChat();
        if (chatRes.success) setChatMessages(chatRes.messages);
        onShowToast('Message sent to server', 'success');
      } else {
        onShowToast(res.error || 'Failed to send message', 'error');
      }
    } catch (err: any) {
      onShowToast(err.message, 'error');
    } finally {
      setIsSending(false);
    }
  };

  const handleQuickAction = async (action: string, label: string) => {
    if (isActionLoading) return;
    setIsActionLoading(true);
    try {
      const res = await api.sendAction(action);
      if (res.success) {
        onShowToast(`${label} executed successfully`, 'success');
        onRefresh();
      } else {
        onShowToast(res.error || `Failed executing ${label}`, 'error');
      }
    } catch (err: any) {
      onShowToast(err.message, 'error');
    } finally {
      setIsActionLoading(false);
    }
  };

  if (!status?.connected) {
    return (
      <div className="flex flex-col items-center justify-center py-16 gap-4">
        <div className="w-16 h-16 rounded-full bg-error/10 text-error flex items-center justify-center">
          <Activity className="w-8 h-8 animate-pulse" />
        </div>
        <h2 className="text-xl font-bold">Server Offline or Disconnected</h2>
        <p className="text-base-content/60 max-w-md text-center text-sm">
          {status?.error || 'Unable to connect to the dedicated server XML-RPC port.'}
        </p>
        <button className="btn btn-primary btn-sm mt-2" onClick={onRefresh}>
          Retry Connection
        </button>
      </div>
    );
  }

  const curMap = status.current_map;
  const nextMap = status.next_map;

  return (
    <div className="flex flex-col gap-6">
      {/* Top Banner: Server Identity */}
      <div className="card bg-base-100 shadow-sm border border-base-300">
        <div className="card-body p-6">
          <div className="flex flex-wrap items-center justify-between gap-4">
            <div className="flex items-center gap-3">
              <div className="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                <Server className="w-6 h-6" />
              </div>
              <div>
                <div className="text-2xl font-black">
                  <TmText html={status.server_title} fallback={status.server_name} />
                </div>
                <div className="text-xs text-base-content/60 flex items-center gap-2 mt-0.5">
                  <span className="badge badge-success badge-xs gap-1">
                    <span className="w-1.5 h-1.5 rounded-full bg-success-content animate-ping" />
                    Online
                  </span>
                  <span>Build: {status.build || status.version || 'ManiaPlanet'}</span>
                  {status.is_password_protected && (
                    <span className="badge badge-warning badge-xs gap-1">
                      <Lock className="w-3 h-3" /> Password Protected
                    </span>
                  )}
                </div>
              </div>
            </div>

            {/* Quick Admin Actions */}
            <div className="flex items-center gap-2">
              <button
                className="btn btn-sm btn-outline gap-1.5"
                onClick={() => handleQuickAction('restart', 'Restart Map')}
                disabled={isActionLoading}
              >
                <RotateCcw className="w-4 h-4 text-warning" />
                Restart
              </button>
              <button
                className="btn btn-sm btn-outline gap-1.5"
                onClick={() => handleQuickAction('next', 'Next Map')}
                disabled={isActionLoading}
              >
                <SkipForward className="w-4 h-4 text-info" />
                Skip Map
              </button>
              <button
                className="btn btn-sm btn-ghost gap-1.5 text-error"
                onClick={() => handleQuickAction('cancel_vote', 'Cancel Vote')}
                disabled={isActionLoading}
              >
                <XCircle className="w-4 h-4" />
                Cancel Vote
              </button>
            </div>
          </div>
        </div>
      </div>

      {/* Grid: Current Map & Player Counters */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Current Map Card */}
        <div className="card bg-base-100 shadow-sm border border-base-300 lg:col-span-2">
          <div className="card-body p-6">
            <div className="flex items-center justify-between border-b border-base-300 pb-3">
              <div className="flex items-center gap-2 font-bold text-base">
                <Trophy className="w-5 h-5 text-primary" />
                Current Rotation
              </div>
              <span className="badge badge-primary badge-outline text-xs font-semibold px-2.5 py-0.5">
                {curMap?.environment || 'TrackMania'}
              </span>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-12 gap-6 mt-4 items-stretch">
              {/* Current Map Info (Left 7 Cols) */}
              <div className="md:col-span-7 flex gap-4 items-center">
                {curMap?.file_name ? (
                  <img
                    src={`/api/maps/thumbnail?path=${encodeURIComponent(curMap.file_name)}`}
                    alt={curMap.name_clean || 'Map Thumbnail'}
                    className="w-28 h-28 rounded-2xl object-cover [transform:scaleY(-1)] border border-base-300 shadow-sm bg-base-300 flex-shrink-0"
                    onError={(e) => {
                      (e.target as HTMLElement).style.display = 'none';
                    }}
                  />
                ) : (
                  <div className="w-28 h-28 rounded-2xl bg-base-200 border border-base-300 flex items-center justify-center text-base-content/40 flex-shrink-0">
                    <Trophy className="w-10 h-10 opacity-40" />
                  </div>
                )}
                <div className="flex-1 min-w-0 flex flex-col justify-between py-0.5 gap-2">
                  <div>
                    <div className="text-xl font-black truncate tracking-tight">
                      <TmText html={curMap?.name_html} fallback={curMap?.name_clean || 'No Map Loaded'} />
                    </div>
                    <p className="text-xs text-base-content/60 mt-0.5">
                      By <span className="font-semibold text-base-content">{curMap?.author || 'Unknown'}</span>
                    </p>
                  </div>

                  <div className="flex flex-wrap items-center gap-2 mt-1">
                    <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-base-200 border border-base-300 text-xs">
                      <span className="text-sm">🥇</span>
                      <span className="text-base-content/60 font-medium">Author:</span>
                      <span className="font-mono font-bold text-primary">
                        {curMap?.author_time ? formatTime(curMap.author_time) : '-'}
                      </span>
                    </div>
                    <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-base-200 border border-base-300 text-xs">
                      <span className="text-sm">🥈</span>
                      <span className="text-base-content/60 font-medium">Gold:</span>
                      <span className="font-mono font-bold text-warning">
                        {curMap?.gold_time ? formatTime(curMap.gold_time) : '-'}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              {/* Up Next Preview (Right 5 Cols) */}
              <div className="md:col-span-5 bg-base-200/50 p-4 rounded-2xl border border-base-300 flex flex-col justify-between gap-3">
                <div>
                  <div className="text-2xs font-bold uppercase tracking-wider text-secondary flex items-center gap-1.5 mb-1.5">
                    <Radio className="w-3.5 h-3.5 animate-pulse" /> Up Next
                  </div>
                  <div className="font-bold text-sm truncate">
                    <TmText html={nextMap?.name_html} fallback={nextMap?.name_clean || 'Automatic rotation'} />
                  </div>
                  <div className="text-xs text-base-content/60 mt-0.5">
                    By {nextMap?.author || 'Unknown'}
                  </div>
                </div>

                <div className="flex items-center justify-between pt-2 border-t border-base-300/60 text-xs text-base-content/60">
                  <span>{nextMap?.environment || curMap?.environment || 'Stadium'}</span>
                  <button
                    className="btn btn-xs btn-ghost btn-outline gap-1 text-2xs hover:btn-primary"
                    onClick={() => handleAction('next')}
                    title="Skip to next map"
                  >
                    <SkipForward className="w-3 h-3" /> Skip
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Player Stats Card */}
        <div className="card bg-base-100 shadow-sm border border-base-300">
          <div className="card-body p-6 flex flex-col justify-between">
            <div className="flex items-center gap-2 font-bold text-base border-b border-base-300 pb-3">
              <Users className="w-5 h-5 text-primary" />
              Live Server Capacity
            </div>

            <div className="flex flex-col gap-4 my-2">
              <div>
                <div className="flex justify-between text-xs font-medium mb-1">
                  <span className="flex items-center gap-1">
                    <Users className="w-3.5 h-3.5 text-primary" /> Active Players
                  </span>
                  <span className="font-bold">
                    {status.players_count} / {status.max_players}
                  </span>
                </div>
                <progress
                  className="progress progress-primary w-full"
                  value={status.players_count || 0}
                  max={status.max_players || 32}
                />
              </div>

              <div>
                <div className="flex justify-between text-xs font-medium mb-1">
                  <span className="flex items-center gap-1">
                    <Eye className="w-3.5 h-3.5 text-secondary" /> Spectators
                  </span>
                  <span className="font-bold">
                    {status.spectators_count} / {status.max_spectators}
                  </span>
                </div>
                <progress
                  className="progress progress-secondary w-full"
                  value={status.spectators_count || 0}
                  max={status.max_spectators || 32}
                />
              </div>
            </div>

            <div className="text-xs text-base-content/50 text-center">
              Real-time synchronization active
            </div>
          </div>
        </div>
      </div>

      {/* Live Chat Box */}
      <div className="card bg-base-100 shadow-sm border border-base-300">
        <div className="card-body p-6">
          <div className="flex items-center justify-between border-b border-base-300 pb-3">
            <div className="flex items-center gap-2 font-bold text-base">
              <Send className="w-5 h-5 text-primary" />
              Live Server Chat
            </div>
            <span className="text-xs text-base-content/60">Auto-updating feed</span>
          </div>

          {/* Chat Stream Window */}
          <div className="bg-base-200 rounded-xl p-4 h-64 overflow-y-auto flex flex-col gap-2 font-sans text-sm border border-base-300">
            {chatMessages.length === 0 ? (
              <div className="text-base-content/40 text-center my-auto text-xs italic">
                No recent chat messages on server.
              </div>
            ) : (
              chatMessages.map((msg, idx) => (
                <div key={idx} className="leading-relaxed break-words">
                  <TmText html={msg.html} fallback={msg.clean} />
                </div>
              ))
            )}
          </div>

          {/* Preset Chips */}
          <div className="flex flex-wrap gap-1.5 mt-2">
            {[
              'Welcome to the server! Have fun!',
              'Server will restart after this map.',
              'Please follow clean driving rules.',
              'Good luck and have fun! (GL & HF)',
            ].map((preset) => (
              <button
                key={preset}
                type="button"
                className="badge badge-outline hover:badge-primary cursor-pointer text-xs transition-colors py-2 px-2.5"
                onClick={() => setChatInput(preset)}
              >
                {preset}
              </button>
            ))}
          </div>

          {/* Send Input */}
          <form onSubmit={handleSendChat} className="flex gap-2 mt-3">
            <input
              type="text"
              className="input input-bordered input-sm flex-1"
              placeholder="Broadcast a server message or chat..."
              value={chatInput}
              onInput={(e) => setChatInput((e.target as HTMLInputElement).value)}
            />
            <button
              type="submit"
              className="btn btn-primary btn-sm gap-1 shadow-sm"
              disabled={isSending || !chatInput.trim()}
            >
              <Send className="w-3.5 h-3.5" />
              Send
            </button>
          </form>
        </div>
      </div>
    </div>
  );
}
