<?php

namespace App\Controller;

use App\Entity\Checkup;
use App\Entity\Payment;
use App\Form\CheckupType;
use App\Repository\CheckupRepository;
use App\Service\PublisherAMQP;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/checkup")
 */
class CheckupController extends AbstractController
{
    /**
     * @Route("/new", name="checkup_new", methods={"GET","POST"})
     */
    public function new(Request $request, PublisherAMQP $publisherAMQP): Response
    {
        $checkup = new Checkup();
        $checkup->setStatus('Назначен');
        $form = $this->createForm(CheckupType::class, $checkup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($checkup);
            $entityManager->flush();

            $checkupDto[] = [
                'id' => $checkup->getId(),
                'client_name' => $checkup->getPet()->getOwner()->getAccount()->getFullName(),
                'pet_name' => $checkup->getPet()->getName(),
                'pet_kind' => $checkup->getPet()->getKind()->getName(),
                'pet_sex' => $checkup->getPet()->getSex() ? 'Мужской' : 'Женский',
                'checkup_date' => $checkup->getDate()->format('c'),
            ];
            $amqpMessage = [
                'action' => 'add',
                'payload' => $checkupDto,
            ];
            $publisherAMQP->publishMessage(json_encode($amqpMessage));

            return $this->redirectToRoute('client_checkups');
        }

        return $this->render('checkup/new.html.twig', [
            'checkup' => $checkup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/pay/{id}", name="checkup_pay", methods={"GET","POST"})
     */
    public function checkupPay(
        CheckupRepository $checkupRepository,
        EntityManagerInterface $manager,
        int $id
    ): Response {
        $checkup = $checkupRepository->find($id);
        if ($checkup) {
            $checkup->setStatus('Оплачен');
            $manager->persist($checkup);
            $payment = new Payment();
            $payment->setCheckup($checkup);
            $payment->setClient($checkup->getPet()->getOwner());
            $payment->setSum($checkup->calculateSum());
            $payment->setDate(new \DateTime());
            $payment->setStatus('Ожидает подтверждения');
            $manager->persist($payment);
            $manager->flush();
        }

        return $this->redirectToRoute('client_checkups');
    }

    /**
     * @Route("/cancel/{id}", name="checkup_cancel", methods={"GET","POST"})
     */
    public function checkupCancel(
        PublisherAMQP $publisherAMQP,
        CheckupRepository $checkupRepository,
        EntityManagerInterface $manager,
        int $id
    ): Response {
        $checkup = $checkupRepository->find($id);
        if ($checkup) {
            $checkup->setStatus('Отменён');
            $manager->persist($checkup);
            $manager->flush();

            $amqpMessage = [
                'action' => 'cancel',
                'id' => $checkup->getId(),
            ];
            $publisherAMQP->publishMessage(json_encode($amqpMessage));
        }

        return $this->redirectToRoute('client_checkups');
    }
}
