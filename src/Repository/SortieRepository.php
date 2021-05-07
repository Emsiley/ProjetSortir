<?php

  namespace App\Repository;

  use App\Entity\Participant;
  use App\Entity\Sortie;
  use App\Filtre;
  use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
  use Doctrine\Persistence\ManagerRegistry;
  use PhpParser\Node\Stmt\ElseIf_;
  use Symfony\Component\Form\FormInterface;

  /**
   * @method Sortie|null find( $id, $lockMode = null, $lockVersion = null )
   * @method Sortie|null findOneBy( array $criteria, array $orderBy = null )
   * @method Sortie[]    findAll()
   * @method Sortie[]    findBy( array $criteria, array $orderBy = null, $limit = null, $offset = null )
   */
  class SortieRepository extends ServiceEntityRepository
  {
    public function __construct( ManagerRegistry $registry )
    {
      parent::__construct( $registry, Sortie::class );
    }

    /**
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function filter( Filtre $form, Participant $utilisateurCourant )
    {
      $campus       = $form->getCampus();
      $nom          = $form->getNom();
      $dateDebut    = $form->getDateDebut();
      $dateFin      = $form->getDateFin();
      $organisateur = $form->getOrganisateur();
      $inscrit      = $form->getInscrit();
      $nonInscrit   = $form->getNonInscrit();
      $expire       = $form->getExpire();

      //dump( $organisateur );

      $qb = $this->createQueryBuilder( 's' );

      if( $organisateur )
      {
        $qb->orWhere( 's.organisateur = :utilisateurCourant' )->setParameter( 'utilisateurCourant', $utilisateurCourant );
      }

      if( $inscrit )
      {
        $qb->orWhere( ':utilisateurCourant MEMBER OF s.participants' )->setParameter( 'utilisateurCourant', $utilisateurCourant );
      }

      if( $nonInscrit )
      {
        $qb->orWhere( ':utilisateurCourant NOT MEMBER OF s.participants' )->setParameter( 'utilisateurCourant', $utilisateurCourant );
      }

      if( $nom != null )
      {
        $qb->andWhere( 's.nom LIKE :val' )->setParameter( 'val', "%$nom%" );
      }

      if( $campus != null )
      {
        $qb->andWhere( 's.campus = :campus' )->setParameter( 'campus', $campus );
      }

      if( $dateDebut != null )
      {
        $qb->andWhere( ':dateDebut <= s.dateHeureDebut' )->setParameter( 'dateDebut', $dateDebut );
      }

      if( $dateFin != null )
      {
        $qb->andWhere( 's.dateHeureDebut <= :dateFin' )->setParameter( 'dateFin', $dateFin );
      }

      if( $expire )
      {
        $qb->andWhere( 's.dateLimiteInscription < :now' )->setParameter( 'now', new \DateTime( "now" ) );
      }

      // TODO Campus
      return $qb->getQuery()->getResult();
    }

    /*
     * public function filterDate(FormInterface $form)
    {
        $champs = $form->all();
        $Date = $champs['dateHeureDebut']->getViewData();

        return $this->createQueryBuilder('dhd')
            ->where('dhd.dateHeureDebut < :val')
            ->setParameter('val', "dateLimiteInscription")
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
  }
