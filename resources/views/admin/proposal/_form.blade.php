{{-- Field bersama formulir tambah/ubah proposal. --}}
@isset($mahasiswa)
    <div class="mb-3">
        <label for="user_id" class="form-label">Mahasiswa pemilik <span class="text-danger">*</span></label>
        <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
            <option value="">Pilih mahasiswa…</option>
            @foreach ($mahasiswa as $mhs)
                <option value="{{ $mhs->id }}" @selected((int) old('user_id') === $mhs->id)>{{ $mhs->name }} ({{ $mhs->nrp }})</option>
            @endforeach
        </select>
        @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
@else
    <div class="mb-3">
        <span class="form-label d-block">Mahasiswa pemilik</span>
        <p class="form-control-plaintext py-0">{{ $proposal->user->name }} <span class="text-body-secondary">({{ $proposal->user->nrp ?? '-' }})</span></p>
    </div>
@endisset

<div class="mb-3">
    <label for="judul" class="form-label">Judul proposal <span class="text-danger">*</span></label>
    <input type="text" id="judul" name="judul" value="{{ old('judul', $proposal->judul) }}"
        class="form-control @error('judul') is-invalid @enderror" maxlength="255" required>
    @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label for="deskripsi" class="form-label">Deskripsi</label>
    <textarea id="deskripsi" name="deskripsi" rows="5" maxlength="2000"
        class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $proposal->deskripsi) }}</textarea>
    @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-md-5 mb-3">
        <label for="tema_agent" class="form-label">Tema agent <span class="text-danger">*</span></label>
        <select id="tema_agent" name="tema_agent" class="form-select @error('tema_agent') is-invalid @enderror" required>
            @foreach ($temaAgent as $tema)
                <option value="{{ $tema }}" @selected(old('tema_agent', $proposal->tema_agent ?? 'Ollama') === $tema)>{{ $tema }}</option>
            @endforeach
        </select>
        @error('tema_agent') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-7 mb-3">
        <label for="api_key_secure" class="form-label">
            API key @unless ($proposal->exists) <span class="text-danger">*</span> @endunless
        </label>
        <input type="password" id="api_key_secure" name="api_key_secure" autocomplete="off"
            class="form-control @error('api_key_secure') is-invalid @enderror"
            @if ($proposal->exists) placeholder="Kosongkan untuk mempertahankan {{ $proposal->maskedApiKey() }}" @else required @endif>
        <div class="form-text">Disimpan terenkripsi dengan APP_KEY dan tidak pernah ditampilkan utuh.</div>
        @error('api_key_secure') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
