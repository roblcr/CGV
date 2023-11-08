<?php

namespace App\Controller;

use App\Entity\CGV;
use App\Form\CgvType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class CgvController extends AbstractController
{
    #[Route('/', name: 'app_cgv')]
    public function generateCgv(Request $request)
    {
        $cgv = new CGV();
        $form = $this->createForm(CgvType::class, $cgv);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Générer le PDF avec les CGV en incorporant le nom de l'entreprise
            $companyName = $cgv->getCompanyName();

            // Vous pouvez utiliser le même contenu que dans votre modèle Twig "generate.html.twig"
            $htmlContent = $this->renderView('cgv/generate.html.twig', [
                'companyName' => $companyName,
            ]);

            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($htmlContent);

            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $pdfContent = $dompdf->output();

            $response = new Response($pdfContent);
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                'cgv.pdf'
            );
            $response->headers->set('Content-Type', 'application/pdf');
            $response->headers->set('Content-Disposition', $disposition);

            return $response;
        }

        return $this->render('cgv/generate_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
