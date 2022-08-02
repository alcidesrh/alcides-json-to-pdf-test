<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Snappy\Pdf;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;

class AppController extends AbstractController
{

    #[Route('/', name: 'app_app')]
    public function index(Pdf $knpSnappyPdf): Response
    {

        if (isset($_POST["submit"])) {

            $file = $_FILES["fileJson"]["tmp_name"];
            $strJsonFileContents = file_get_contents($file);
            $array = json_decode($strJsonFileContents, true)['Blocks'];

            $data = array_map(
                fn ($value) => $value['Text'],
                array_values(
                    array_filter(
                        $array,
                        fn ($value) => isset($value['BlockType']) && $value['BlockType'] == 'LINE'
                    )
                )
            );

            // return $this->render('app/pdf.html.twig', ['data' => $data]);

            $html = $this->renderView(
                'app/pdf.html.twig',
                [
                    'data' => $data,
                ]
            );

            return new PdfResponse(
                $knpSnappyPdf->getOutputFromHtml($html),
                'file.pdf'
            );
        }

        return $this->render('app/index.html.twig');
    }
}
