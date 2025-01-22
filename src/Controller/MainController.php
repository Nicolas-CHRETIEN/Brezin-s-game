<?php

namespace App\Controller;

use App\Entity\Situation;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class MainController extends AbstractController
{
    /**
     * render the home page.
     * 
     * @Route("/", name="app_home")
     * 
     * @return Response
    */
    public function renderHome()
    {
        return $this->render('home/home.html.twig');
    }

    /**
     * render the rules page.
     * 
     * @Route("/rules", name="app_rules")
     * 
     * @return Response
    */
    public function renderRules()
    {
        return $this->render('rules/rules.html.twig');
    }

    /**
     * render the rules page with situation saved.
     * 
     * @Route("/rules/save/{id}", name="app_rules_save")
     * 
     * @param int $id Situation's id.
     * @return Response
    */
    public function renderRulesWithSave($id)
    {
        $Situation_repository = $this->getDoctrine()->getRepository(Situation::class);
        $Situation = $Situation_repository->findOneById($id);

        if ($Situation->getStage() === 'showResultGame') { // render the result menu if needed insted of the game template.
            return $this->render('rules/rules.html.twig', ['save' => $id, 'result' => true]);
        }

        return $this->render('rules/rules.html.twig', ['save' => $id]);
    }

    /**
     * render the knowMore page.
     * 
     * @Route("/knowMore", name="app_know_more")
     * 
     * @return Response
    */
    public function renderKnowMore()
    {
        return $this->render('knowMore/knowMore.html.twig');
    }

    /**
     * render the contact page.
     * 
     * @Route("/contact", name="app_contact")
     * 
     * @return Response
    */
    public function renderContact()
    {
        return $this->render('contact/contact.html.twig');
    }
}
