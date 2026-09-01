export interface AppUser {
  level: string;
  name: string;
  permissions: string[];
  logged_at: number;
}

export interface ServerInfo {
  name: string;
  address: string;
  port: number;
}

export interface AuthMeResponse {
  authenticated: boolean;
  user: AppUser | null;
  active_server: string | null;
  servers: ServerInfo[];
  app_info: {
    title: string;
    subtitle: string;
    default_theme: string;
  };
}

export interface DashboardStatus {
  success: boolean;
  connected: boolean;
  error?: string;
  server_name: string;
  server_title?: string;
  server_comment?: string;
  version?: string;
  build?: string;
  current_map?: {
    uid: string;
    name_raw: string;
    name_html: string;
    name_clean: string;
    author: string;
    environment: string;
    mood: string;
    author_time: number;
    gold_time: number;
    silver_time: number;
    bronze_time: number;
    file_name: string;
  };
  next_map?: {
    uid: string;
    name_html: string;
    name_clean: string;
    author: string;
    environment: string;
  };
  players_count?: number;
  spectators_count?: number;
  max_players?: number;
  max_spectators?: number;
  is_password_protected?: boolean;
}

export interface ChatMessage {
  raw: string;
  html: string;
  clean: string;
}

export interface Player {
  login: string;
  nickname_raw: string;
  nickname_html: string;
  nickname_clean: string;
  player_id: number;
  team_id: number;
  is_spectator: boolean;
  ladder_ranking: number;
}

export interface PlaylistItem {
  index: number;
  uid: string;
  name_raw: string;
  name_html: string;
  name_clean: string;
  author: string;
  environment: string;
  file_name: string;
  author_time: number;
  gold_time: number;
  is_current: boolean;
}

export interface ServerOverviewItem {
  name: string;
  address: string;
  port: number;
  connected: boolean;
  error?: string;
  server_name: string;
  server_title: string;
  version?: string;
  players_count: number;
  max_players: number;
  current_map: {
    name_raw: string;
    name_html: string;
    name_clean: string;
    author: string;
    environment: string;
  } | null;
}

async function request<T>(endpoint: string, options: RequestInit = {}): Promise<T> {
  const res = await fetch(`/api/${endpoint.replace(/^\//, '')}`, {
    ...options,
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      ...options.headers,
    },
  });

  const data = await res.json();
  return data as T;
}

export const api = {
  // Auth
  getMe: () => request<AuthMeResponse>('auth/me'),
  login: (server: string, password: string, level?: string) =>
    request<{ success: boolean; user?: AppUser; error?: string }>('auth/login', {
      method: 'POST',
      body: JSON.stringify({ server, password, level }),
    }),
  logout: () => request<{ success: boolean }>('auth/logout', { method: 'POST' }),
  switchServer: (server: string) =>
    request<{ success: boolean; active_server?: string; error?: string }>('auth/switch-server', {
      method: 'POST',
      body: JSON.stringify({ server }),
    }),

  // Dashboard
  getStatus: () => request<DashboardStatus>('dashboard/status'),
  getOverview: () => request<{ success: boolean; overview: ServerOverviewItem[] }>('dashboard/overview'),
  getChat: () => request<{ success: boolean; messages: ChatMessage[] }>('dashboard/chat'),
  sendChat: (message: string) =>
    request<{ success: boolean; error?: string }>('dashboard/chat', {
      method: 'POST',
      body: JSON.stringify({ message }),
    }),
  sendAction: (action: string) =>
    request<{ success: boolean; error?: string }>('dashboard/action', {
      method: 'POST',
      body: JSON.stringify({ action }),
    }),

  // Players
  getPlayers: () => request<{ success: boolean; players: Player[]; error?: string }>('players'),
  kickPlayer: (login: string, message: string) =>
    request<{ success: boolean; error?: string }>('players/kick', {
      method: 'POST',
      body: JSON.stringify({ login, message }),
    }),
  banPlayer: (login: string, message: string) =>
    request<{ success: boolean; error?: string }>('players/ban', {
      method: 'POST',
      body: JSON.stringify({ login, message }),
    }),
  unbanPlayer: (login: string) =>
    request<{ success: boolean; error?: string }>('players/unban', {
      method: 'POST',
      body: JSON.stringify({ login }),
    }),
  forceSpectator: (login: string) =>
    request<{ success: boolean; error?: string }>('players/force-spectator', {
      method: 'POST',
      body: JSON.stringify({ login }),
    }),
  forcePlayer: (login: string) =>
    request<{ success: boolean; error?: string }>('players/force-player', {
      method: 'POST',
      body: JSON.stringify({ login }),
    }),
  getBans: () => request<{ success: boolean; bans: any[]; blacklisted: any[]; guests: any[] }>('players/bans'),

  // Maps
  getPlaylist: () => request<{ success: boolean; playlist: PlaylistItem[]; error?: string }>('maps/playlist'),
  removeMap: (fileName: string) =>
    request<{ success: boolean; error?: string }>('maps/remove', {
      method: 'POST',
      body: JSON.stringify({ file_name: fileName }),
    }),
  insertMap: (fileName: string) =>
    request<{ success: boolean; error?: string }>('maps/insert', {
      method: 'POST',
      body: JSON.stringify({ file_name: fileName }),
    }),
  jumpToMap: (index: number) =>
    request<{ success: boolean; error?: string }>('maps/jump', {
      method: 'POST',
      body: JSON.stringify({ index }),
    }),
  getLocalMaps: (path: string = '') =>
    request<{ success: boolean; data: { folders: any[]; files: any[]; current_path: string }; error?: string }>(
      `maps/local?path=${encodeURIComponent(path)}`
    ),

  // MatchSettings
  getMatchSettings: () => request<{ success: boolean; files: any[]; error?: string }>('matchsettings'),
  getMatchSettingDetail: (path: string) =>
    request<{ success: boolean; data: any; error?: string }>(`matchsettings/detail?path=${encodeURIComponent(path)}`),
  loadMatchSetting: (path: string) =>
    request<{ success: boolean; error?: string }>('matchsettings/load', {
      method: 'POST',
      body: JSON.stringify({ path }),
    }),
  createMatchSetting: (filename: string, maps: string[], gameMode: number, scriptName?: string) =>
    request<{ success: boolean; path?: string; error?: string }>('matchsettings/create', {
      method: 'POST',
      body: JSON.stringify({ filename, maps, game_mode: gameMode, script_name: scriptName }),
    }),

  // Server Config
  getServerOptions: () => request<{ success: boolean; options: any; error?: string }>('server/options'),
  setServerOptions: (options: any) =>
    request<{ success: boolean; error?: string }>('server/options', {
      method: 'POST',
      body: JSON.stringify(options),
    }),
  getGameInfos: () => request<{ success: boolean; game_infos: any; error?: string }>('server/game-infos'),
  setGameInfos: (infos: any) =>
    request<{ success: boolean; error?: string }>('server/game-infos', {
      method: 'POST',
      body: JSON.stringify(infos),
    }),
  getScriptSettings: () =>
    request<{ success: boolean; is_script_mode: boolean; info: any; settings: any; error?: string }>('server/script-settings'),
  setScriptSettings: (settings: any) =>
    request<{ success: boolean; error?: string }>('server/script-settings', {
      method: 'POST',
      body: JSON.stringify({ settings }),
    }),

  // Master Online Config
  getConfigStatus: () => request<{ success: boolean; enabled: boolean; unlocked: boolean }>('config/status'),
  unlockConfig: (password: string) =>
    request<{ success: boolean; unlocked?: boolean; error?: string }>('config/unlock', {
      method: 'POST',
      body: JSON.stringify({ password }),
    }),
  lockConfig: () => request<{ success: boolean; unlocked: boolean }>('config/lock', { method: 'POST' }),
  changeMasterPassword: (newPassword: string, confirmPassword: string) =>
    request<{ success: boolean; message?: string; error?: string }>('config/change-password', {
      method: 'POST',
      body: JSON.stringify({ new_password: newPassword, confirm_password: confirmPassword }),
    }),

  // Admin Config
  getServersConfig: () => request<{ success: boolean; servers: any[] }>('config/servers'),
  saveServerConfig: (originalName: string, server: any) =>
    request<{ success: boolean; servers?: any[]; error?: string }>('config/servers/save', {
      method: 'POST',
      body: JSON.stringify({ original_name: originalName, server }),
    }),
  deleteServerConfig: (name: string) =>
    request<{ success: boolean; servers?: any[]; error?: string }>('config/servers/delete', {
      method: 'POST',
      body: JSON.stringify({ name }),
    }),
  testServerConnection: (server: any) =>
    request<{ success: boolean; message?: string; error?: string; version?: any }>('config/servers/test', {
      method: 'POST',
      body: JSON.stringify(server),
    }),
  getAdminLevels: () => request<{ success: boolean; admin_levels: any }>('config/admin-levels'),
};
