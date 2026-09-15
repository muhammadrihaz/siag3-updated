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
        session()->remove(['role', 'id_jemaat']);
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
}
