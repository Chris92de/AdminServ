import { h } from 'preact';
import { useState, useEffect } from 'preact/hooks';
import { PlaylistItem, api } from '../services/api';
import { TmText } from './TmText';
import {
  ListOrdered,
  FolderOpen,
  Upload,
  Play,
  Trash2,
  PlusCircle,
  RefreshCw,
  Folder,
  FileCode,
  CheckCircle2,
} from 'lucide-preact';

interface MapsTabProps {
  onShowToast: (msg: string, type?: 'success' | 'error' | 'info') => void;
}

function formatTime(ms: number): string {
  if (!ms || ms <= 0) return '00:00.00';
  const min = Math.floor(ms / 60000);
  const sec = Math.floor((ms % 60000) / 1000);
  const hundredths = Math.floor((ms % 1000) / 10);
  return `${String(min).padStart(2, '0')}:${String(sec).padStart(2, '0')}.${String(hundredths).padStart(2, '0')}`;
}

export function MapsTab({ onShowToast }: MapsTabProps) {
  const [activeSubTab, setActiveSubTab] = useState<'playlist' | 'local'>('playlist');
  const [playlist, setPlaylist] = useState<PlaylistItem[]>([]);
  const [isLoading, setIsLoading] = useState(false);

  // Local folder explorer state
  const [currentPath, setCurrentPath] = useState('');
  const [localFolders, setLocalFolders] = useState<any[]>([]);
  const [localFiles, setLocalFiles] = useState<any[]>([]);
  const [isUploading, setIsUploading] = useState(false);

  const fetchPlaylist = async () => {
    setIsLoading(true);
    try {
      const res = await api.getPlaylist();
      if (res.success) {
        setPlaylist(res.playlist);
      } else {
        onShowToast(res.error || 'Failed to fetch playlist', 'error');
      }
    } catch (err: any) {
      onShowToast(err.message, 'error');
    } finally {
      setIsLoading(false);
    }
  };

  const fetchLocalMaps = async (path: string = '') => {
    setIsLoading(true);
    try {
      const res = await api.getLocalMaps(path);
      if (res.success) {
        setLocalFolders(res.data.folders);
        setLocalFiles(res.data.files);
        setCurrentPath(res.data.current_path);
      } else {
        onShowToast(res.error || 'Failed to browse maps', 'error');
      }
    } catch (err: any) {
      onShowToast(err.message, 'error');
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    fetchPlaylist();
    fetchLocalMaps();
  }, []);

  const handleJumpToMap = async (index: number, name: string) => {
    try {
      const res = await api.jumpToMap(index);
      if (res.success) {
        onShowToast(`Jumping to map ${name}`, 'success');
        fetchPlaylist();
      } else {
        onShowToast(res.error || 'Failed to jump map', 'error');
      }
    } catch (err: any) {
      onShowToast(err.message, 'error');
    }
  };

  const handleRemoveMap = async (fileName: string) => {
    try {
      const res = await api.removeMap(fileName);
      if (res.success) {
        onShowToast('Removed map from playlist', 'success');
        fetchPlaylist();
      } else {
        onShowToast(res.error || 'Failed to remove map', 'error');
      }
    } catch (err: any) {
      onShowToast(err.message, 'error');
    }
  };

  const handleInsertLocalMap = async (filePath: string) => {
    try {
      const res = await api.insertMap(filePath);
      if (res.success) {
        onShowToast('Map inserted into active playlist', 'success');
        fetchPlaylist();
      } else {
        onShowToast(res.error || 'Failed inserting map', 'error');
      }
    } catch (err: any) {
      onShowToast(err.message, 'error');
    }
  };

  const handleUploadFile = async (e: h.JSX.TargetedEvent) => {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('map_file', file);
    formData.append('sub_dir', currentPath);

    setIsUploading(true);
    try {
      const res = await fetch('/api/maps/upload', {
        method: 'POST',
        body: formData,
      });
      const data = await res.json();
      if (data.success) {
        onShowToast(`Uploaded ${file.name} successfully`, 'success');
        fetchLocalMaps(currentPath);
      } else {
        onShowToast(data.error || 'Upload failed', 'error');
      }
    } catch (err: any) {
      onShowToast(err.message, 'error');
    } finally {
      setIsUploading(false);
      target.value = '';
    }
  };

  return (
    <div className="flex flex-col gap-6">
      {/* Sub tabs & controls */}
      <div className="flex items-center justify-between">
        <div role="tablist" className="tabs tabs-box">
          <button
            role="tab"
            className={`tab ${activeSubTab === 'playlist' ? 'tab-active font-semibold' : ''}`}
            onClick={() => setActiveSubTab('playlist')}
          >
            <ListOrdered className="w-4 h-4 mr-2" />
            Active Playlist ({playlist.length})
          </button>
          <button
            role="tab"
            className={`tab ${activeSubTab === 'local' ? 'tab-active font-semibold' : ''}`}
            onClick={() => setActiveSubTab('local')}
          >
            <FolderOpen className="w-4 h-4 mr-2" />
            Local Maps Explorer
          </button>
        </div>

        <button
          className="btn btn-sm btn-ghost gap-1"
          onClick={() => {
            if (activeSubTab === 'playlist') fetchPlaylist();
            else fetchLocalMaps(currentPath);
          }}
          disabled={isLoading}
        >
          <RefreshCw className={`w-4 h-4 ${isLoading ? 'animate-spin' : ''}`} />
          Refresh
        </button>
      </div>

      {activeSubTab === 'playlist' ? (
        /* Active Playlist Table */
        <div className="card bg-base-100 shadow-sm border border-base-300">
          <div className="card-body p-6">
            <div className="overflow-x-auto">
              <table className="table table-zebra table-sm">
                <thead>
                  <tr className="border-b border-base-300">
                    <th className="w-12 text-center">#</th>
                    <th>Map Name</th>
                    <th>Author</th>
                    <th>Environment</th>
                    <th>Author Time</th>
                    <th className="text-right">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {playlist.length === 0 ? (
                    <tr>
                      <td colSpan={6} className="text-center py-8 text-base-content/50 italic text-sm">
                        No maps currently in server playlist.
                      </td>
                    </tr>
                  ) : (
                    playlist.map((m, idx) => (
                      <tr key={m.uid || idx} className={m.is_current ? 'bg-primary/10 font-medium' : ''}>
                        <td className="text-center font-mono text-xs">
                          {m.is_current ? (
                            <span className="badge badge-primary badge-xs">Now</span>
                          ) : (
                            idx + 1
                          )}
                        </td>
                        <td>
                          <div className="font-semibold flex items-center gap-2">
                            <TmText html={m.name_html} fallback={m.name_clean} />
                          </div>
                          <span className="font-mono text-2xs text-base-content/50 block truncate max-w-xs">
                            {m.file_name}
                          </span>
                        </td>
                        <td className="text-xs">{m.author}</td>
                        <td>
                          <span className="badge badge-outline badge-xs">{m.environment || 'Stadium'}</span>
                        </td>
                        <td className="font-mono text-xs text-primary font-bold">
                          {formatTime(m.author_time)}
                        </td>
                        <td className="text-right">
                          <div className="flex items-center justify-end gap-1">
                            {!m.is_current && (
                              <button
                                className="btn btn-ghost btn-xs text-primary"
                                title="Jump to this map"
                                onClick={() => handleJumpToMap(m.index, m.name_clean)}
                              >
                                <Play className="w-3.5 h-3.5" />
                              </button>
                            )}
                            <button
                              className="btn btn-ghost btn-xs text-error"
                              title="Remove from playlist"
                              onClick={() => handleRemoveMap(m.file_name)}
                            >
                              <Trash2 className="w-3.5 h-3.5" />
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
        /* Local Maps Explorer */
        <div className="card bg-base-100 shadow-sm border border-base-300">
          <div className="card-body p-6 flex flex-col gap-4">
            {/* Explorer Header & Breadcrumb */}
            <div className="flex flex-wrap items-center justify-between gap-3 border-b border-base-300 pb-3">
              <div className="flex items-center gap-2 text-sm font-mono">
                <span className="text-base-content/50">Path:</span>
                <button
                  className="link link-hover font-bold text-primary"
                  onClick={() => fetchLocalMaps('')}
                >
                  [Tracks Root]
                </button>
                {currentPath && (
                  <span className="text-base-content">/ {currentPath}</span>
                )}
              </div>

              {/* Upload Map Button */}
              <div>
                <label className={`btn btn-sm btn-primary gap-1 shadow-sm ${isUploading ? 'loading' : ''}`}>
                  <Upload className="w-4 h-4" />
                  Upload Map (.Gbx)
                  <input
                    type="file"
                    accept=".gbx,.map.gbx"
                    className="hidden"
                    onChange={handleUploadFile}
                    disabled={isUploading}
                  />
                </label>
              </div>
            </div>

            {/* Folder & Files Grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
              {/* Folder list */}
              {localFolders.map((f) => (
                <button
                  key={f.name}
                  className="flex items-center gap-3 p-3 rounded-xl bg-base-200 hover:bg-base-300 transition-colors text-left border border-base-300"
                  onClick={() => fetchLocalMaps(f.path)}
                >
                  <Folder className="w-6 h-6 text-warning flex-shrink-0" />
                  <div className="truncate">
                    <div className="font-bold text-sm truncate">{f.name}</div>
                    <div className="text-2xs text-base-content/50">Folder</div>
                  </div>
                </button>
              ))}

              {/* Map files list */}
              {localFiles.map((f) => (
                <div
                  key={f.name}
                  className="flex flex-col justify-between p-3.5 rounded-xl bg-base-200/60 hover:bg-base-200 transition-colors border border-base-300 gap-2"
                >
                  <div className="flex items-start gap-3">
                    {f.has_thumbnail ? (
                      <img
                        src={`/api/maps/thumbnail?path=${encodeURIComponent(f.path)}`}
                        alt={f.name}
                        className="w-14 h-14 rounded-lg object-cover [transform:scaleY(-1)] border border-base-300 shadow-xs flex-shrink-0 bg-base-300"
                        loading="lazy"
                        onError={(e) => {
                          (e.target as HTMLElement).style.display = 'none';
                        }}
                      />
                    ) : (
                      <div className="w-14 h-14 rounded-lg bg-base-300/80 flex items-center justify-center flex-shrink-0 border border-base-300 text-primary">
                        <FileCode className="w-6 h-6 opacity-70" />
                      </div>
                    )}
                    <div className="overflow-hidden flex-1 min-w-0">
                      <div className="font-bold text-sm truncate" title={f.name}>{f.name}</div>
                      <div className="text-xs text-base-content/70 flex items-center gap-2 mt-1">
                        <span className="truncate">By {f.author}</span>
                        {f.environment && (
                          <span className="badge badge-ghost badge-2xs flex-shrink-0">{f.environment}</span>
                        )}
                      </div>
                    </div>
                  </div>

                  <div className="flex items-center justify-between pt-2 border-t border-base-300 text-xs">
                    <span className="font-mono text-primary font-bold">
                      {f.author_time > 0 ? formatTime(f.author_time) : '-'}
                    </span>
                    <button
                      className="btn btn-xs btn-primary gap-1"
                      onClick={() => handleInsertLocalMap(f.path)}
                      title="Add to server playlist"
                    >
                      <PlusCircle className="w-3.5 h-3.5" />
                      Add to Playlist
                    </button>
                  </div>
                </div>
              ))}

              {localFolders.length === 0 && localFiles.length === 0 && (
                <div className="col-span-full text-center py-10 text-base-content/50 italic text-sm">
                  This folder is empty.
                </div>
              )}
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
