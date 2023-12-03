<?php

namespace App\Controller;

use App\Entity\Url;
use App\Repository\UrlRepository;
use App\Shortener\classes\EncodeUrl;
use App\Shortener\Interface\ICheckInWeb;
use App\Shortener\Interface\IUrlEncoder;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Shortener\Interface\NotFoundServiceException;

class MainController extends AbstractController
{
    #[Route('/', name: 'starting_page')]
    public function index(): Response
    {
        return $this->render('main/StartPage.html.twig');
    }

    #[Route('/proxy', name: 'proxy_page')]
    public function proxy(Request $request): Response
    {
        $url = $request->request->get('url');
        $pattern = "/vily/i";
        if (preg_match($pattern, $url)) {
            $route = '/decode/' . $url;
        } else {
            $route = '/encode/' . $url;
        }

        return $this->redirect($route);
    }

    #[Route('/encode/{longUrl}', name: 'encode_page', requirements: ['longUrl' => '.*'])]
    public function encode(EntityManagerInterface $entityManager, $longUrl,
                           IUrlEncoder            $encoder,
                           UrlRepository          $urlRepository,
                           ICheckInWeb            $checkInWeb): Response
    {
        $getLong = $urlRepository->findOneBy(['LongUrl' => $longUrl]);
        $webResponse = $checkInWeb->exist($longUrl);

        if (!$getLong) {
            $shortUrl = $encoder->encode($longUrl);
            $urlEntity = new Url();
            $urlEntity->setShortUrl($shortUrl);
            $urlEntity->setLongUrl($longUrl);
            $entityManager->persist($urlEntity);
            $entityManager->flush();
        }else{
            $shortUrl=$getLong->getShortUrl();
        }
        return $this->render('main/EncodePage.html.twig', [
            'long_url' => $longUrl,
            'short_url' => $shortUrl,
            'exist' => $webResponse
        ]);
    }

    #[Route('/decode/{ShortUrl}', name: 'decode_page', requirements: ['ShortUrl' => '.*'])]
    public function decode(UrlRepository $urlRepository, $ShortUrl): Response
    {
        $getLong = $urlRepository->findOneBy(['ShortUrl' => $ShortUrl]);
        return $this->render('main/DecodePage.html.twig', [
            'long_url' => $getLong->getLongUrl(),
            'short_url' => $ShortUrl
        ]);
    }


}
