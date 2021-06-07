<?php

namespace App\Controller;

use App\Repository\ServiceRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceListController extends AbstractController
{
    /**
     * @Route("/services", name="app_services_list")
     */
    public function index(
        ServiceRepository $serviceRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        // формируем запрос на получение списка услуг
        $servicesQuery = $serviceRepository->getServicesPaginationQuery();
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

        return $this->render('services_list/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
