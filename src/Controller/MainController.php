<?php


namespace App\Controller;

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
        $sortieFilterForm = $this->createForm(SortieFilterType::class);
        $sortieFilterForm->handleRequest($request);
        $sorties = $sr->findAll();

        if($sortieFilterForm->isSubmitted()) {
            dump($sortieFilterForm->all());
//            $sr->filter($sortieFilterForm->all());
        }

        return $this->render('main/acceuil.html.twig', [
            "sortieFilterForm" => $sortieFilterForm->createView(),
            "sorties" => $sorties,
            "user" => $ps->findAll()[0]
        ]);
    }
}