<?php

namespace App\Service;

use App\Entity\Guest;
use App\Entity\Wedding;
use Knp\Snappy\Image;
use Knp\Snappy\Pdf;
use Twig\Environment;

class GeneratorService
{
    private Pdf $pdf;
    private Environment $twig;
    private Image $image;

    public function __construct(Pdf $pdf, Environment $twig, Image $image)
    {
        $this->pdf = $pdf;
        $this->twig = $twig;
        $this->image = $image;
    }

    public function generatePdf(Wedding $wedding, Guest $guest, string $qrcode): string
    {
        $html = $this->getContentHTML($wedding, $guest, $qrcode);

        $this->pdf->setTimeout(120);
        $this->pdf->setOption('enable-local-file-access', true);

        return $this->pdf->getOutputFromHtml($html);
    }

    public function generateImage(Wedding $wedding, Guest $guest, string $qrcode): string
    {
        $html = $this->getContentHTML($wedding, $guest, $qrcode);

        $this->image->setTimeout(120);
        $this->image->setOptions([
            'enable-local-file-access' => true,
            'format' => 'png',
        ]);

        return $this->image->getOutputFromHtml($html);
    }

    public function getContentHTML(Wedding $wedding, Guest $guest, string $qrcode): string
    {
        return $this->twig->render('emails/invitation.html.twig', [
            'wedding' => $wedding,
            'guest' => $guest,
            'qrcode' => $qrcode,
        ]);
    }
}
