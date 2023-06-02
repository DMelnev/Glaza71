<?php


namespace App\Service;


use App\Entity\Article;
use App\Repository\ArticleRepository;

class UpdateSitemap
{
    private ArticleRepository $articleRepository;

    /**
     * UpdateSitemap constructor.
     */
    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function execute()
    {

        $articles = $this->articleRepository->findAllSortedByUpdate();
        $today = (new \DateTime('now'))->format('Y-m-d H:i:s');
        $url = 'https://glaza71.ru';
        $mainPages = [
            ['url' => '', 'priority' => '1'],
            ['url' => '/optic', 'priority' => '1'],
            ['url' => '/tomography', 'priority' => '1'],
            ['url' => '/articles', 'priority' => '0.9'],
        ];
        $result = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $result .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        foreach ($mainPages as $page) {
            $result .= "  <url>" . PHP_EOL;
            $result .= "    <loc>" . $url . $page['url'] . "</loc>" . PHP_EOL;
            $result .= "    <priority>" . $page['priority'] . "</priority>" . PHP_EOL;
            $result .= "    <lastmod>" . $today . "</lastmod>" . PHP_EOL;
            $result .= "    <changefreq>weekly</changefreq>" . PHP_EOL;
            $result .= "  </url>" . PHP_EOL;
        }
        /** @var Article $article */
        foreach ($articles as $article) {
            $result .= "  <url>" . PHP_EOL;
            $result .= "    <loc>" . $url . '/article/' . $article->getSlug() . "</loc>" . PHP_EOL;
            $result .= "    <priority>1</priority>" . PHP_EOL;
            $result .= "    <lastmod>" . $today . "</lastmod>" . PHP_EOL;
            $result .= "    <changefreq>weekly</changefreq>" . PHP_EOL;
            $result .= "  </url>" . PHP_EOL;
        }
        $result .= '</urlset>';

        $fileName = 'sitemap.xml';
        $path = $this->publicDir;
        file_put_contents($path.$fileName, $result);
    }

}