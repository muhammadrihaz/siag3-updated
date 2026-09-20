<?php

use App\Controllers\Auth;
use App\Controllers\WaitlistSakramen;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class JemaatRegistrationTest extends CIUnitTestCase
{
    protected function tearDown(): void
    {
        cache()->delete('permissions_jemaat');
        session()->remove(['logged_in', 'role', 'id_jemaat', 'username']);
        parent::tearDown();
    }

    public function testPhoneNormalizationAcceptsCommonIndonesianFormats(): void
    {
        $controller = (new ReflectionClass(Auth::class))->newInstanceWithoutConstructor();
        $method = new ReflectionMethod(Auth::class, 'normalizePhone');
        $method->setAccessible(true);

        $this->assertSame('081234567890', $method->invoke($controller, '+62 812-3456-7890'));
        $this->assertSame('081234567890', $method->invoke($controller, '812 3456 7890'));
        $this->assertSame('081234567890', $method->invoke($controller, '0812-3456-7890'));
    }

    public function testJemaatCanOnlyAccessItsOwnRequest(): void
    {
        session()->set(['role' => 'jemaat', 'id_jemaat' => 25]);
        $controller = (new ReflectionClass(WaitlistSakramen::class))->newInstanceWithoutConstructor();
        $sessionProperty = new ReflectionProperty(WaitlistSakramen::class, 'session');
        $sessionProperty->setAccessible(true);
        $sessionProperty->setValue($controller, session());
        $method = new ReflectionMethod(WaitlistSakramen::class, 'canAccess');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($controller, (object) ['id_jemaat' => 25]));
        $this->assertFalse($method->invoke($controller, (object) ['id_jemaat' => 26]));
    }

    public function testAuthorizedStaffCanAccessAnyRequest(): void
    {
        session()->set(['role' => 'sekretaris', 'id_jemaat' => null]);
        $controller = (new ReflectionClass(WaitlistSakramen::class))->newInstanceWithoutConstructor();
        $sessionProperty = new ReflectionProperty(WaitlistSakramen::class, 'session');
        $sessionProperty->setAccessible(true);
        $sessionProperty->setValue($controller, session());
        $method = new ReflectionMethod(WaitlistSakramen::class, 'canAccess');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($controller, (object) ['id_jemaat' => 999]));
    }

    public function testJemaatSubmissionViewShowsFaqOptionalAttachmentAndConfirmationCopy(): void
    {
        session()->set(['logged_in' => true, 'role' => 'jemaat', 'id_jemaat' => 25]);
        cache()->save('permissions_jemaat', [(object) ['module_slug' => '__none__']], 60);

        $html = view('waitlist/index', [
            'active_menu' => 'pelayanan',
            'sub_menu' => 'waitlist',
            'title' => 'Permohonan Sakramen Saya',
            'waitlist' => [],
            'jemaat' => [],
            'current_jemaat' => (object) [
                'nama_jemaat' => 'Jemaat Uji',
                'no_anggota' => 'JMT-TEST',
            ],
            'is_staff' => false,
        ]);

        $this->assertStringContainsString('Informasi &amp; FAQ Pelayanan Sakramen', $html);
        $this->assertStringContainsString('Dokumen Persyaratan <span class="text-muted">(Opsional)</span>', $html);
        $this->assertDoesNotMatchRegularExpression('/<input(?=[^>]*\bid="attachment")(?=[^>]*\brequired\b)[^>]*>/i', $html);
        $this->assertStringContainsString('Pengajuan Berhasil Dikirim', $html);
        $this->assertStringContainsString('dalam 2 hari ke depan', $html);
    }

    public function testAdminDashboardShowsSynchronizedPendingSubmissionWidget(): void
    {
        session()->set(['logged_in' => true, 'role' => 'master', 'username' => 'Admin Uji']);

        $html = view('dashboard/index', [
            'active_menu' => 'dashboard',
            'sub_menu' => '',
            'title' => 'Dashboard Analytics',
            'total_jemaat' => 0,
            'total_keluarga' => 0,
            'total_sektor' => 0,
            'total_ibadah' => 0,
            'total_absensi_hari_ini' => 0,
            'user_name' => 'Admin Uji',
            'user_role' => 'master',
            'user_sektor' => null,
            'is_master' => true,
            'cabang_list' => [],
            'can_manage_sakramen' => true,
            'pending_sakramen_count' => 1,
            'pending_sakramen' => [(object) [
                'id' => 42,
                'nama_jemaat' => 'Jemaat Uji',
                'no_anggota' => 'JMT-TEST',
                'jenis_sakramen' => 'sidi',
                'created_at' => '2026-09-20 10:00:00',
            ]],
        ]);

        $this->assertStringContainsString('Pengajuan Sakramen Baru', $html);
        $this->assertStringContainsString('Kelola Pengajuan', $html);
        $this->assertStringContainsString('waitlistsakramen?open=42', $html);
    }
}
