<?php

namespace ScraperBot\Routing\Controllers;

use ScraperBot\Core\GlitcherBot;
use ScraperBot\Storage\SqlLite3Storage;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;
use Twig\Environment;
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

        // Get if we are coming from the delete area.
        $idDeleted = $request->query->get('id');

        $data = ['headers' => $crawls, 'idDeleted' => $idDeleted];

        $response = new Response();
        $renderer = GlitcherBot::service('glitcherbot.renderer');

        $content = $renderer->render('admin.twig', $data);
        $response->setContent($content);

        return $response;
    }

    public function removeCrawl(Request $request, $id) {
        $response = new Response();

        $defaultFormTheme = 'form_div_layout.html.twig';

        // the path to TwigBridge library so Twig can locate the
        // form_div_layout.html.twig file
        $appVariableReflection = new \ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
        $vendorTwigBridgeDirectory = dirname($appVariableReflection->getFileName());

        // the path to your other templates
        $viewsDirectory = realpath($_SERVER['DOCUMENT_ROOT'] . '/../src/templates');

        //init twig with directories
        $twig = new Environment(new FilesystemLoader([
            $viewsDirectory,
            $vendorTwigBridgeDirectory . '/Resources/views/Form',
        ]));

        $formEngine = new TwigRendererEngine([$defaultFormTheme], $twig);
        $twig->addRuntimeLoader(new FactoryRuntimeLoader([
            FormRenderer::class => function () use ($formEngine) {
                return new FormRenderer($formEngine);
            },
        ]));

        // Add form extenstion.
        $twig->addExtension(new FormExtension());

        // Creates the Translator.
        $translator = new Translator('en');
        // Somehow load some translations into it.
        $translator->addLoader('xlf', new XliffFileLoader());

        // Adds the TranslationExtension (it gives us trans filter).
        $twig->addExtension(new TranslationExtension($translator));

        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new HttpFoundationExtension())
            ->getFormFactory();

        $form = $formFactory->createBuilder()
            ->add('Confirm', CheckboxType::class)
            ->getForm();

        $request = Request::createFromGlobals();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            // TODO: adapt to work against different db engines, not just sqlite.
            $db = new SqlLite3Storage();
            $db->DeleteCrawl($id);

            $response = new RedirectResponse('/admin?crawldeleted&id=' . $id);
            $response->prepare($request);

            return $response->send();
        } else {
            $content = $twig->render('crawls_delete.twig', ['deleteForm' => $form->createView(), 'id' => $id]);

        }

        $response->setContent($content);
        return $response;

    }

}
