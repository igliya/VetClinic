<?php

namespace App\Controller;

use App\Repository\CheckupRepository;
use App\Repository\ClientRepository;
use App\Repository\PetRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        ClientRepository $clientRepository,
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
        ClientRepository $clientRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        // получаем список питомцев пользователя
        $pets = $petRepository->getPetsPaginationQuery($this->getUser()->getClient())->getResult();
        $pagination = null;
        if ($pets) {
            $checkupsQuery = $checkupRepository->getCheckupsHistoryPaginationQuery($pets, ['Назначен']);
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
        }

        return $this->render('profile/checkups.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/checkups/cancel/{id}", name="checkup_cancel")
     */
    public function checkupsCancel(CheckupRepository $checkupRepository, EntityManagerInterface $manager, int $id): Response {
        $checkup = $checkupRepository->find($id);
        if ($checkup) {
            $checkup->setStatus('Отменён');
            $manager->persist($checkup);
            $manager->flush();
        }

        return $this->redirectToRoute('client_checkups');
    }

    /**
     * @Route("/checkups/history", name="client_checkups_history")
     */
    public function checkupsHistory(
        PetRepository $petRepository,
        CheckupRepository $checkupRepository,
        ClientRepository $clientRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        // получаем список питомцев пользователя
        $pets = $petRepository->getPetsPaginationQuery($this->getUser()->getClient())->getResult();
        $pagination = null;
        if ($pets) {
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
        }

        return $this->render('profile/checkups-history.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
