<?php

namespace App\Controller;

use App\Entity\CGV;
use App\Form\CgvType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CgvController extends AbstractController
{
    #[Route('/', name: 'app_cgv')]
    public function generateCgv(Request $request)
    {
        $cgv = new CGV();
        $form = $this->createForm(CgvType::class, $cgv);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Générer le texte des CGV en incorporant le nom de l'entreprise
            $companyName = $cgv->getCompanyName();
            // $cgvText = "Conditions Générales de Vente de " . $companyName;

            return $this->render('cgv/generate.html.twig', [
                'companyName' => $companyName,
            ]);
        }

        return $this->render('cgv/generate_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
