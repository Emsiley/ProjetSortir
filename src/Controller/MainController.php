<?php


namespace App\Controller;

use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_acceuil")
     */
    public function acceuil(SortieRepository $sr)
    {
        return $this->render('main/acceuil.html.twig', [
            "sorties" => $sr->findAll()
        ]);
    }
}