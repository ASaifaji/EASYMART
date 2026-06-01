<?php

use App\Mail\SellerApprovedMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

test('email otomatis terkirim saat seller di-approve admin', function () {
	Mail::fake();

	$platformUser = User::factory()->create([
		'role' => User::ROLE_PLATFORM,
	]);

	$sellerUser = User::factory()->create([
		'role' => User::ROLE_PENJUAL,
		'status_verifikasi' => User::STATUS_PENDING,
		'name' => 'Andi Pratama',
		'email' => 'andi.pratama@example.com',
		'nama_toko' => 'Toko Sumber Rejeki',
		'deskripsi_singkat' => 'Toko kebutuhan harian lengkap dan terjangkau.',
		'no_handphone_pic' => '081234567890',
		'alamat_pic' => 'Jl. Melati No. 10',
		'rt' => '001',
		'rw' => '002',
		'nama_kelurahan' => 'Kebon Jeruk',
		'kabupaten_kota' => 'Jakarta Barat',
		'provinsi' => 'DKI Jakarta',
		'no_ktp_pic' => '3174010101010001',
	]);

	$response = actingAs($platformUser)
		->from(route('platform.sellers.index', absolute: false))
		->post(route('platform.sellers.approve', $sellerUser->id, absolute: false));

	$response->assertRedirect(route('platform.sellers.index', absolute: false));
	$response->assertSessionHas('status', 'Penjual berhasil diaktifkan.');

	$sellerUser->refresh();

	$this->assertSame(User::STATUS_APPROVED, $sellerUser->status_verifikasi);
	$this->assertDatabaseHas('sellers', [
		'user_id' => $sellerUser->id,
		'storeName' => 'Toko Sumber Rejeki',
		'picName' => 'Andi Pratama',
		'picEmail' => 'andi.pratama@example.com',
		'status' => 'active',
	]);

	Mail::assertSent(SellerApprovedMail::class, function (SellerApprovedMail $mail) use ($sellerUser) {
		return $mail->user->is($sellerUser);
	});
});
