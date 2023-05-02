<?php

namespace App\Command;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\MainPageRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UpdateSitemapCommand extends Command
{
    protected static $defaultName = 'app:update-sitemap';
    protected static $defaultDescription = 'Updating sitemap.xml';
    private ArticleRepository $articleRepository;
    private $publicDir;

    /**
     * UpdateSitemapCommand constructor.
     */
    public function __construct(
        ArticleRepository $articleRepository,
        $publicDir
    )
    {
        parent::__construct();
        $this->articleRepository = $articleRepository;
        $this->publicDir = $publicDir;
    }

    protected function configure(): void
    {
//        $this
////            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
////            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
//        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $articles = $this->articleRepository->findAllSortedByUpdate();
        $today = (new \DateTime('now'))->format('Y-m-d H:i:s');
        $url = 'https://glaza71.ru';
        $mainPages = [
            ['url' => '', 'priority' => '1'],
            ['url' => '/optic', 'priority' => '1'],
            ['url' => '/tomography', 'priority' => '1'],
            ['url' => '/articles', 'priority' => '0.8'],
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
            $result .= "    <priority>0.9</priority>" . PHP_EOL;
            $result .= "    <lastmod>" . $today . "</lastmod>" . PHP_EOL;
            $result .= "    <changefreq>weekly</changefreq>" . PHP_EOL;
            $result .= "  </url>" . PHP_EOL;
        }
        $result .= '</urlset>';

        $fileName = 'sitemap.xml';
        $path = $this->publicDir;
        file_put_contents($path.$fileName, $result);

        $io->success('Time was changed to ' . $today);
        return Command::SUCCESS;
    }
}
