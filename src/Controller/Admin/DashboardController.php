<?php

namespace App\Controller\Admin;

use App\Entity\AnimalKind;
use App\Entity\Service;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="app_admin")
     */
    public function index(): Response
    {
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();
        return $this->redirect($routeBuilder->setController(ServiceCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('VetClinic - Административная панель')
            ->setFaviconPath('icons/favicon.png');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section();
        yield MenuItem::linkToCrud('Услуги', 'fas fa-list', Service::class);
        yield MenuItem::linkToCrud('Типы животных', 'fas fa-cat', AnimalKind::class);
        yield MenuItem::linkToCrud('Сотрудники', 'fas fa-user-circle', User::class);
        yield MenuItem::linkToUrl(
            'Отчёт по услугам',
            'fas fa-file-pdf',
            $this->generateUrl('report_services', [], true)
        );
        yield MenuItem::linkToUrl(
            'Отчёт по докторам',
            'fas fa-file-pdf',
            $this->generateUrl('report_doctors', [], true)
        );
        yield MenuItem::linkToUrl(
            'Статистика',
            'fa fa-bar-chart',
            "http://practice.igliya.ru"
        );
    }
}
