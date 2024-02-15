<?php

namespace App\Controller;

use App\Entity\Fruit;
use App\Form\FruitType;
use App\Repository\FruitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 */
#[Route('/admin')]
class FruitController extends AbstractController
{
    #[Route('/', name: 'app_fruit_index', methods: ['GET'])]
    public function index(FruitRepository $fruitRepository): Response
    {
        return $this->render('fruit/index.html.twig', [
            'fruits' => $fruitRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_fruit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $fruit = new Fruit();
        $form = $this->createForm(FruitType::class, $fruit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($fruit);
            $entityManager->flush();

            return $this->redirectToRoute('app_fruit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fruit/new.html.twig', [
            'fruit' => $fruit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fruit_show', methods: ['GET'])]
    public function show(Fruit $fruit): Response
    {
        return $this->render('fruit/show.html.twig', [
            'fruit' => $fruit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_fruit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Fruit $fruit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FruitType::class, $fruit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_fruit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fruit/edit.html.twig', [
            'fruit' => $fruit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fruit_delete', methods: ['POST'])]
    public function delete(Request $request, Fruit $fruit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fruit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($fruit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_fruit_index', [], Response::HTTP_SEE_OTHER);
    }
}
