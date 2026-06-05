<?php

namespace Tests\Feature;

use App\Models\User;

use function Pest\Laravel\post;

test('registrasi_gagal_jika_ktp_lebih_dari_enam_belas_digit', function () {
    // Simulasi request dengan KTP 17 digit
    $response = post('/seller/register', [
            
            'name' => 'Abyasa',
            'email' => 'abyasa@easymart.com',    
            'role' => User::ROLE_PENJUAL,
            'status_verifikasi' => User::STATUS_PENDING,
            'nama_toko' => 'Toko Sumber Rejeki',
            'deskripsi_singkat' => 'Toko kebutuhan harian lengkap dan terjangkau.',
            'no_handphone_pic' => '081234567890',
            'alamat_pic' => 'Jl. Melati No. 10',
            'rt' => '001',
            'rw' => '002',
            'nama_kelurahan' => 'Kebon Jeruk',
            'kabupaten_kota' => 'Jakarta Barat',
            'provinsi' => 'DKI Jakarta',
            'no_ktp_pic' => '12345', // KTP tidak valid
            'password' => 'Rahasia123!',
            'password_confirmation' => 'Rahasia123!',
        ]);

        // Assert: Pastikan muncul error validasi untuk KTP
        $response->assertSessionHasErrors(['no_ktp_pic']);
});