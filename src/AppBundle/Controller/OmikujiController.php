<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Unsei;
use AppBundle\Repository\UnseiRepository;

class OmikujiController extends Controller {
  /**
   * Omikuji
   * @Route("/omikuji/{yourname}", defaults={"yourname" = "YOU"}, name="omikuji")
   * @param Request $request
   * @return Response
   */
  public function omikujiAction(Request $req, $yourname) {
    //$omikuji = ['大吉', '中吉', '小吉', '末吉', '凶'];
    $repo = $this->getDoctrine()->getRepository(Unsei::class);
    $omikuji = $repo->findAll();
    $number = rand(0, count($omikuji) -1);
    //return new Response(
    //  "<html><body> $yourname さんの運勢は $omikuji[$number] です。</body></html>");
    return $this->render('omikuji/omikuji.html.twig', [
      'name' => $yourname,
      'unsei' => $omikuji[$number]
    ]);
  }

  /**
   * @Route("/find")
   */
  public function findAction() {
    $repo = $this->getDoctrine()->getRepository(Unsei::class);
    $unseis = $repo->findAll();
    dump($unseis);
    $one = $repo->find(1);
    dump($one);
    $dai = $repo->findOneBy(['val' => '大吉']);
    dump($dai);
    $daisyou = $repo->findBy(['val' => ['大吉', '小吉']]);
    dump($daisyou);
    $cyu = $repo->findByVal('中吉');
    dump($cyu);
    die;
    return new Response("Dummy");
  }

  /**
   * @Route("/crud")
   */
  public function crudAction() {
    $em = $this->getDoctrine()->getManager();
    // create
    $unsei = new Unsei();
    $unsei->setVal('大凶');
    $em->persist($unsei);
    $em->flush();
    dump($unsei);
    // read
    $repo = $em->getRepository(Unsei::class);
    $daikyou = $repo->findOneByVal('大凶');
    dump($daikyou);
    // update
    $daikyou->setVal('大大吉');
    $em->persist($daikyou);
    $em->flush();
    $daidaikichi = $repo->find($daikyou->getId());
    dump($daidaikichi);
    // delete
    $em->persist($daidaikichi);
    $em->remove($daidaikichi);
    $em->flush();

    $unseis = $repo->findAll();
    dump($unseis);
    foreach ($unseis as $obj) {
      dump($obj->getVal());
    }
    die;
    return new Response("Dummy");
  }

  /**
   * @Route("/dql")
   */
  public function dqlAction() {
    $em = $this->getDoctrine()->getManager();
    $query = $em->createQuery(
      'SELECT u FROM AppBundle:Unsei u WHERE u.val = :val'
    )->setParameter('val', '大吉');
    $unsei = $query->getResult();
    dump($unsei);
    die;
    return new Response("Dummy");
  }

  /**
   * @Route("/qb")
   */
  public function qbAction() {
    $repo = $this->getDoctrine()->getRepository(Unsei::class);
    $query = $repo->createQueryBuilder('u')
      ->where('u.val = :val')
      ->setParameter('val', '大吉')
      ->getQuery();
    $unsei = $query->getResult();
    dump($unsei);
    die;
    return new Response("dummy");
  }
}