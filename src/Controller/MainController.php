<?php

namespace App\Controller;

use App\Entity\Url;
use App\Repository\UrlRepository;
use App\Shortener\Interface\ICheckInWeb;
use App\Shortener\Interface\IUrlEncoder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'starting_page')]
    public function index(): Response
    {
        return $this->render('main/StartPage.html.twig');
    }

    #[Route('/helper', name: 'helper_page')]
    public function helper(Request $request): Response
    {
        $getPrefix = $this->getParameter('prefix');
        $url = trim($request->request->get('url'), " \n");
        $maxLength = $this->getParameter('max_length_url') + 9;

        $lengthUrl = strlen($url);

        $option = $request->request->get('option');

        $pattern = "/^http?[a-zA-Z]:\/\/[$getPrefix]*\//m";

        $startSession = $request->getSession();
        $startSession->set('url', $url);

        if (preg_match($pattern, $url) and $option === 'decode' and $maxLength == $lengthUrl) {
            $route = $this->redirectToRoute('decode_page', status: 307);
        } elseif ($option == 'code' and !preg_match($pattern, $url)) {
            $route = $this->redirectToRoute('encode_page', status: 307);
        } elseif ($option == 'redirect' and preg_match($pattern, $url) and $maxLength == $lengthUrl) {
            $route = $this->redirectToRoute('redirect_page');
        } else {
            $route = $this->render('main/HelpPage.html.twig',
                ['length' => $maxLength]);
        }
        return $route;
    }

    /**
     * @throws \Exception
     */
    #[Route('/encode', name: 'encode_page', requirements: ['longUrl' => '.*'])]
    public function encode(EntityManagerInterface $entityManager,
                           IUrlEncoder            $encoder,
                           UrlRepository          $urlRepository,
                           ICheckInWeb            $checkInWeb,
                           Request                $request
    ): Response
    {
        $getUrl = $request->getSession();
        $longUrl = $getUrl->get('url');
        $webResponse = $checkInWeb->exist($longUrl);
        $getLong = $urlRepository->findOneBy(['LongUrl' => $longUrl]);
        $existInDB='';
        if (!$getLong) {
            $shortUrl = $encoder->encode($longUrl);
            $urlEntity = new Url();
            $urlEntity->setShortUrl($shortUrl);
            $urlEntity->setLongUrl($longUrl);
            $timeZone = new \DateTimeZone("Europe/Kyiv");
            $date = new \DateTimeImmutable('now', $timeZone);
            $urlEntity->setCreatedAt($date);
            $entityManager->persist($urlEntity);
            $entityManager->flush();
        } elseif ($getLong) {
            $existInDB='This site already exists in our DB, here it is';
            $shortUrl = $getLong->getShortUrl();
        }
        return $this->render('main/EncodePage.html.twig', [
            'long_url' => $longUrl,
            'short_url' => $shortUrl,
            'exist' => $webResponse,
            'existInDB'=>$existInDB,
        ]);
    }

    #[Route('/decode', name: 'decode_page', requirements: ['ShortUrl' => '.*'])]
    public function decode(UrlRepository $urlRepository, Request $request): Response
    {
        $getUrl = $request->getSession();
        $ShortUrl = $getUrl->get('url');
        $getLong = $urlRepository->findOneBy(['ShortUrl' => $ShortUrl]);
        $getRedirectNumber = $getLong->getRedirectNumber();
        $createdAt = $getLong->getCreatedAt()->getTimestamp();
        $dateTime = date('H:i:s Y-m-d', $createdAt);
        return $this->render('main/DecodePage.html.twig', [
            'long_url' => $getLong->getLongUrl(),
            'short_url' => $ShortUrl,
            'createdAt' => $dateTime,
            'getRedirectNumber' => $getRedirectNumber,

        ]);
    }

    #[Route('/redirect', name: 'redirect_page')]
    public function goToSite(UrlRepository $urlRepository,EntityManagerInterface $entityManager, Request $request): Response
    {
        $getUrl = $request->getSession();
        $ShortUrl = $getUrl->get('url');
        $getLong = $urlRepository->findOneBy(['ShortUrl' => $ShortUrl]);

        $increaseRedirect = $getLong->getRedirectNumber() + 1;

        $getLong->setRedirectNumber($increaseRedirect);

        $entityManager->flush();

        return $this->redirect($getLong->getLongUrl());
    }

}
