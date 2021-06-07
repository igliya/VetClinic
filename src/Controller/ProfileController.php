<?php

namespace App\Controller;

use App\Repository\CheckupRepository;
use App\Repository\PetRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/", name="client_profile")
     */
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }

    /**
     * @Route("/pets", name="client_pets")
     */
    public function pets(
        PetRepository $petRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        // формируем запрос на получение списка питомцев
        $petsQuery = $petRepository->getPetsPaginationQuery($this->getUser()->getClient());
        // получаем номер страницы
        $pageNumber = $request->query->getInt('page', 1);
        // проверяем номер страницы
        if ($pageNumber < 1) {
            $pageNumber = 1;
        }
        // разбиваем на страницы
        $pagination = $paginator->paginate(
            $petsQuery,
            $pageNumber,
            10
        );

        return $this->render('profile/pets.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/checkups", name="client_checkups")
     */
    public function checkups(
        PetRepository $petRepository,
        CheckupRepository $checkupRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        // получаем список питомцев пользователя
        $pets = $petRepository->getPetsByOwner($this->getUser()->getClient());
        $checkupsQuery = $checkupRepository->getCheckupsHistoryPaginationQuery($pets, [
            'Назначен', 'Ожидает оплаты', 'Оплачен',
        ]);
        // получаем номер страницы
        $pageNumber = $request->query->getInt('page', 1);
        // проверяем номер страницы
        if ($pageNumber < 1) {
            $pageNumber = 1;
        }
        // разбиваем на страницы
        $pagination = $paginator->paginate(
            $checkupsQuery,
            $pageNumber,
            10
        );

        return $this->render('profile/checkups.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/checkups/history", name="client_checkups_history")
     */
    public function checkupsHistory(
        PetRepository $petRepository,
        CheckupRepository $checkupRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        // получаем список питомцев пользователя
        $pets = $petRepository->getPetsByOwner($this->getUser()->getClient());
        $checkupsQuery = $checkupRepository->getCheckupsHistoryPaginationQuery($pets, ['Завершён', 'Отменён']);
        // получаем номер страницы
        $pageNumber = $request->query->getInt('page', 1);
        // проверяем номер страницы
        if ($pageNumber < 1) {
            $pageNumber = 1;
        }
        // разбиваем на страницы
        $pagination = $paginator->paginate(
            $checkupsQuery,
            $pageNumber,
            10
        );

        return $this->render('profile/checkups-history.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
