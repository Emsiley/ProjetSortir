<?php

  namespace App\Controller;

  use App\Entity\Sortie;
  use App\Filtre;
  use App\Form\SortieFilterType;
  use App\Repository\ParticipantRepository;
  use App\Repository\SortieRepository;
  use App\Repository\CampusRepository;
  use App\Repository\VilleRepository;
  use App\Repository\LieuRepository;
  use App\Repository\EtatRepository;
  use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\Routing\Annotation\Route;

  class MainController extends AbstractController
  {
    /**
     * @Route("/", name="main_home")
     */
    public function home( Request $request, SortieRepository $sortieRepository, ParticipantRepository $participantRepository )
    {
      $sortieFilterForm = $this->createForm( SortieFilterType::class, new Filtre() );
      $sortieFilterForm->add( 'Rechercher', SubmitType::class, [ 'attr' => [ 'class' => 'w-100' ] ] );

      $sortieFilterForm->handleRequest( $request );

      //dd($sortieFilterForm->getData());

      // TODO : Remplacer par l'utilisateur connecté plutot que le premier trouvé en BDD
      $currentUser = $participantRepository->findAll()[0];
      $sorties     = $sortieRepository->filter( $sortieFilterForm->getData(), $currentUser );

      //var_dump( $sorties );

      return $this->render( 'main/home.html.twig', [
        "sortieFilterForm" => $sortieFilterForm->createView(),
        "sorties"          => $sorties,
        "user"             => $currentUser
      ] );
    }

    /**
     * @Route("/create", name="main_create", methods={"GET"})
     */
    public function create( Request $request, SortieRepository $sortieRepository, ParticipantRepository $participantRepository, VilleRepository $villeRepository, CampusRepository $campusRepository, LieuRepository $lieuRepository )
    {
      $currentUser = $participantRepository->findAll()[0];
      return $this->render( 'main/create.html.twig', [
        "user"   => $currentUser,
        "villes" => $villeRepository->findAll(),
        "campus" => $campusRepository->findAll(),
        "lieux"  => $lieuRepository->findAll(),
        "url"    => $this->generateUrl( 'main_create_post' ),
      ] );
    }

    /**
     * @Route("/create", name="main_create_post", methods={"POST"})
     */
    public function createPost( Request $request, ParticipantRepository $participantRepository, CampusRepository $campusRepository, LieuRepository $lieuRepository, EtatRepository $etatRepository )
    {
      //dd( $request );

      $entityManager = $this->getDoctrine()->getManager();

      $currentUser = $participantRepository->findAll()[0];

      // TODO : Vérification des données soumises

      $sortie = new Sortie();
      $sortie->setNom( $request->request->get( "nom", "Sortie test" ) );
      $sortie->setDateHeureDebut( new \DateTime( $request->request->get( "dateHeureDebut", "2021-01-01 00:00" ) ) );
      $sortie->setDuree( (int)$request->request->get( "duree", 10 ) );
      $sortie->setDateLimiteInscription( new \DateTime( $request->request->get( "dateLimiteInscription", "2022-01-01 00:00" ) ) );
      $sortie->setNbInscriptionMax( (int)$request->request->get( "nbInscriptionMax", 100 ) );
      $sortie->setInfosSortie( $request->request->get( "infosSortie", "Infos de sortie par défaut" ) );

      // TODO : Gérer la ville dans l'entité Sortie
      //$sortie->setVille( $villeRepository->find( $request->request->get( "ville", 0 ) ) );

      $sortie->setCampus( $campusRepository->find( $request->request->get( "campus", 0 ) ) );
      $sortie->setLieu( $lieuRepository->find( $request->request->get( "lieu", 0 ) ) );
      $sortie->setOrganisateur( $currentUser );

      // TODO : Mettre le bon état
      $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Ouverte']));

      // Sauvegarde BDD
      try
      {
        $entityManager->persist( $sortie );
        $entityManager->flush();
        return $this->render( "main/create.html.twig", [
          "msg"      => "Sortie ajoutée avec succès !",
          "previous" => $this->generateUrl( 'main_home' ),
        ] );
      }
      catch( \Doctrine\ORM\ORMException $e )
      {
        return $this->render( 'main/create.html.twig', [
          "error" => "Une erreur est survenue lors de la sauvegarde ! (${ $e })",
        ] );
      }
    }

    /**
     * @Route("/delete", name="main_delete")
     */
    public function delete( Request $request, SortieRepository $sr, ParticipantRepository $ps )
    {
      $sortieFilterForm = $this->createForm( SortieFilterType::class, new Filtre() );
      $sortieFilterForm->handleRequest( $request );

      //var_dump($request->get( "id" ) );

      $currentUser    = $ps->findAll()[0];
      $sortieToDelete = $sr->find( $request->get( "id" ) );

      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->remove( $sortieToDelete );
      $entityManager->flush();

      return $this->render( 'main/delete.html.twig', [
        "sortieToDelete" => $sortieToDelete,
        "previous"       => $this->generateUrl( 'main_home' ),
        "user"           => $currentUser
      ] );
    }
  }
