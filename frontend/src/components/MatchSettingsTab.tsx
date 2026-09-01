import { h } from 'preact';
import { useState, useEffect } from 'preact/hooks';
import { api } from '../services/api';
import {
  FileText,
  PlayCircle,
  Eye,
  Plus,
  RefreshCw,
  Layers,
} from 'lucide-preact';

interface MatchSettingsTabProps {
  onShowToast: (msg: string, type?: 'success' | 'error' | 'info') => void;
}

export function MatchSettingsTab({ onShowToast }: MatchSettingsTabProps) {
  const [files, setFiles] = useState<any[]>([]);
  const [isLoading, setIsLoading] = useState(false);
  const [selectedDetail, setSelectedDetail] = useState<any | null>(null);

  const fetchFiles = async () => {
    setIsLoading(true);
    try {
      const res = await api.getMatchSettings();
      if (res.success) {
        setFiles(res.files);
      } else {
        onShowToast(res.error || 'Failed to load MatchSettings', 'error');
      }
    } catch (err: any) {
      onShowToast(err.message, 'error');
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    fetchFiles();
  }, []);

  const handleLoadMatchSetting = async (path: string) => {
    try {
      const res = await api.loadMatchSetting(path);
      if (res.success) {
        onShowToast(`MatchSettings '${path}' loaded on server!`, 'success');
      } else {
        onShowToast(res.error || 'Failed to load MatchSettings', 'error');
      }
    } catch (err: any) {
      onShowToast(err.message, 'error');
    }
  };

  const handleInspect = async (path: string) => {
    try {
      const res = await api.getMatchSettingDetail(path);
      if (res.success) {
        setSelectedDetail(res.data);
      } else {
        onShowToast(res.error || 'Failed to inspect file', 'error');
      }
    } catch (err: any) {
      onShowToast(err.message, 'error');
    }
  };

  return (
    <div className="flex flex-col gap-6">
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-lg font-bold">MatchSettings Manager</h2>
          <p className="text-xs text-base-content/60">Load, inspect, and manage server playlists</p>
        </div>
        <button className="btn btn-sm btn-ghost gap-1" onClick={fetchFiles} disabled={isLoading}>
          <RefreshCw className={`w-4 h-4 ${isLoading ? 'animate-spin' : ''}`} />
          Refresh
        </button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {files.length === 0 ? (
          <div className="col-span-full card bg-base-100 p-8 text-center text-base-content/50 italic border border-base-300">
            No MatchSettings XML files found in server directory.
          </div>
        ) : (
          files.map((file) => (
            <div
              key={file.path}
              className="card bg-base-100 shadow-sm border border-base-300 hover:border-primary/50 transition-colors"
            >
              <div className="card-body p-5 flex flex-col justify-between">
                <div>
                  <div className="flex items-center gap-2 mb-2">
                    <FileText className="w-5 h-5 text-primary" />
                    <span className="font-bold text-sm truncate">{file.name}</span>
                  </div>
                  <div className="text-2xs font-mono text-base-content/60">{file.path}</div>
                </div>

                <div className="flex items-center justify-between mt-4 pt-3 border-t border-base-300">
                  <button
                    className="btn btn-xs btn-outline gap-1"
                    onClick={() => handleInspect(file.path)}
                  >
                    <Eye className="w-3.5 h-3.5" /> Inspect
                  </button>
                  <button
                    className="btn btn-xs btn-primary gap-1 shadow-sm"
                    onClick={() => handleLoadMatchSetting(file.path)}
                  >
                    <PlayCircle className="w-3.5 h-3.5" /> Load into Server
                  </button>
                </div>
              </div>
            </div>
          ))
        )}
      </div>

      {/* Inspect Modal */}
      {selectedDetail && (
        <div className="modal modal-open">
          <div className="modal-box max-w-2xl">
            <h3 className="font-bold text-lg flex items-center gap-2">
              <FileText className="w-5 h-5 text-primary" />
              {selectedDetail.file_name}
            </h3>
            <div className="grid grid-cols-2 gap-3 my-3 text-xs bg-base-200 p-3 rounded-lg">
              <div>
                <span className="text-base-content/60 block">Game Mode</span>
                <span className="font-bold">Mode ID: {selectedDetail.game_mode}</span>
              </div>
              <div>
                <span className="text-base-content/60 block">Map Count</span>
                <span className="font-bold">{selectedDetail.map_count} maps</span>
              </div>
            </div>

            <div className="text-xs font-semibold uppercase tracking-wider text-base-content/60 mb-2">
              Playlist Maps
            </div>
            <div className="max-h-60 overflow-y-auto bg-base-200/50 p-2 rounded-lg border border-base-300">
              {selectedDetail.maps?.map((m: any, idx: number) => (
                <div key={idx} className="py-1 px-2 border-b border-base-300 last:border-0 font-mono text-xs truncate">
                  {idx + 1}. {m.file}
                </div>
              ))}
            </div>

            <div className="modal-action">
              <button className="btn btn-sm btn-ghost" onClick={() => setSelectedDetail(null)}>
                Close
              </button>
              <button
                className="btn btn-sm btn-primary"
                onClick={() => {
                  handleLoadMatchSetting(selectedDetail.file_name);
                  setSelectedDetail(null);
                }}
              >
                Load This Playlist
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
