<?php

namespace UrlShortener\Controller;

use AppCore\Controller\Controller;
use UrlShortener\Manager\ShortUrlManager;

class UrlShortenerController extends Controller
{
    /**
     * @param array $request
     */
    public function indexAction(array $request)
    {
        $this->render('UrlShortener:index');
    }

    public function shortUrlAction(array $request)
    {
        // @todo check is ajax request
        $url = (!empty($request['url'])) ? $request['url'] : '';
        // @todo throw exception if url is empty

        $urlManager = $this->getShortUrlManager();
        $shortUrl = $urlManager->createShortUrl($url);

        echo json_encode(['short_url' => $shortUrl]);
    }

    public function redirectAction($request)
    {
        $shortCode = (!empty($request['code'])) ? $request['code'] : '';

        $urlManager = $this->getShortUrlManager();
        $url = $urlManager->getSourceUrlByShortCode($shortCode);

        if (!empty($url)) {
            $this->redirect($url);
        }

        $this->render('Default:index');
    }

    /**
     * @return ShortUrlManager
     */
    private function getShortUrlManager()
    {
        $container = $this->getContainer();
        /* @var $urlManager ShortUrlManager */
        return $container->get('short_url_manager');
    }
}