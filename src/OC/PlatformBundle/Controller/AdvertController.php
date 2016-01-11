<?php

namespace OC\PlatformBundle\Controller;

//use OC\PlatformBundle\Entity\AdvertSkill;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Form\AdvertType;
use OC\PlatformBundle\Form\AdvertEditType;
//use OC\PlatformBundle\Entity\Image;
//use OC\PlatformBundle\Entity\Application;


class AdvertController extends Controller
{
 
    //**********************************************************
    //****INDEX*************************************************
    //**********************************************************
    
    
   public function indexAction($page)
    {
      if ($page < 1) {
        throw $this->createNotFoundException("La page ".$page." n'existe pas.");
      }
      
      $nbPerPage = 3;

      // Pour récupérer la liste de toutes les annonces : on utilise findAll()
      $listAdverts = $this->getDoctrine()
        ->getManager()
        ->getRepository('OCPlatformBundle:Advert')
        ->getAdverts($page,$nbPerPage)
      ;

      $nbPages = ceil(count($listAdverts)/$nbPerPage);

       // Si la page n'existe pas, on retourne une 404
       if ($page > $nbPages) {
         throw $this->createNotFoundException("La page ".$page." n'existe pas.");
       }

       // On donne toutes les informations nécessaires à la vue
       return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
         'listAdverts' => $listAdverts,
         'nbPages'     => $nbPages,
         'page'        => $page
       ));
    }
    
    //**********************************************************
    //****VIEW*************************************************
    //**********************************************************
    
   public function viewAction($id)
    {
      // On récupère l'EntityManager
      $em = $this->getDoctrine()->getManager();

      // Pour récupérer une annonce unique : on utilise find()
      $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

      // On vérifie que l'annonce avec cet id existe bien
      if ($advert === null) {
        throw $this->createNotFoundException("L'annonce d'id ".$id." n'existe pas.");
      }

      // On récupère la liste des advertSkill pour l'annonce $advert
      $listAdvertSkills = $em->getRepository('OCPlatformBundle:AdvertSkill')->findByAdvert($advert);

      // Puis modifiez la ligne du render comme ceci, pour prendre en compte les variables :
      return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
        'advert'           => $advert,
        'listAdvertSkills' => $listAdvertSkills,
      ));
    }
    
    
    //**********************************************************
    //****ADD*************************************************
    //**********************************************************
    
    public function addAction(Request $request)
    {
//      // La gestion d'un formulaire est particulière, mais l'idée est la suivante :
//
//      if ($request->isMethod('POST')) {
//        // Ici, on s'occupera de la création et de la gestion du formulaire
//
//        $request->getSession()->getFlashBag()->add('info', 'Annonce bien enregistrée.');
//        
//        
//        // Puis on redirige vers la page de visualisation de cet article
//        return $this->redirect($this->generateUrl('oc_platform_view', array('id' => 1)));
//      }
//
//      // Si on n'est pas en POST, alors on affiche le formulaire
//      return $this->render('OCPlatformBundle:Advert:add.html.twig');
      
          // On crée un objet Advert
            $advert = new Advert();
            $form = $this->createForm(new AdvertType(), $advert);

            if ($form->handleRequest($request)->isValid()) {    
              $em = $this->getDoctrine()->getManager();
              $em->persist($advert);
              $em->flush();

              $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

              return $this->redirect($this->generateUrl('oc_platform_view', array('id' => $advert->getId())));
            }

            return $this->render('OCPlatformBundle:Advert:add.html.twig', array(
              'form' => $form->createView(),
              'advert' => $advert
            ));

    }
    
    
    //**********************************************************
    //****EDIT*************************************************
    //**********************************************************
    
    public function editAction($id, Request $request)
    {
      // On récupère l'EntityManager
      $em = $this->getDoctrine()->getManager();

      // On récupère l'entité correspondant à l'id $id
      $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

      // Si l'annonce n'existe pas, on affiche une erreur 404
      if ($advert == null) {
        throw $this->createNotFoundException("L'annonce d'id ".$id." n'existe pas.");
      }
      
      $form = $this->createForm(new AdvertEditType(), $advert);

        if ($form->handleRequest($request)->isValid()) {
          // Inutile de persister ici, Doctrine connait déjà notre annonce
          $em->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

          return $this->redirect($this->generateUrl('oc_platform_view', array('id' => $advert->getId())));
        }

      // Ici, on s'occupera de la création et de la gestion du formulaire

      return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
        'form'   => $form->createView(),
        'advert' => $advert
      ));
    }
    
    //**********************************************************
    //****EDIT IMAGE*************************************************
    //**********************************************************
    
//    public function editImageAction($advertId)
//    {
//      $em = $this->getDoctrine()->getManager();
//
//      // On récupère l'annonce
//      $advert = $em->getRepository('OCPlatformBundle:Advert')->find($advertId);
//
//      // On modifie l'URL de l'image par exemple
//      $advert->getImage()->setUrl('test.png');
//
//      // On n'a pas besoin de persister l'annonce ni l'image.
//      // Rappelez-vous, ces entités sont automatiquement persistées car
//      // on les a récupérées depuis Doctrine lui-même
//
//      // On déclenche la modification
//      $em->flush();
//
//      return new Response('OK');
//    }
//    
    
    //**********************************************************
    //****DELETE*************************************************
    //**********************************************************
    public function deleteAction($id, Request $request)
    {
      // On récupère l'EntityManager
      $em = $this->getDoctrine()->getManager();

      // On récupère l'entité correspondant à l'id $id
      $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

      // Si l'annonce n'existe pas, on affiche une erreur 404
      if ($advert == null) {
        throw $this->createNotFoundException("L'annonce d'id ".$id." n'existe pas.");
      }
      
        // On crée un formulaire vide, qui ne contiendra que le champ CSRF
        // Cela permet de protéger la suppression d'annonce contre cette faille
        $form = $this->createFormBuilder()->getForm();

        if ($form->handleRequest($request)->isValid()) {
          $em->remove($advert);
          $em->flush();

          $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");

          return $this->redirect($this->generateUrl('oc_platform_home'));
        }

      // Si la requête est en GET, on affiche une page de confirmation avant de delete
      return $this->render('OCPlatformBundle:Advert:delete.html.twig', array(
        'advert' => $advert,
         'form'=> $form->createView()
      ));
    }
    
    //**********************************************************
    //***MENU*************************************************
    //**********************************************************
    
    public function menuAction($limit = 3)
    {
      $listAdverts = $this->getDoctrine()
        ->getManager()
        ->getRepository('OCPlatformBundle:Advert')
        ->findBy(
          array(),                 // Pas de critère
          array('date' => 'desc'), // On trie par date décroissante
          $limit,                  // On sélectionne $limit annonces
          0                        // À partir du premier
      );

      return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
        'listAdverts' => $listAdverts
      ));
    }
    
     public function translationAction($name)
    {
      return $this->render('OCPlatformBundle:Blog:translation.html.twig', array(
        'name' => $name
      ));
    }
}

