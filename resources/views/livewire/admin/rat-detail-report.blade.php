<div class="px-4 py-6 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Laporan RAT - Detail Akuntansi</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Tabel lengkap sesuai format RAT dengan dukungan input manual per tahun.</p>
        </div>
        <div class="flex items-center gap-2">
            <label for="ratYear" class="text-sm text-gray-600 dark:text-gray-400 font-medium">Tahun:</label>
            <select id="ratYear" wire:model.live="selectedYear" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 text-sm rounded focus:ring-blue-500 focus:border-blue-500 block p-2">
                @foreach($availableYears as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
            <button wire:click="notifyExport" class="inline-flex items-center gap-2 px-3 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm font-medium rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500">
                <i class='bx bx-export'></i>
                Export
            </button>
        </div>
    </div>

    <div class="flex flex-wrap gap-2 mb-5">
        @foreach($tables as $tableKey => $table)
            <button wire:click="$set('activeTab', '{{ $tableKey }}')"
                class="px-3 py-1.5 rounded-full text-xs font-semibold transition-all {{ $activeTab === $tableKey ? 'bg-indigo-600 text-white shadow' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">
                {{ $table['title'] }}
            </button>
        @endforeach
    </div>

    @foreach($tables as $tableKey => $table)
        @if($activeTab === $tableKey)
            <div class="bg-white dark:bg-gray-800 rounded shadow-sm border border-gray-200 dark:border-gray-700 mb-6 overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200">{{ $table['title'] }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Input hanya untuk tahun terpilih. Ganti tahun untuk edit periode lain.</p>
                    </div>
                    <button wire:click="saveTable('{{ $tableKey }}')" class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded shadow-sm">
                        <i class='bx bx-save'></i>
                        Simpan
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left align-middle">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Keterangan</th>
                                @foreach($table['columns'] as $column)
                                    <th class="px-4 py-3 font-semibold text-right">{{ $column['label'] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($table['rows'] as $row)
                                @php
                                    $indentClass = match ($row['level']) {
                                        1 => 'pl-4',
                                        2 => 'pl-8',
                                        3 => 'pl-12',
                                        default => ''
                                    };
                                @endphp

                                @if($row['type'] === 'section' || $row['type'] === 'group')
                                    <tr class="bg-slate-50 dark:bg-gray-800/40">
                                        <td colspan="{{ 1 + count($table['columns']) }}" class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 {{ $row['type'] === 'section' ? 'uppercase' : '' }}">
                                            <span class="{{ $indentClass }}">{{ $row['label'] }}</span>
                                        </td>
                                    </tr>
                                @else
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">
                                            <span class="{{ $indentClass }} {{ $row['type'] === 'total' ? 'font-semibold' : '' }}">{{ $row['label'] }}</span>
                                        </td>
                                        @foreach($table['columns'] as $column)
                                            <td class="px-4 py-2 text-right text-gray-700 dark:text-gray-200">
                                                @if($row['editable'] && (int) $column['year'] === (int) $selectedYear)
                                                    <input
                                                        type="number"
                                                        step="0.01"
                                                        wire:model.defer="manualInputs.{{ $tableKey }}.{{ $row['key'] }}.{{ $column['key'] }}"
                                                        class="w-32 text-right bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 text-xs rounded px-2 py-1 focus:ring-blue-500 focus:border-blue-500"
                                                    />
                                                @else
                                                    {{ $this->formatCurrency($row['values'][$column['key']] ?? null) }}
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endforeach
</div>
