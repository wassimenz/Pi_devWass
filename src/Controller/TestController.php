<?php

namespace App\Controller;
use App\Entity\Users;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\UsersRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Annotation\IsGranted;
use DateTime;
class TestController extends AbstractController
{
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }
    #[Route('/test', name: 'app_test')]
    public function index(UsersRepository $userRepository,Request $request ): Response
        {
    // Check if the user is authenticated
    if ($this->getUser()) {
        $userRoles = $this->getUser()->getRoles();
        
        // Check if the user has the 'ROLE_ADMIN' role
        if (in_array('ROLE_ADMIN', $userRoles)) {
            if ($request->isMethod('GET'))  {
                $roleFilter = $request->query->get('roles');
                    
            
                    // Get the available sorting options
                    $validSearchColumns = ['name', 'email', 'dateofbirth','created_at','numT','Blocked'];
                    $validSortColumns = ['name', 'email', 'dateofbirth','created_at']; // Add more as needed
            
                    // Get the selected sorting option from the request
                    $currentDateTime = new DateTime();
                    $time = $currentDateTime->format('m-d');
                    //var_dump($time).die;
                    $searchBy=$request->query->get('search_by','name');
                    $sortBy = $request->query->get('sort_by', 'dateofbirth');
                    $sortOrder = $request->query->get('sort_order', 'asc');
                    $search = $request->query->get('search',"");
            
                    // Use these parameters to query users from the database
                    $userRepository = $this->getDoctrine()->getRepository(Users::class);
                    if($searchBy=='Blocked'){
                        $users = $userRepository->Blocked();
                    }else{
                    $users = $userRepository->findByFiltersAndSort($search,$searchBy,$sortBy,$sortOrder);}
                    $user = $userRepository->birthDay();
                    $session = $this->get('session');
                    
            if ($session->has('login_time')) {
                $loginTime = $session->get('login_time');
                
                $currentTime = new \DateTime();
                $connectionDuration = $currentTime->diff($loginTime)->format('%H:%I:%S');}
                    //var_dump($user).die;
                   // var_dump($users).die;
                    // Render the user list view with the filtered and sorted users
                    return $this->render('test/index.html.twig', [
                        'users' => $users,
                        'user'  =>$user,
                        'sortColumns' => $validSortColumns,
                        'searchColumns' => $validSearchColumns,
                        'search' => $search,
                        'selectedSearchColumn' => $searchBy,
                        'selectedSortColumn' => $sortBy,
                        'selectedSortOrder' => $sortOrder,
                        'a' => $connectionDuration
                    ]);
                };
        } elseif (in_array('ROLE_USER', $userRoles)) {
            return new RedirectResponse($this->urlGenerator->generate('app_home'));
        } 
         
        }
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
       
    }
    #[Route('/show/{id}', name: 'app_show')]
    #[IsGranted("ROLE_ADMIN")]
    public function show(UsersRepository $userRepository , int $id): Response
    {
        if ($this->getUser()) {
            $userRoles = $this->getUser()->getRoles();
            
            // Check if the user has the 'ROLE_ADMIN' role
            if (in_array('ROLE_ADMIN', $userRoles)) {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $userRepository->find($id);
        return $this->render('test/show.html.twig', ['user'=>$user]);}
        elseif (in_array('ROLE_USER', $userRoles)) {
            return new RedirectResponse($this->urlGenerator->generate('app_home'));
        } 
         
        }
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }
    #[Route('/edituser/{id}', name: 'edituser')]
    public function editUser(Request $request,UserPasswordHasherInterface $userPasswordHasher, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(Users::class)->find($id);
        if ($request->isMethod('POST')) {
            // Get the value of the 'editUsername' field
            $editUsername = $request->request->get('editUsername');
            $editLastname = $request->request->get('editLastname');
            $editEmail = $request->request->get('editEmail');
            $editPhone = $request->request->get('editPhone');
            $editDate = $request->request->get('editDate');
            $editDate = DateTime::createFromFormat("Y-m-d",$editDate);
            $editPassword = $request->request->get('editPassword');
            //var_dump($editPassword).die;
            if($editPassword!==""){
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $editPassword
                    )
                );
            }
            $user->setNumT($editPhone);
            $user->setDateofbirth($editDate);
            $user->setName($editUsername);
            $user->setLastname($editLastname);
            $user->setEmail($editEmail);
           // var_dump($user).die;
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_test');
        }
            // Now $editUsername contains the value entered in the input field
            // You can use it as needed in your controller logic
        

        return $this->render('test/edituser.html.twig', [
            'editForm' => $editForm->createView(),
            'user' => $user,
        ]);
    }
    #[Route('/editpica/{id}', name: 'editpica')]
    public function editpica(UsersRepository $userRepository ,Request $request, int $id): Response
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
            return new RedirectResponse($this->urlGenerator->generate('app_show', ['id' => $id]));
    }
    return new RedirectResponse($this->urlGenerator->generate('app_home'));
    
    }
    
#[Route('/deleteuser/{id}', name: 'deleteuser')]

public function deleteuser(
    $id,
    UsersRepository $userRepository,
    ManagerRegistry $managerRegistry
): Response {
    if ($this->getUser()) {
        $userRoles = $this->getUser()->getRoles();
        
        // Check if the user has the 'ROLE_ADMIN' role
        if (in_array('ROLE_ADMIN', $userRoles)) {
            $em = $managerRegistry->getManager();
            $dataid = $userRepository->find($id);
            $em->remove($dataid);
            $em->flush();
            return $this->redirectToRoute('app_test');}
    elseif (in_array('ROLE_USER', $userRoles)) {
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    } 
     
    }
    return new RedirectResponse($this->urlGenerator->generate('app_login'));
}
#[Route('/blockuser/{id}', name: 'blockuser')]

public function blockuser(
    $id,
    UsersRepository $userRepository,
    ManagerRegistry $managerRegistry
): Response {
    if ($this->getUser()) {
        $userRoles = $this->getUser()->getRoles();
        
        // Check if the user has the 'ROLE_ADMIN' role
        if (in_array('ROLE_ADMIN', $userRoles)) {
            $em = $managerRegistry->getManager();
            $dataid = $userRepository->find($id);
            $dataid->setBlockunitl(new \DateTime('+24 hour'));
            $em->flush();
            return $this->redirectToRoute('app_test');}
    elseif (in_array('ROLE_USER', $userRoles)) {
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    } 
     
    }
    return new RedirectResponse($this->urlGenerator->generate('app_login'));
}



    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
    
   
}