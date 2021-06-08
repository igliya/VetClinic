<?php

namespace App\Controller;

use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/report")
 */
class ReportController extends AbstractController
{
    /**
     * @Route("/services", name="report_services")
     */
    public function reportServices(ServiceRepository $serviceRepository, Pdf $knpSnappyPdf)
    {
        $services = $serviceRepository->getServicesPaginationQuery()->getResult();
        $html = $this->renderView('report/services.html.twig', [
            'services' => $services,
        ]);

        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            'Services.pdf'
        );
    }

    /**
     * @Route("/doctors", name="report_doctors")
     */
    public function reportDoctors(UserRepository $userRepository, Pdf $knpSnappyPdf)
    {
        $doctors = $userRepository->getDoctors();
        $html = $this->renderView('report/doctors.html.twig', [
            'doctors' => $doctors,
        ]);

        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            'Doctors.pdf'
        );
    }
}
