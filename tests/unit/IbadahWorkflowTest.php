<?php

use App\Models\IbadahModel;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class IbadahWorkflowTest extends CIUnitTestCase
{
    public function testModelRejectsFinishedStatusWhileKetuaFiveIsPending(): void
    {
        $model = (new ReflectionClass(IbadahModel::class))->newInstanceWithoutConstructor();
        $method = new ReflectionMethod(IbadahModel::class, 'preventUnapprovedFinish');
        $method->setAccessible(true);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('approval Ketua 5 masih pending');

        $method->invoke($model, [
            'data' => [
                'status' => 'selesai',
                'approval_ketua5' => 'pending',
            ],
        ]);
    }

    public function testModelAllowsFinishedStatusAfterKetuaFiveApproval(): void
    {
        $model = (new ReflectionClass(IbadahModel::class))->newInstanceWithoutConstructor();
        $method = new ReflectionMethod(IbadahModel::class, 'preventUnapprovedFinish');
        $method->setAccessible(true);
        $eventData = [
            'data' => [
                'status' => 'selesai',
                'approval_ketua5' => 'approved',
            ],
        ];

        $this->assertSame($eventData, $method->invoke($model, $eventData));
    }
}
