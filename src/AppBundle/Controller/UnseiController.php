<?php
namespace AppBundle\Controller;
 
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityManager;
use AppBundle\Form\UnseiType;
use AppBundle\Entity\Unsei;
/**
 * @Route("/unsei")
 */
class UnseiController extends Controller {

  /**
   * @Route("/", name="unsei_index")
   * @Method("GET")
   */
  public function indexAction(Request $req) {
    $repo = $this->getDoctrine()->getRepository(Unsei::class);
    $unseis = $repo->findAll();
    return $this->render('unsei/index.html.twig', [
      'unseis' => $unseis
    ]);
  }

  /**
   * @Route("/new", name="unsei_new")
   * @Method({"GET", "POST"})
   */
  public function newAction(Request $req) {
    $unsei = new Unsei();
    $form = $this->createForm(UnseiType::class, $unsei);
    $form->handleRequest($req);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($unsei);
      $em->flush();
      return $this->redirectToRoute('unsei_index');
    }
    return $this->render('unsei/new.html.twig', [
      'form' => $form->createView()
    ]);
  }

  /**
   * @Route("/{id}/edit", name="unsei_edit")
   * @Method({"GET", "PUT"})
   */
  public function editAction(Request $req, $id) {
    $repo = $this->getDoctrine()->getRepository(Unsei::class);
    $unsei = $repo->find($id);
    if (!$unsei) {
      throw $this->createNotFoundException('No unsei found for id '.$id);
    }
    $form = $this->createForm(UnseiType::class, $unsei, [
      'method' => 'PUT'
    ]);
    $form->handleRequest($req);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->flush();
      return $this->redirectToRoute('unsei_index');
    }
    return $this->render('unsei/edit.html.twig', [
      'form' => $form->createView()
    ]);
  }

  /**
   * @Route("/{id}", name="unsei_delete")
   * @Method("DELETE")
   */
  public function deleteAction(Request $req, $id) {
    $repo = $this->getDoctrine()->getRepository(Unsei::class);
    $unsei = $repo->find($id);
    if (!$unsei) {
      throw $this->createNotFoundException('No unsei found for id '.$id);
    }
    if ($this->isCsrfTokenValid('unsei', $req->get('_token'))) {
      $em = $this->getDoctrine()->getManager();
      $em->remove($unsei);
      $em->flush();
    }
    return $this->redirectToRoute('unsei_index');
  }

  /**
   * @Route("/validate/{name}", defaults={"name" = ""})
   */
  public function validateAction($name) {
    $unsei = new Unsei();
    $unsei->setVal($name);
    $validator = $this->get('validator');
    $errors = $validator->validate($unsei);
    if (count($errors) > 0) {
      return $this->render('unsei/validate.html.twig', [
        'errors' => $errors
      ]);
    }
    return new Response("[{$name}]は正しい運勢です！");
  }
}