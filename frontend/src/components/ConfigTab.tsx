import { h } from 'preact';
import { useState, useEffect } from 'preact/hooks';
import { api } from '../services/api';
import {
  Settings,
  Shield,
  Server,
  Plus,
  Edit2,
  Trash2,
  Activity,
  CheckCircle2,
  XCircle,
  Save,
  Lock,
  Unlock,
  KeyRound,
} from 'lucide-preact';

interface ConfigTabProps {
  onShowToast: (msg: string, type?: 'success' | 'error' | 'info') => void;
  onConfigChanged?: () => void;
}

export function ConfigTab({ onShowToast, onConfigChanged }: ConfigTabProps) {
  const [isUnlocked, setIsUnlocked] = useState(false);
  const [masterPassword, setMasterPassword] = useState('');
  const [isUnlocking, setIsUnlocking] = useState(false);

  // Change master password state
  const [newMasterPassword, setNewMasterPassword] = useState('');
  const [confirmMasterPassword, setConfirmMasterPassword] = useState('');
  const [isChangingPassword, setIsChangingPassword] = useState(false);

  const [servers, setServers] = useState<any[]>([]);
  const [adminLevels, setAdminLevels] = useState<any>({});
  const [isLoading, setIsLoading] = useState(false);

  // Edit/Add Server Modal state
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [originalName, setOriginalName] = useState('');
  const [serverForm, setServerForm] = useState<any>({
    name: '',
    address: '127.0.0.1',
    port: 5000,
    timeout: 3,
    maps_base_path: '',
    matchsettings_path: 'MatchSettings/',
    admin_passwords: {
      SuperAdmin: 'SuperAdmin',
      Admin: 'Admin',
      User: 'User',
    },
  });

  const [isTesting, setIsTesting] = useState(false);
  const [testResult, setTestResult] = useState<{ success: boolean; message?: string; error?: string } | null>(null);
  const [isSaving, setIsSaving] = useState(false);

  const checkStatus = async () => {
    try {
      const statusRes = await api.getConfigStatus();
      if (statusRes.success) {
        setIsUnlocked(statusRes.unlocked);
        if (statusRes.unlocked) {
          fetchConfig();
        }
      }
    } catch {
      // ignore
    }
  };

  const fetchConfig = async () => {
    setIsLoading(true);
    try {
      const [srvRes, lvlRes] = await Promise.all([
        api.getServersConfig(),
        api.getAdminLevels(),
      ]);
      if (srvRes.success) setServers(srvRes.servers);
      if (lvlRes.success) setAdminLevels(lvlRes.admin_levels);
    } catch (err: any) {
      onShowToast(err.message, 'error');
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    checkStatus();
  }, []);

  const handleUnlock = async (e: h.JSX.TargetedEvent) => {
    e.preventDefault();
    if (!masterPassword.trim() || isUnlocking) return;

    setIsUnlocking(true);
    try {
      const res = await api.unlockConfig(masterPassword);
      if (res.success && res.unlocked) {
        setIsUnlocked(true);
        setMasterPassword('');
        onShowToast('Master configuration unlocked!', 'success');
        fetchConfig();
      } else {
        onShowToast(res.error || 'Invalid master configuration password', 'error');
      }
    } catch (err: any) {
      onShowToast(err.message, 'error');
    } finally {
      setIsUnlocking(false);
    }
  };

  const handleLock = async () => {
    try {
      await api.lockConfig();
      setIsUnlocked(false);
      onShowToast('Configuration locked.', 'info');
    } catch (err: any) {
      onShowToast(err.message, 'error');
    }
  };

  const openAddServerModal = () => {
    setOriginalName('');
    setServerForm({
      name: `Server ${servers.length + 1}`,
      address: '127.0.0.1',
      port: 5000 + servers.length,
      timeout: 3,
      maps_base_path: '',
      matchsettings_path: 'MatchSettings/',
      admin_passwords: {
        SuperAdmin: 'SuperAdmin',
        Admin: 'Admin',
        User: 'User',
      },
    });
    setTestResult(null);
    setIsModalOpen(true);
  };

  const openEditServerModal = (srv: any) => {
    setOriginalName(srv.name);
    setServerForm({
      name: srv.name,
      address: srv.address || '127.0.0.1',
      port: srv.port || 5000,
      timeout: srv.timeout || 3,
      maps_base_path: srv.maps_base_path || '',
      matchsettings_path: srv.matchsettings_path || 'MatchSettings/',
      admin_passwords: srv.admin_passwords || {
        SuperAdmin: 'SuperAdmin',
        Admin: 'Admin',
        User: 'User',
      },
    });
    setTestResult(null);
    setIsModalOpen(true);
  };

  const handleTestConnection = async () => {
    setIsTesting(true);
    setTestResult(null);
    try {
      const res = await api.testServerConnection({
        address: serverForm.address,
        port: parseInt(serverForm.port, 10),
        timeout: parseFloat(serverForm.timeout) || 3.0,
      });
      setTestResult(res);
    } catch (err: any) {
      setTestResult({ success: false, error: err.message });
    } finally {
      setIsTesting(false);
    }
  };

  const handleSaveServer = async (e: h.JSX.TargetedEvent) => {
    e.preventDefault();
    if (!serverForm.name.trim()) return;

    setIsSaving(true);
    try {
      const res = await api.saveServerConfig(originalName, serverForm);
      if (res.success) {
        if (res.servers) setServers(res.servers);
        onShowToast(
          originalName ? `Server '${serverForm.name}' updated!` : `Server '${serverForm.name}' added!`,
          'success'
        );
        setIsModalOpen(false);
        onConfigChanged?.();
      } else {
        onShowToast(res.error || 'Failed saving server configuration', 'error');
      }
    } catch (err: any) {
      onShowToast(err.message, 'error');
    } finally {
      setIsSaving(false);
    }
  };

  const handleDeleteServer = async (name: string) => {
    if (!confirm(`Are you sure you want to delete server '${name}'?`)) return;

    try {
      const res = await api.deleteServerConfig(name);
      if (res.success) {
        if (res.servers) setServers(res.servers);
        onShowToast(`Server '${name}' removed.`, 'success');
        onConfigChanged?.();
      } else {
        onShowToast(res.error || 'Failed to delete server', 'error');
      }
    } catch (err: any) {
      onShowToast(err.message, 'error');
    }
  };

  const handleChangeMasterPassword = async (e: h.JSX.TargetedEvent) => {
    e.preventDefault();
    if (!newMasterPassword.trim() || isChangingPassword) return;

    if (newMasterPassword.length < 4) {
      onShowToast('Master password must be at least 4 characters long.', 'error');
      return;
    }

    if (newMasterPassword !== confirmMasterPassword) {
      onShowToast('Passwords do not match.', 'error');
      return;
    }

    setIsChangingPassword(true);
    try {
      const res = await api.changeMasterPassword(newMasterPassword, confirmMasterPassword);
      if (res.success) {
        onShowToast(res.message || 'Master password updated successfully!', 'success');
        setNewMasterPassword('');
        setConfirmMasterPassword('');
      } else {
        onShowToast(res.error || 'Failed to update master password', 'error');
      }
    } catch (err: any) {
      onShowToast(err.message, 'error');
    } finally {
      setIsChangingPassword(false);
    }
  };

  // If locked, render the Master Configuration Password Prompt
  if (!isUnlocked) {
    return (
      <div className="flex flex-col items-center justify-center py-12 px-4 max-w-md mx-auto">
        <div className="card bg-base-100 shadow-md border border-base-300 w-full">
          <form onSubmit={handleUnlock} className="card-body p-6 flex flex-col gap-4">
            <div className="flex items-center gap-3 border-b border-base-300 pb-3">
              <div className="w-10 h-10 rounded-xl bg-warning/20 text-warning flex items-center justify-center flex-shrink-0">
                <KeyRound className="w-5 h-5" />
              </div>
              <div>
                <h3 className="font-bold text-base">Online Configuration</h3>
                <p className="text-xs text-base-content/60">Master Security Authentication</p>
              </div>
            </div>

            <p className="text-xs text-base-content/70">
              Server setup and system permissions require the dedicated <strong>Master Configuration Password</strong> (defined in <code className="text-2xs bg-base-200 px-1 py-0.5 rounded">config/adminserv.php</code>).
            </p>

            <div className="form-control">
              <label className="label text-xs font-semibold py-1">Master Password</label>
              <input
                type="password"
                className="input input-bordered input-sm w-full"
                placeholder="Enter master password (default: adminserv)..."
                value={masterPassword}
                onInput={(e) => setMasterPassword((e.target as HTMLInputElement).value)}
                autoFocus
              />
            </div>

            <button
              type="submit"
              className="btn btn-warning btn-sm w-full gap-1.5 shadow-sm mt-1"
              disabled={isUnlocking || !masterPassword.trim()}
            >
              <Unlock className="w-4 h-4" />
              {isUnlocking ? 'Unlocking...' : 'Unlock Configuration'}
            </button>
          </form>
        </div>
      </div>
    );
  }

  return (
    <div className="flex flex-col gap-6">
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-lg font-bold">Server & Roles Management</h2>
          <p className="text-xs text-base-content/60">Configure dedicated servers and role permissions</p>
        </div>
        <div className="flex items-center gap-2">
          <button className="btn btn-xs btn-outline text-warning gap-1" onClick={handleLock} title="Lock configuration">
            <Lock className="w-3.5 h-3.5" /> Lock
          </button>
          <button className="btn btn-sm btn-primary gap-1 shadow-sm" onClick={openAddServerModal}>
            <Plus className="w-4 h-4" /> Add Server
          </button>
        </div>
      </div>

      {/* Servers list table */}
      <div className="card bg-base-100 shadow-sm border border-base-300">
        <div className="card-body p-6">
          <h3 className="font-bold text-base border-b border-base-300 pb-3 flex items-center gap-2">
            <Server className="w-5 h-5 text-primary" /> Configured Dedicated Servers ({servers.length})
          </h3>

          <div className="overflow-x-auto mt-2">
            <table className="table table-zebra table-sm">
              <thead>
                <tr className="border-b border-base-300">
                  <th>Server Profile</th>
                  <th>Host / IP</th>
                  <th>XML-RPC Port</th>
                  <th>Maps Directory</th>
                  <th className="text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                {servers.length === 0 ? (
                  <tr>
                    <td colSpan={5} className="text-center py-6 text-base-content/50 italic text-sm">
                      No dedicated servers configured. Click 'Add Server' to create one.
                    </td>
                  </tr>
                ) : (
                  servers.map((s) => (
                    <tr key={s.name}>
                      <td className="font-bold">{s.name}</td>
                      <td className="font-mono text-xs">{s.address}</td>
                      <td className="font-mono text-xs text-primary font-bold">{s.port}</td>
                      <td className="font-mono text-2xs text-base-content/60 truncate max-w-xs">
                        {s.maps_base_path || 'Default'}
                      </td>
                      <td className="text-right">
                        <div className="flex items-center justify-end gap-1">
                          <button
                            className="btn btn-ghost btn-xs text-primary"
                            title="Edit server"
                            onClick={() => openEditServerModal(s)}
                          >
                            <Edit2 className="w-3.5 h-3.5" />
                          </button>
                          <button
                            className="btn btn-ghost btn-xs text-error"
                            title="Delete server"
                            onClick={() => handleDeleteServer(s.name)}
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

      {/* Admin Levels & Permissions */}
      <div className="card bg-base-100 shadow-sm border border-base-300">
        <div className="card-body p-6">
          <h3 className="font-bold text-base border-b border-base-300 pb-3 flex items-center gap-2">
            <Shield className="w-5 h-5 text-secondary" /> Admin Roles & Permissions
          </h3>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
            {Object.entries(adminLevels).map(([role, conf]: [string, any]) => (
              <div key={role} className="p-4 rounded-xl bg-base-200 border border-base-300 flex flex-col gap-2">
                <div className="flex items-center justify-between">
                  <span className="font-bold text-sm text-primary">{role}</span>
                  <span className="badge badge-outline badge-xs">{conf.permissions?.length || 0} perms</span>
                </div>
                <p className="text-xs text-base-content/70">{conf.description || ''}</p>
                <div className="flex flex-wrap gap-1 mt-2">
                  {conf.permissions?.map((p: string) => (
                    <span key={p} className="badge badge-ghost badge-2xs text-2xs">
                      {p}
                    </span>
                  ))}
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Change Master Password Card */}
      <div className="card bg-base-100 shadow-sm border border-base-300">
        <div className="card-body p-6">
          <div className="flex items-center justify-between border-b border-base-300 pb-3">
            <h3 className="font-bold text-base flex items-center gap-2">
              <KeyRound className="w-5 h-5 text-warning" /> Change Master Configuration Password
            </h3>
            <span className="badge badge-outline badge-xs text-2xs font-mono">Bcrypt Encrypted</span>
          </div>

          <p className="text-xs text-base-content/70 mt-1">
            Update the master password used to unlock and manage server configurations. This password will be securely hashed and stored in <code className="text-2xs bg-base-200 px-1 py-0.5 rounded">config/adminserv.php</code>.
          </p>

          <form onSubmit={handleChangeMasterPassword} className="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end mt-2">
            <div className="sm:col-span-4 form-control">
              <label className="label text-xs font-semibold py-1">New Master Password</label>
              <input
                type="password"
                className="input input-bordered input-sm w-full"
                placeholder="Enter new password (min. 4 chars)..."
                value={newMasterPassword}
                onInput={(e) => setNewMasterPassword((e.target as HTMLInputElement).value)}
                required
              />
            </div>

            <div className="sm:col-span-4 form-control">
              <label className="label text-xs font-semibold py-1">Confirm New Password</label>
              <input
                type="password"
                className="input input-bordered input-sm w-full"
                placeholder="Repeat new password..."
                value={confirmMasterPassword}
                onInput={(e) => setConfirmMasterPassword((e.target as HTMLInputElement).value)}
                required
              />
            </div>

            <div className="sm:col-span-4">
              <button
                type="submit"
                className="btn btn-warning btn-sm w-full gap-1.5 shadow-sm font-semibold"
                disabled={isChangingPassword || !newMasterPassword.trim() || !confirmMasterPassword.trim()}
              >
                <Save className="w-4 h-4" />
                {isChangingPassword ? 'Saving...' : 'Update Master Password'}
              </button>
            </div>
          </form>
        </div>
      </div>

      {/* Add / Edit Server Modal */}
      {isModalOpen && (
        <div className="modal modal-open">
          <div className="modal-box max-w-lg">
            <h3 className="font-bold text-lg flex items-center gap-2">
              <Server className="w-5 h-5 text-primary" />
              {originalName ? `Edit '${originalName}'` : 'Add New Dedicated Server'}
            </h3>

            <form onSubmit={handleSaveServer} className="flex flex-col gap-4 mt-4">
              <div className="form-control">
                <label className="label text-xs font-semibold py-1">Server Profile Name</label>
                <input
                  type="text"
                  className="input input-bordered input-sm"
                  placeholder="e.g. My TM Server"
                  value={serverForm.name}
                  onInput={(e) => setServerForm({ ...serverForm, name: (e.target as HTMLInputElement).value })}
                  required
                />
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div className="form-control">
                  <label className="label text-xs font-semibold py-1">IP / Hostname</label>
                  <input
                    type="text"
                    className="input input-bordered input-sm"
                    value={serverForm.address}
                    onInput={(e) => setServerForm({ ...serverForm, address: (e.target as HTMLInputElement).value })}
                    required
                  />
                </div>
                <div className="form-control">
                  <label className="label text-xs font-semibold py-1">XML-RPC Port</label>
                  <input
                    type="number"
                    className="input input-bordered input-sm"
                    value={serverForm.port}
                    onInput={(e) =>
                      setServerForm({ ...serverForm, port: parseInt((e.target as HTMLInputElement).value, 10) })
                    }
                    required
                  />
                </div>
              </div>

              <div className="form-control">
                <label className="label text-xs font-semibold py-1">Maps Base Path on Server</label>
                <input
                  type="text"
                  className="input input-bordered input-sm"
                  placeholder="e.g. /home/user/UserData/Maps/"
                  value={serverForm.maps_base_path}
                  onInput={(e) =>
                    setServerForm({ ...serverForm, maps_base_path: (e.target as HTMLInputElement).value })
                  }
                />
              </div>

              {/* Admin Passwords Section */}
              <div className="bg-base-200/60 p-3 rounded-xl border border-base-300 flex flex-col gap-2">
                <span className="text-xs font-bold flex items-center gap-1.5 text-base-content/80">
                  <Lock className="w-3.5 h-3.5 text-primary" /> Dedicated Server Admin Passwords
                </span>
                <div className="grid grid-cols-3 gap-2">
                  <div className="form-control">
                    <label className="label text-2xs font-semibold py-0.5">SuperAdmin</label>
                    <input
                      type="text"
                      className="input input-bordered input-xs"
                      value={serverForm.admin_passwords?.SuperAdmin || ''}
                      onInput={(e) =>
                        setServerForm({
                          ...serverForm,
                          admin_passwords: {
                            ...serverForm.admin_passwords,
                            SuperAdmin: (e.target as HTMLInputElement).value,
                          },
                        })
                      }
                    />
                  </div>
                  <div className="form-control">
                    <label className="label text-2xs font-semibold py-0.5">Admin</label>
                    <input
                      type="text"
                      className="input input-bordered input-xs"
                      value={serverForm.admin_passwords?.Admin || ''}
                      onInput={(e) =>
                        setServerForm({
                          ...serverForm,
                          admin_passwords: {
                            ...serverForm.admin_passwords,
                            Admin: (e.target as HTMLInputElement).value,
                          },
                        })
                      }
                    />
                  </div>
                  <div className="form-control">
                    <label className="label text-2xs font-semibold py-0.5">User</label>
                    <input
                      type="text"
                      className="input input-bordered input-xs"
                      value={serverForm.admin_passwords?.User || ''}
                      onInput={(e) =>
                        setServerForm({
                          ...serverForm,
                          admin_passwords: {
                            ...serverForm.admin_passwords,
                            User: (e.target as HTMLInputElement).value,
                          },
                        })
                      }
                    />
                  </div>
                </div>
              </div>

              {/* Test Connection Button & Status */}
              <div className="flex items-center justify-between pt-1">
                <button
                  type="button"
                  className={`btn btn-xs btn-outline gap-1 ${isTesting ? 'loading' : ''}`}
                  onClick={handleTestConnection}
                  disabled={isTesting}
                >
                  <Activity className="w-3.5 h-3.5" /> Test Connection
                </button>

                {testResult && (
                  <div
                    className={`text-xs font-semibold flex items-center gap-1 ${
                      testResult.success ? 'text-success' : 'text-error'
                    }`}
                  >
                    {testResult.success ? <CheckCircle2 className="w-4 h-4" /> : <XCircle className="w-4 h-4" />}
                    <span>{testResult.success ? 'Connected!' : 'Connection Failed'}</span>
                  </div>
                )}
              </div>

              <div className="modal-action border-t border-base-300 pt-3 mt-1">
                <button
                  type="button"
                  className="btn btn-sm btn-ghost"
                  onClick={() => setIsModalOpen(false)}
                  disabled={isSaving}
                >
                  Cancel
                </button>
                <button type="submit" className="btn btn-sm btn-primary gap-1" disabled={isSaving}>
                  <Save className="w-4 h-4" /> Save Server Profile
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
