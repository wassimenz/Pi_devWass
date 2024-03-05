<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\Users;
use App\Repository\UsersRepository;

class HomeController extends AbstractController
{ public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        // Check if the user is authenticated
        if ($this->getUser()) {
                $userRoles = $this->getUser()->getRoles();
                
                // Check if the user has the 'ROLE_ADMIN' role
                if (in_array('ROLE_ADMIN', $userRoles)) {

                    return $this->render('test/homee.html.twig', [
                        'controller_name' => 'HomeController',
                    ]);
                }
           elseif (in_array('ROLE_USER', $userRoles)){
                return $this->render('home/home.html.twig', [
                    'controller_name' => 'HomeController',
                ]);}
            } 
                // Handle the case where the user doesn't have the 'ROLE_ADMIN' role
                // You might redirect them to another page or show an error message
                return $this->redirectToRoute('app_login');
            
        }
        #[Route('/showuser/{id}', name: 'app_showuser')]
    public function show(UsersRepository $userRepository , int $id): Response
    {
        if ($this->getUser()->getId()==$id) {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $userRepository->find($id);
        return $this->render('home/show.html.twig', ['user'=>$user]);
    }
    return new RedirectResponse($this->urlGenerator->generate('app_home'));
}
#[Route('/editpic/{id}', name: 'editpic')]
public function editpic(UsersRepository $userRepository ,Request $request, int $id): Response
{
    $entityManager = $this->getDoctrine()->getManager();
    $user = $entityManager->getRepository(Users::class)->find($id);
    if ($request->isMethod('POST'))  {
        $editfile = $request->files->get('editPicture');
        $filename = md5(uniqid()) . '.' . $editfile->guessExtension();
       // $uploadsDirectory = $this->getParameter('\public\uploads');
       // var_dump($uploadsDirectory).die;
        $editfile->move($this->getParameter('\public\uploads'), $filename);
        //var_dump($editfile).die;
        $user->setPicture($filename);
        $entityManager->flush();
        //var_dump($filename).die;
        return new RedirectResponse($this->urlGenerator->generate('app_showuser', ['id' => $id]));
}
return new RedirectResponse($this->urlGenerator->generate('app_home'));

}
}