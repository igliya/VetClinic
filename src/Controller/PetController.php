<?php

namespace App\Controller;

use App\Entity\Pet;
use App\Form\PetType;
use App\Repository\PetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/pet")
 */
class PetController extends AbstractController
{
    /**
     * @Route("/new", name="pet_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $pet = new Pet();
        $pet->setStatus(true);
        $pet->setOwner($this->getUser()->getClient());
        $form = $this->createForm(PetType::class, $pet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pet);
            $entityManager->flush();

            return $this->redirectToRoute('client_pets');
        }

        return $this->render('pet/new.html.twig', [
            'pet' => $pet,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="pet_delete", methods={"GET", "POST"})
     */
    public function delete(PetRepository $petRepository, EntityManagerInterface $manager, int $id): Response
    {
        $pet = $petRepository->find($id);
        if ($pet) {
            $pet->setStatus(false);
            $manager->persist($pet);
            $manager->flush();
        }

        return $this->redirectToRoute('client_pets');
    }
}
