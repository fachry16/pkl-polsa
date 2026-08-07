@extends('layouts.app')

@section('content')

<h1 class="page-header">
    Tambah Penilaian RPS
</h1>

<x-alert type="error" :message="session('error')" />

<form action="{{ route('rps.penilaian.store', $rps) }}"
      method="POST"
      class="card">

    @csrf

    @foreach(['tugas','quiz','uts','uas','praktikum','project'] as $item)

    <div class="form-group">

        <label class="form-label">
            {{ ucfirst($item) }} (%)
        </label>

        <input type="number"
               name="{{ $item }}"
               value="{{ old($item, 0) }}"
               min="0"
               max="100"
               step="0.01"
               class="form-input w-full bobot-input"
               oninput="hitungTotalPenilaian()">

        @error($item)
        <p class="form-error">{{ $message }}</p>
        @enderror

    </div>

    @endforeach

    <div class="form-group">
        <label class="form-label font-bold">Total</label>
        <p id="total-penilaian" class="text-lg font-bold">0%</p>
        <p class="text-sm">Total harus tepat <strong>100%</strong>.</p>
    </div>

    <div class="btn-group">

        <button class="btn btn-primary">

            Simpan

        </button>

        <a href="{{ route('rps.penilaian.index', $rps) }}"
           class="btn btn-secondary">

            Kembali

        </a>

    </div>

</form>

@push('scripts')
<script>
function hitungTotalPenilaian() {
    var inputs = document.querySelectorAll('.bobot-input');
    var total = 0;
    inputs.forEach(function(input) {
        total += parseFloat(input.value) || 0;
    });
    document.getElementById('total-penilaian').textContent = total + '%';
}
hitungTotalPenilaian();
</script>
@endpush

@endsection
