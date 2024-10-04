<?php

namespace App\Tests\Repository;

use App\Entity\AssociationFees;
use App\Repository\AssociationFeesRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AssociationFeesRepositoryTest extends KernelTestCase
{
    private $entityManager;
    private $associationFeesRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->associationFeesRepository = $this->entityManager->getRepository(AssociationFees::class);
    }

    public function testFindFeeForPrice(): void
    {
        $associationFee = new AssociationFees();
        $associationFee->setMinPrice(1000);
        $associationFee->setMaxPrice(3000);
        $associationFee->setAssociationFee(20);

        $this->entityManager->persist($associationFee);
        $this->entityManager->flush();

        $fee = $this->associationFeesRepository->findFeeForPrice(2000);

        $this->assertNotNull($fee);
        $this->assertEquals(20, $fee->getAssociationFee());

        $feeNotFound = $this->associationFeesRepository->findFeeForPrice(5000);
        $this->assertNull($feeNotFound);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        parent::tearDown();
    }
}
