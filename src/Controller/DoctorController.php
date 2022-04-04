<?php

namespace App\Controller;

use App\Entity\Checkup;
use App\Form\CheckupEditType;
use App\Repository\CheckupRepository;
use App\Service\PublisherAMQP;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DoctorController extends AbstractController
{
    /**
     * @Route("/doctor", name="app_doctor")
     */
    public function index(
        CheckupRepository $checkupRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        // формируем запрос на получение списка назначенных приёмов
        $servicesQuery = $checkupRepository->getCheckupsDoctorPaginationQuery($this->getUser());
        // получаем номер страницы
        $pageNumber = $request->query->getInt('page', 1);
        // проверяем номер страницы
        if ($pageNumber < 1) {
            $pageNumber = 1;
        }
        // разбиваем на страницы
        $pagination = $paginator->paginate(
            $servicesQuery,
            $pageNumber,
            10
        );

        return $this->render('checkup/doctor.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/doctor/checkup/{id}/edit", name="checkup_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Checkup $checkup, PublisherAMQP $publisherAMQP): Response
    {
        $form = $this->createForm(CheckupEditType::class, $checkup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $checkup->setStatus('Ожидает оплаты');
            $this->getDoctrine()->getManager()->flush();

            $amqpMessage = [
                'action' => 'end',
                'id' => $checkup->getId(),
                'date' => $checkup->getDate()->format('c'),
            ];
            // $publisherAMQP->publishMessage(json_encode($amqpMessage));

            return $this->redirectToRoute('app_doctor');
        }

        return $this->render('checkup/edit.html.twig', [
            'checkup' => $checkup,
            'form' => $form->createView(),
        ]);
    }
}
