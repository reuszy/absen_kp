@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-md-7 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Perangkat Fingerprint</h4>
                <p class="card-description">Daftarkan mesin ZKTeco baru ke sistem</p>

                <form action="{{ route('fingerprint.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="nama_lokasi">Nama Lokasi</label>
                        <input type="text"
                               class="form-control @error('nama_lokasi') is-invalid @enderror"
                               id="nama_lokasi"
                               name="nama_lokasi"
                               placeholder="Contoh: Gedung Rektorat"
                               value="{{ old('nama_lokasi') }}"
                               autofocus>
                        @error('nama_lokasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="ip">IP Lokal</label>
                        <input type="text"
                               class="form-control @error('ip') is-invalid @enderror"
                               id="ip"
                               name="ip"
                               placeholder="Contoh: 192.168.1.100"
                               value="{{ old('ip') }}">
                        <small class="text-muted">Alamat IP mesin di jaringan lokal.</small>
                        @error('ip')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="vpn">IP VPN</label>
                        <input type="text"
                               class="form-control @error('vpn') is-invalid @enderror"
                               id="vpn"
                               name="vpn"
                               placeholder="Contoh: 10.8.0.5"
                               value="{{ old('vpn') }}">
                        <small class="text-muted">Alamat IP VPN mesin untuk akses remote. Isi sama dengan IP Lokal jika tidak menggunakan VPN.</small>
                        @error('vpn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="port">Port</label>
                        <input type="number"
                               class="form-control @error('port') is-invalid @enderror"
                               id="port"
                               name="port"
                               placeholder="4370"
                               value="{{ old('port', 4370) }}"
                               min="1"
                               max="65535">
                        <small class="text-muted">Port komunikasi ZKTeco. Default: 4370.</small>
                        @error('port')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary mr-2">Simpan</button>
                    <a href="{{ route('fingerprint.index') }}" class="btn btn-dark">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
