<?php


namespace App\Controller;

use App\Filtre;
use App\Form\SortieFilterType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_acceuil")
     */
    public function acceuil(Request $request, SortieRepository $sr, ParticipantRepository $ps)
    {
        $filtre = new Filtre();
        $sortieFilterForm = $this->createForm(SortieFilterType::class, $filtre);
        $sortieFilterForm->handleRequest($request);

        dump($sortieFilterForm->getData());

        $sorties = $sr->filter($sortieFilterForm->getData(), $ps->findAll()[0]);

        return $this->render('main/acceuil.html.twig', [
            "sortieFilterForm" => $sortieFilterForm->createView(),
            "sorties" => $sorties,
            "user" => $ps->findAll()[0]
        ]);
    }
}