<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class RolePermissionTest extends CIUnitTestCase
{
    protected function tearDown(): void
    {
        session()->remove(['role', 'id_cabang_gereja']);
        parent::tearDown();
    }

    public function testAdminAreaCanManageAttendanceOnlyInsideAssignedBranch(): void
    {
        session()->set([
            'role' => 'admin_area',
            'id_cabang_gereja' => 2,
        ]);

        $this->assertTrue(canView('absensi'));
        $this->assertTrue(canCreate('absensi'));
        $this->assertTrue(canEdit('absensi'));
        $this->assertTrue(canDelete('absensi'));
        $this->assertTrue(canAccessCabang(2, 'absensi'));
        $this->assertFalse(canAccessCabang(3, 'absensi'));
    }

    public function testKasitCanCreateAndEditOfferingsAcrossBranches(): void
    {
        session()->set([
            'role' => 'kasir',
            'id_cabang_gereja' => 1,
        ]);

        $this->assertTrue(canView('ibadah'));
        $this->assertTrue(canView('persembahan'));
        $this->assertTrue(canCreate('persembahan'));
        $this->assertTrue(canEdit('persembahan'));
        $this->assertFalse(canDelete('persembahan'));
        $this->assertTrue(canAccessCabang(5, 'persembahan'));
    }

    public function testKetuaFiveCanInspectAndApproveAllChurchBranches(): void
    {
        session()->set([
            'role' => 'ketua_5',
            'id_cabang_gereja' => 1,
        ]);

        $this->assertTrue(canView('ibadah'));
        $this->assertTrue(canView('absensi'));
        $this->assertTrue(canView('pelayan'));
        $this->assertTrue(canView('persembahan'));
        $this->assertTrue(canApproveKetua5());
        $this->assertTrue(canAccessCabang(5, 'ibadah'));
    }

    public function testTreasurerCanApproveOfferingWhileKasitCannot(): void
    {
        session()->set('role', 'bendahara');
        $this->assertTrue(canApprovePersembahan());
        $this->assertTrue(canView('persembahan'));

        session()->set('role', 'kasir');
        $this->assertFalse(canApprovePersembahan());
    }
}
