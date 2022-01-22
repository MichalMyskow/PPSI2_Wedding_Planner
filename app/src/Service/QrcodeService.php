<?php
namespace App\Service;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
class QrcodeService
{
    private BuilderInterface $builder;

    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    public function qrcode($uuid)
    {
        $protocol = ((!empty($_SERVER['HTTPS']) && 'off' !== $_SERVER['HTTPS']) || 443 === $_SERVER['SERVER_PORT']) ? 'https://' : 'http://';
        $url = $protocol.$_SERVER['HTTP_HOST'].'/accept-invitation/'.$uuid;
        $result = $this->builder
            ->data($url)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(200)
            ->build()
        ;
        return $result->getDataUri();
    }
}