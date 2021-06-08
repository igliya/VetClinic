<?php

namespace App\Controller;

use App\Repository\CheckupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrarController extends AbstractController
{
    /**
     * @Route("/registrar", name="app_registrar")
     */
    public function index(
        CheckupRepository $checkupRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        // формируем запрос на получение списка оплаченных заказов
        $servicesQuery = $checkupRepository->getPaymentPaginationQuery();
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

        return $this->render('checkup/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/registrar/close/{id}", name="checkup_close", methods={"GET","POST"})
     */
    public function checkupClose(
        CheckupRepository $checkupRepository,
        EntityManagerInterface $manager,
        int $id
    ): Response {
        $checkup = $checkupRepository->find($id);
        if ($checkup) {
            $checkup->setStatus('Завершён');
            $manager->persist($checkup);
            $payment = $checkup->getPayment();
            $payment->setStatus('Подтверждён');
            $payment->setRegistrar($this->getUser());
            $manager->persist($payment);
            $manager->flush();
        }

        return $this->redirectToRoute('app_registrar');
    }
}
