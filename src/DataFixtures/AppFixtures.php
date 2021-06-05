<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->passwordEncoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // создаём клиента
        $user1 = new User();
        $user1->setLogin('client1');
        $user1->setPassword($this->passwordEncoder->encodePassword($user1, 'Client1!123'));
        $user1->setLastName('Иванов');
        $user1->setFirstName('Иван');
        $user1->setPatronymic('Иванович');
        $user1->setPhone('+7(904)123-4567');
        $manager->persist($user1);
        $client = new Client();
        $client->setAddress('г. Липецк, ул. Московская, д.30');
        $client->setPassport('4213123456');
        $client->setAccount($user1);
        $manager->persist($client);

        // создаём врача
        $user2 = new User();
        $user2->setLogin('doctor1');
        $user2->setPassword($this->passwordEncoder->encodePassword($user2, 'Doctor1!123'));
        $user2->setLastName('Петров');
        $user2->setFirstName('Пётр');
        $user2->setPatronymic('Петрович');
        $user2->setPhone('+7(904)456-7890');
        $user2->setRoles(['ROLE_DOCTOR']);
        $manager->persist($user2);

        // создаём регистратора
        $user3 = new User();
        $user3->setLogin('registrar1');
        $user3->setPassword($this->passwordEncoder->encodePassword($user3, 'Registrar1!123'));
        $user3->setLastName('Фёдоров');
        $user3->setFirstName('Фёдор');
        $user3->setPatronymic('Фёдорович');
        $user3->setPhone('+7(904)980-1234');
        $user3->setRoles(['ROLE_REGISTRAR']);
        $manager->persist($user3);
        $manager->flush();
    }
}
