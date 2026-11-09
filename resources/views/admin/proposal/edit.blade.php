@extends('adminlte::page')

@section('title', 'Ubah Proposal')

@section('content_header')
    <h3 class="mb-0">Ubah Proposal</h3>
@stop

@section('content')
    <div class="card">
        <form method="POST" action="{{ route('admin.proposal.update', $proposal) }}">
            @csrf
            @method('PUT')
            <div class="card-body">
                @include('admin.proposal._form')
            </div>
            <div class="card-footer d-flex justify-content-end gap-2">
                <a href="{{ route('admin.proposal.index') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save" aria-hidden="true"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
@stop
