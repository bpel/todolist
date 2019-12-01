<?php

namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\Templating\EngineInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private $templating;

    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        return new Response($this->templating->render('default/index.html.twig', [
            'error' => "Vous n'avez pas la permission d'accedez à cette ressource"
        ], 403));
    }
}
