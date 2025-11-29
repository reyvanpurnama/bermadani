<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-white">Activity Logs</h1>
            <p class="text-sm text-slate-500">Riwayat aktivitas admin, super admin, dan developer</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 p-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="text-xs font-medium text-slate-500 mb-1 block">User</label>
                <select wire:model.live="filterUser" class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-600 dark:bg-slate-800 focus:ring-primary focus:border-primary">
                    <option value="">Semua User</option>
                    @foreach($this->users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-medium text-slate-500 mb-1 block">Module</label>
                <select wire:model.live="filterModule" class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-600 dark:bg-slate-800 focus:ring-primary focus:border-primary">
                    <option value="">Semua Module</option>
                    @foreach($this->modules as $module)
                        <option value="{{ $module }}">{{ $module }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-medium text-slate-500 mb-1 block">Action</label>
                <select wire:model.live="filterAction" class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-600 dark:bg-slate-800 focus:ring-primary focus:border-primary">
                    <option value="">Semua Action</option>
                    @foreach($this->actions as $action)
                        <option value="{{ $action }}">{{ $action }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-medium text-slate-500 mb-1 block">Tanggal</label>
                <input type="date" wire:model.live="filterDate" class="w-full text-sm rounded-lg border-slate-200 dark:border-slate-600 dark:bg-slate-800 focus:ring-primary focus:border-primary">
            </div>
            <div class="flex items-end">
                <button wire:click="clearFilters" class="w-full px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                    <i class='bx bx-reset mr-1'></i> Reset
                </button>
            </div>
        </div>
    </div>

    {{-- Activity Log Table --}}
    <div class="bg-card dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">User</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">Action</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">Module</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($this->logs as $log)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-xs text-slate-600 dark:text-slate-300">{{ $log->created_at->format('d M Y') }}</div>
                                <div class="text-[10px] text-slate-400">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-primary text-white flex items-center justify-center text-[10px] font-bold">
                                        {{ strtoupper(substr($log->user->name ?? 'U', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="text-xs font-medium text-slate-800 dark:text-white">{{ $log->user->name ?? '-' }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $log->user->role ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-{{ $log->actionColor }}-100 dark:bg-{{ $log->actionColor }}-900/30 text-{{ $log->actionColor }}-600 dark:text-{{ $log->actionColor }}-400">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-300">
                                    <i class='bx {{ $log->moduleIcon }}'></i>
                                    {{ $log->module }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-xs text-slate-600 dark:text-slate-300 max-w-xs truncate" title="{{ $log->description }}">
                                    {{ $log->description }}
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-[10px] font-mono text-slate-400">{{ $log->ip_address }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="text-slate-400">
                                    <i class='bx bx-history text-4xl'></i>
                                    <p class="text-sm mt-2">Belum ada activity log</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($this->logs->hasPages())
            <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-700">
                {{ $this->logs->links() }}
            </div>
        @endif
    </div>
</div>
