<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use App\Entity\VehiculeType;
use App\Entity\AssociationFees;

class CalculationControllerTest extends WebTestCase
{
    private $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->createDatabaseSchema();

        $this->setUpDatabase();
    }

    private function createDatabaseSchema(): void
    {
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        if (!empty($metadata)) {
            $schemaTool->dropDatabase();

            try {
                $schemaTool->createSchema($metadata);
            } catch (\Exception $e) {
                echo "Schema creation failed: " . $e->getMessage();
            }
        }
    }

    private function setUpDatabase(): void
    {
        // Create and persist a new VehiculeType entity
        $vehicleType = new VehiculeType();
        $vehicleType->setName('Test Vehicle');
        $vehicleType->setBasicFeeMin(10.0);
        $vehicleType->setBasicFeeMax(500.0);
        $vehicleType->setBasicFeeRate(0.10);
        $vehicleType->setSpecialFeeRate(0.04);
        $this->entityManager->persist($vehicleType);

        // Create and persist a new AssociationFees entity
        $associationFees = new AssociationFees();
        $associationFees->setMaxPrice(1000);
        $associationFees->setMinPrice(500);
        $associationFees->setAssociationFee(20.0);
        $this->entityManager->persist($associationFees);

        // Flush the data into the database
        $this->entityManager->flush();
    }

    public function testSaveCalculationWithValidInputAndAuthenticatedUser(): void
    {
        $client = static::createClient();

        $data = [
            'base_price' => 1000,
            'vehicle_type_id' => 1
        ];

        $client->request(
            'POST',
            '/api/calculate',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $decodedContent = json_decode($response->getContent(), true);
        $this->assertEquals('Calculation completed!', $decodedContent['status']);
        $this->assertTrue($decodedContent['saved']);
        $this->assertEquals(1500, $decodedContent['fees']['total_cost']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // Close the EntityManager
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
