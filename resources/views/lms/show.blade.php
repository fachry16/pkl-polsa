@extends('layouts.lms')

@section('title', $pengampu->mataKuliah->nama ?? 'Kelas')

@section('content')
<div class="kelas-layout">
    <!-- Header with Cover -->
    <div class="header-section relative h-48 md:h-64 bg-gradient-to-r from-blue-600 to-cyan-500">
        <div class="absolute bottom-0 left-0 right-0 p-4 md:p-6 bg-gradient-to-t from-black/50 to-transparent text-white">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-end justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="bg-white/20 backdrop-blur text-white text-xs px-2 py-1 rounded font-medium">
                                {{ $pengampu->mataKuliah->kode ?? '' }}
                            </span>
                            <span class="text-sm opacity-90">Kelas {{ $pengampu->kelas ?? '' }}</span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-bold text-white mb-1">{{ $pengampu->mataKuliah->nama ?? 'Mata Kuliah' }}</h1>
                        <p class="text-sm md:text-base text-white/90">{{ $pengampu->dosen?->user?->name ?? '-' }} &middot; {{ $pengampu->semester_akademik ?? '' }} {{ $pengampu->tahunAkademik?->tahun ?? '' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6">
        <div class="flex flex-col lg:flex-row gap-4">
            <!-- Main Content -->
            <div class="flex-1 min-w-0">
                <!-- Tabs -->
                <div class="bg-white border border-gray-200 rounded-lg mb-4">
                    <div class="flex">
                        <button class="tab-btn active px-4 py-3 text-sm font-medium border-b-2 border-blue-600 text-blue-600 bg-blue-50" data-tab="stream">Stream</button>
                        <button class="tab-btn px-4 py-3 text-sm font-medium text-gray-600 hover:text-gray-900" data-tab="classwork">Classwork</button>
                        <button class="tab-btn px-4 py-3 text-sm font-medium text-gray-600 hover:text-gray-900" data-tab="people">People</button>
                    </div>
                </div>

                <!-- Tab Content -->
                <div id="tab-content" class="space-y-4">
                    <!-- Stream Tab -->
                    <div class="tab-pane active" id="stream-content">
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-semibold">Stream</h2>
                            </div>
                            
                            <!-- Posts -->
                            <div class="space-y-3">
                                @forelse($pengampu->lmsPengumumans->sortByDesc('published_at')->take(5) as $pengumuman)
                                    <div class="post-card bg-gray-50 rounded-lg p-3 hover:bg-gray-100 transition-colors cursor-pointer">
                                        <div class="flex items-start gap-3">
                                            <div class="bg-blue-100 text-blue-700 p-2 rounded-lg">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9L11.414 2.586a2 2 0 113.182 3.182L2.686 15.364a2 2 0 00.177 2.863l4.243 4.243a2 2 0 002.864-.177L18.586 8.998a2 2 0 00-2.999-2.999L11.414 9.172z"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-medium text-sm">{{ $pengumuman->judul }}</div>
                                                <div class="text-xs text-gray-500 mt-1">{{ $pengumuman->published_at?->format('d M Y H:i') }}</div>
                                                <div class="text-sm text-gray-700 mt-1">{{ Str::limit($pengumuman->isi, 150) }}</div>
                                                <div class="text-xs text-gray-400 mt-1"> oleh {{ $pengampu->dosen->user->name ?? 'Dosen' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-gray-500">Belum ada postingan.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Classwork Tab -->
                    <div class="tab-pane hidden" id="classwork-content">
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <h2 class="text-lg font-semibold mb-4">Classwork</h2>
                            
                            <!-- Materi -->
                            <div class="mb-6">
                                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">Materi</h3>
                                <div class="space-y-2">
                                    @forelse($pengampu->lmsMateris as $materi)
                                        <div class="classwork-item bg-gray-50 rounded-lg p-3 hover:bg-blue-50 border border-transparent transition-all cursor-pointer">
                                            <div class="flex items-center gap-3">
                                                <div class="bg-gray-200 text-gray-600 p-2 rounded-lg">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                                        <polyline points="14 2 14 8 20 8"/>
                                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="font-medium">{{ $materi->judul }}</div>
                                                    <div class="text-xs text-gray-500">Diupload {{ $materi->created_at->diffForHumans() }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4 text-gray-500">Belum ada materi.</div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Tugas -->
                            <div>
                                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">Tugas</h3>
                                <div class="space-y-2">
                                    @forelse($pengampu->lmsTugas as $tugas)
                                        <div class="classwork-item bg-gray-50 rounded-lg p-3 hover:bg-orange-50 border border-transparent transition-all cursor-pointer">
                                            <div class="flex items-center gap-3">
                                                <div class="bg-gray-200 text-gray-600 p-2 rounded-lg">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M9 5H3v11a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="font-medium truncate">{{ $tugas->judul }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        Deadline: {{ $tugas->deadline->format('d M Y H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4 text-gray-500">Belum ada tugas.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- People Tab -->
                    <div class="tab-pane hidden" id="people-content">
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <h2 class="text-lg font-semibold mb-4">People</h2>
                            
                            <!-- Dosen -->
                            <div class="mb-4">
                                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">Pengampu</h3>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                            {{ strtoupper(substr($pengampu->dosen->user->name ?? 'D', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-medium">{{ $pengampu->dosen->user->name ?? '-' }}</div>
                                            <div class="text-xs text-gray-500">{{ $pengampu->dosen->jabatan ?? 'Dosen' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mahasiswa -->
                            <div>
                                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">Mahasiswa ({{ $pengampu->mahasiswas->count() }} orang)</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    @forelse($pengampu->mahasiswas->take(6) as $mahasiswa)
                                        <div class="bg-gray-50 rounded-lg p-2 flex items-center gap-2">
                                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-white font-bold text-xs">
                                                {{ strtoupper(substr($mahasiswa->user->name ?? 'M', 0, 1)) }}
                                            </div>
                                            <div class="text-xs">{{ $mahasiswa->user->name ?? '-' }}</div>
                                        </div>
                                    @empty
                                        <div class="text-xs text-gray-500 col-span-2">Belum ada mahasiswa.</div>
                                    @endforelse
                                </div>
                                @if($pengampu->mahasiswas->count() > 6)
                                    <div class="text-xs text-gray-400 mt-2">... dan {{ $pengampu->mahasiswas->count() - 6 }} mahasiswa lainnya</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="w-full lg:w-80 flex-shrink-0">
                <!-- Upcoming Tasks -->
                <div class="bg-white border border-gray-200 rounded-lg p-4 mb-4">
                    <h3 class="text-lg font-semibold mb-3">Upcoming Tasks</h3>
                    @forelse($pengampu->lmsTugas->where('deadline', '>', now())->sortBy('deadline')->take(5) as $task)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg mb-2">
                            <div class="flex-shrink-0">
                                @if($task->deadline->isTomorrow())
                                    <span class="inline-block w-3 h-3 bg-blue-500 rounded-full"></span>
                                @elseif($task->deadline->isToday())
                                    <span class="inline-block w-3 h-3 bg-orange-500 rounded-full"></span>
                                @else
                                    <span class="inline-block w-3 h-3 bg-red-500 rounded-full"></span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium truncate">{{ $task->judul }}</div>
                                <div class="text-xs text-gray-500">{{ $task->deadline->format('d M H:i') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-gray-500 text-sm">Tidak ada tugas mendatang.</div>
                    @endforelse
                </div>

                <!-- Class Stats -->
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <h3 class="text-lg font-semibold mb-3">Statistik Kelas</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total Mahasiswa:</span>
                            <span class="font-medium">{{ $pengampu->mahasiswas->count() }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total Materi:</span>
                            <span class="font-medium">{{ $pengampu->lmsMateris()->count() }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total Tugas:</span>
                            <span class="font-medium">{{ $pengampu->lmsTugas()->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabPanes = document.querySelectorAll('.tab-pane');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'border-blue-600', 'text-blue-600', 'bg-blue-50');
                        btn.classList.add('text-gray-600');
                    });
                    this.classList.add('active', 'border-blue-600', 'text-blue-600', 'bg-blue-50');
                    this.classList.remove('text-gray-600');
                    
                    tabPanes.forEach(pane => {
                        pane.classList.add('hidden');
                    });
                    document.getElementById(tabId + '-content').classList.remove('hidden');
                });
            });
        });
    </script>
</div>
@endsection
