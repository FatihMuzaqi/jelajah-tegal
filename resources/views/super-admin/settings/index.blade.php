@extends('layouts.super-admin')

@section('title', 'Pengaturan Platform')
@section('page-title', 'Status & Pengaturan Platform')
@section('page-description', 'Pemeriksaan kesehatan runtime framework, database, dan lingkungan.')

@section('content')
<div class="row g-4">
    <div class="col-md-6">
        <div class="content-card">
            <div class="card-header">
                <h2>Informasi System Environment</h2>
            </div>
            <div class="card-body">
                <dl class="profile-summary">
                    <div><dt>Versi PHP</dt><dd><code>{{ $health['php_version'] }}</code></dd></div>
                    <div><dt>Versi Laravel</dt><dd><code>{{ $health['laravel_version'] }}</code></dd></div>
                    <div><dt>Koneksi Database</dt><dd><code>{{ $health['database_connection'] }}</code></dd></div>
                    <div><dt>Environment</dt><dd><code>{{ $health['app_env'] }}</code></dd></div>
                    <div><dt>Mode Debug</dt><dd><span class="status-badge status-success">{{ $health['app_debug'] }}</span></dd></div>
                    <div><dt>Timezone</dt><dd>{{ $health['timezone'] }}</dd></div>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="content-card">
            <div class="card-header">
                <h2>Keamanan & Sanitasi Secret</h2>
            </div>
            <div class="card-body">
                <div class="alert alert-info m-0" role="alert">
                     <strong>Strict Redaction Active</strong><br>
                    Kredensial database, `.env`, Midtrans Server Key, dan rahasia R2 disanitasi secara otomatis dan tidak akan pernah ditampilkan pada antarmuka.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
