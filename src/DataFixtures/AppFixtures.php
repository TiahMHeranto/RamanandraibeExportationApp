<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Product;
use App\Entity\Shipment;
use App\Entity\ShipmentLine;
use App\Entity\User;
use App\Enum\ShipmentStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@ramanandraibe.mg');
        $admin->setFullName('Export Manager');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        $clients = [];
        $clientData = [
            ['Nordic Spice House', 'Erik Lindqvist', 'erik@nordicspice.se', '+46 8 555 210', 'Sweden', 'Stockholm'],
            ['Tokyo Aroma Trading', 'Yuki Tanaka', 'yuki@tokyoaroma.jp', '+81 3 5555 8890', 'Japan', 'Tokyo'],
            ['Marseille Gourmet SARL', 'Camille Dubois', 'camille@marseille-gourmet.fr', '+33 4 91 00 00', 'France', 'Marseille'],
        ];

        foreach ($clientData as [$company, $contact, $email, $phone, $country, $city]) {
            $client = (new Client())
                ->setCompanyName($company)
                ->setContactName($contact)
                ->setEmail($email)
                ->setPhone($phone)
                ->setCountry($country)
                ->setCity($city);
            $manager->persist($client);
            $clients[] = $client;
        }

        $products = [];
        $productData = [
            ['Bourbon Vanilla Beans Grade A', 'VAN-A-001', 'Vanilla', 'kg', '95.00', 'Hand-cured Madagascar bourbon vanilla.'],
            ['Black Pepper Whole', 'SPC-BP-010', 'Spices', 'kg', '8.50', 'Malabar-style black pepper from the highlands.'],
            ['Clove Buds', 'SPC-CL-020', 'Spices', 'kg', '12.00', 'Sun-dried clove buds for export.'],
            ['Cocoa Beans Trinitario', 'COC-TR-100', 'Cocoa', 'kg', '4.20', 'Fermented Trinitario cocoa beans.'],
            ['Ylang-Ylang Essential Oil', 'OIL-YY-050', 'Essential oils', 'liter', '180.00', 'Extra grade ylang-ylang oil.'],
        ];

        foreach ($productData as [$name, $sku, $category, $unit, $price, $description]) {
            $product = (new Product())
                ->setName($name)
                ->setSku($sku)
                ->setCategory($category)
                ->setUnit($unit)
                ->setUnitPrice($price)
                ->setDescription($description);
            $manager->persist($product);
            $products[] = $product;
        }

        $shipment = (new Shipment())
            ->setReference('REX-20260731-A1')
            ->setClient($clients[0])
            ->setStatus(ShipmentStatus::InTransit)
            ->setOriginPort('Toamasina')
            ->setDestinationPort('Gothenburg')
            ->setDepartureDate(new \DateTimeImmutable('-5 days'))
            ->setArrivalDate(new \DateTimeImmutable('+18 days'))
            ->setNotes('Priority vanilla consignment for Q3 contracts.');

        $line1 = (new ShipmentLine())
            ->setProduct($products[0])
            ->setQuantity('250.000')
            ->setUnitPrice('95.00');
        $line2 = (new ShipmentLine())
            ->setProduct($products[1])
            ->setQuantity('500.000')
            ->setUnitPrice('8.50');

        $shipment->addLine($line1)->addLine($line2);
        $manager->persist($shipment);

        $shipment2 = (new Shipment())
            ->setReference('REX-20260720-B4')
            ->setClient($clients[1])
            ->setStatus(ShipmentStatus::Confirmed)
            ->setOriginPort('Toamasina')
            ->setDestinationPort('Yokohama')
            ->setDepartureDate(new \DateTimeImmutable('+7 days'))
            ->setNotes('Essential oils + cloves mixed container.');

        $line3 = (new ShipmentLine())
            ->setProduct($products[4])
            ->setQuantity('40.000')
            ->setUnitPrice('180.00');
        $shipment2->addLine($line3);
        $manager->persist($shipment2);

        $manager->flush();
    }
}
