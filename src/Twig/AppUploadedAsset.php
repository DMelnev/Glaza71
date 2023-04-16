<?php


namespace App\Twig;


use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\RuntimeExtensionInterface;

class AppUploadedAsset implements RuntimeExtensionInterface
{
    private ParameterBagInterface $parameterBag;
    private Packages $packages;

    public function __construct(ParameterBagInterface $parameterBag, Packages $packages)
    {
        $this->parameterBag = $parameterBag;
        $this->packages = $packages;
    }

    public function uploadAsset(string $config, string $path)
    {
        return $this->packages->getUrl($this->parameterBag->get($config) . '/' . $path);
    }

}