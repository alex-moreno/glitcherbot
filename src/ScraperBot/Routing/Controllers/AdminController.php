<?php

namespace ScraperBot\Routing\Controllers;

use ScraperBot\Core\GlitcherBot;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormBuilder;
use Symfony\Contracts\Translation\Test\TranslatorTest;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;

/**
 * Class IndexController
 * @package ScraperBot\Routing\Controllers
 */
class AdminController {

    /**
     * Handle a request for the index.
     *
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request) {
        $resultsStorage = GlitcherBot::service('glitcherbot.storage');
        $crawls = $resultsStorage->getTimeStamps();

        $response = new Response();
        $renderer = GlitcherBot::service('glitcherbot.renderer');
        $data = ['headers' => $crawls];

        $content = $renderer->render('admin.twig', $data);
        $response->setContent($content);

        return $response;
    }

    public function removeCrawl(Request $request, $id) {
        $response = new Response();
//        $renderer = GlitcherBot::service('glitcherbot.renderer');

        $defaultFormTheme = 'crawls_delete.twig';

        // the path to TwigBridge library so Twig can locate the
        // form_div_layout.html.twig file
        $appVariableReflection = new \ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
        $vendorTwigBridgeDirectory = dirname($appVariableReflection->getFileName());

        // the path to your other templates
        $viewsDirectory = realpath(__DIR__.'/Resources/views/Form');

        $twig = new Environment(new FilesystemLoader([
            $viewsDirectory,
            $vendorTwigBridgeDirectory . '/../../../src/templates',
        ]),
        ['debug' => true]);
        $formEngine = new TwigRendererEngine([$defaultFormTheme], $twig);
        $twig->addRuntimeLoader(new FactoryRuntimeLoader([
            FormRenderer::class => function () use ($formEngine) {
                return new FormRenderer($formEngine);
            },
        ]));

        $twig->addExtension(new FormExtension());
//        $twig->addExtension(new DebugExtension());

        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new HttpFoundationExtension())
            ->getFormFactory();

        $form = $formFactory->createBuilder()
            ->add('task', TextType::class)
            ->add('dueDate', DateType::class)
            ->getForm();

//        var_dump($twig->render('crawls_delete.twig', [
//            'form' => $form->createView(),
//        ]));

        $content = $twig->render('crawls_delete.twig', ['deleteForm' => $form->createView()]);

        $response->setContent($content);
        return $response;
//        return $twig->render('crawls_delete.twig', [
//            'form' => $form->createView()]);

    }

}
