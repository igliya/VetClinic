<?php

namespace App\DataFixtures;

use App\Entity\AnimalKind;
use App\Entity\Checkup;
use App\Entity\Client;
use App\Entity\Payment;
use App\Entity\Pet;
use App\Entity\Service;
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
        $user1->setRoles(['ROLE_CLIENT']);
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

        // создаём администратора
        $admin = new User();
        $admin->setLogin('admin');
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, 'Admin!123'));
        $admin->setLastName('Администратор');
        $admin->setFirstName('');
        $admin->setPatronymic('');
        $admin->setPhone('+7(900)123-1010');
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        // создаём список услуг
        // услуга 1
        $service1 = new Service();
        $service1->setName('Первичный клинический осмотр');
        $service1->setStatus(true);
        $service1->setPrice(145);
        $manager->persist($service1);
        // услуга 2
        $service2 = new Service();
        $service2->setName('Внутривенное введение лекарственных препаратов');
        $service2->setStatus(true);
        $service2->setPrice(96);
        $manager->persist($service2);
        // услуга 3
        $service3 = new Service();
        $service3->setName('Вправление вывиха');
        $service3->setStatus(true);
        $service3->setPrice(540);
        $manager->persist($service3);
        // остальные услуги
        $services = [
            ['Консультация ветспециалиста', true, 160],
            ['Повторный клинический осмотр', true, 110],
            ['Вакцинация животного с проведением клинического осмотра', true, 574],
            ['Внутривенное капельное введение препаратов', true, 200],
            ['Взятие проб крови из вены', true, 230],
            ['Взятие проб крови из капилляра', true, 121],
            ['Взятие соскобов, мазков', true, 96],
            ['Общий наркоз для проведения оперативных вмешательств', true, 400],
            ['Оперативное вмешательство ', true, 2000],
            ['Кастрация, стерилизация ', true, 1500],
            ['Купирование ушных раковин у собак', true, 500],
            ['Наложение гипсовой повязки', true, 200],
            ['Снятие гипсовой повязки', true, 100],
            ['Снятие швов', true, 100],
            ['Обработка послеоперационного шва', true, 150],
            ['Обработка ран', true, 130],
            ['Перевязка ран', true, 120],
            ['Общий анализ крови', true, 150],
            ['Биохимическое исследование крови', true, 180],
            ['Исследование на кровепаразитарные болезки', true, 300],
            ['Гельминтокопрологические исследования', true, 160],
            ['Ультразвуковое исследование', true, 600],
            ['Повторное ультразвуковое исследование', true, 350],
            ['Груминг кошек', true, 700],
            ['Груминг собак', true, 900],
            ['Обрезка когтей', true, 150],
            ['Удаление иксодовых клещей', true, 90],
            ['Рентген', true, 300],
            ];
        foreach ($services as $data) {
            $service = new Service();
            $service->setName($data[0]);
            $service->setStatus($data[1]);
            $service->setPrice($data[2]);
            $manager->persist($service);
        }

        // создаём список видов животых
        $kinds = ['Собака', 'Грызун', 'Рептилия'];
        // создаём вид кошки
        $catKind = new AnimalKind();
        $catKind->setName('Кошка');
        $catKind->setStatus(true);
        $manager->persist($catKind);
        foreach ($kinds as $data) {
            $kind = new AnimalKind();
            $kind->setName($data);
            $kind->setStatus(true);
            $manager->persist($kind);
        }

        // создаём список питомцев
        $pet1 = new Pet();
        $pet1->setOwner($client);
        $pet1->setName('Кузя');
        $pet1->setKind($catKind);
        $pet1->setBirthday(new \DateTime('25-09-2018'));
        $pet1->setSex(false);
        $pet1->setStatus(true);
        $manager->persist($pet1);
        $pet2 = new Pet();
        $pet2->setOwner($client);
        $pet2->setName('Мурка');
        $pet2->setKind($catKind);
        $pet2->setBirthday(new \DateTime('22-01-2019'));
        $pet2->setSex(false);
        $pet2->setStatus(true);
        $manager->persist($pet2);

        // создаём приём
        $checkup = new Checkup();
        $checkup->setPet($pet1);
        $checkup->setDoctor($user2);
        $checkup->setDate(new \DateTime('01-05-2021 15:00'));
        $checkup->setComplaints('Поджимает правую переднюю лапу');
        $checkup->setDiagnosis('Травматический вывих коленной чашечки');
        $checkup->setTreatment('Первичный клинический осмотр, правление вывиха, укол обезболивающего');
        $checkup->addService($service1);
        $checkup->addService($service2);
        $checkup->addService($service3);
        $checkup->setStatus('Завершён');
        $manager->persist($checkup);

        // создаём счёт
        $payment = new Payment();
        $payment->setCheckup($checkup);
        $payment->setClient($checkup->getPet()->getOwner());
        $sum = 0;
        foreach ($checkup->getServices() as $service) {
            $sum += $service->getPrice();
        }
        $payment->setSum($sum);
        $payment->setDate(new \DateTime('01-05-2021 16:00'));
        $payment->setRegistrar($user3);
        $payment->setStatus('Подтверждён');
        $manager->persist($payment);
        $manager->flush();
    }
}
