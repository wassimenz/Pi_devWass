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
use DateTime;
use App\Service\EmailService;


class SearchController extends AbstractController
{
   
  
    #[Route('/search', name: 'app_search')]
    public function index(Request $request): Response
    {
        if ($request->isMethod('GET'))  {
    $roleFilter = $request->query->get('roles');
        

        // Get the available sorting options
        $validSearchColumns = ['name', 'email', 'dateofbirth','created_at','numT'];
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
        $users = $userRepository->findByFiltersAndSort($search,$searchBy,$sortBy,$sortOrder);
        $user = $userRepository->birthDay();
        $session = $this->get('session');

if ($session->has('login_time')) {
    $loginTime = $session->get('login_time');
    
    $currentTime = new \DateTime();
    $connectionDuration = $currentTime->diff($loginTime)->format('%H:%I:%S');}
        //var_dump($user).die;
       // var_dump($users).die;
        // Render the user list view with the filtered and sorted users
        return $this->render('search/index.html.twig', [
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
    }}
    #[Route('/mail', name: 'mail')]
    public function sendEmailTest(EmailService $emailService)
    {
        // Appel de la méthode sendEmail du service
        $emailService->sendEmail('adembensalah53@gmail.com', '5ouya salemo3alakom', 'wiiiou');
        //var_dump($emailService).die;
        //$emailService->sendEmail('adem.bensalah@esprit.tn', '5ouya salemo3alakom', 'L MAILING YEMCHI YA ZEBBI ');

        // Vous pouvez retourner une réponse appropriée
        return new Response('Email envoyé avec succès !');
    }

}
