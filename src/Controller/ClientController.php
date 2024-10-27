<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Client;
use App\Form\ClientType; // Ajout de l'import de ClientType
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class ClientController extends AbstractController
{
    #[Route('/client', name: 'list_clients')]
    public function index(EntityManagerInterface $em, Request $request, PaginatorInterface $paginator): Response
    {
        // Récupérer tous les clients depuis la base de données
        $queryBuilder = $em->getRepository(Client::class)->createQueryBuilder('c');

        // Pagination
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            8
        );

        // Renvoyer la vue avec la liste des clients
        return $this->render('client/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/client/add', name: 'add_clients')]
    public function addClient(Request $request, EntityManagerInterface $em): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($client);
            $em->flush();

            $this->addFlash('success', 'Client ajouté avec succès.');

            return $this->redirectToRoute('list_clients');
        }

        return $this->render('client/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
