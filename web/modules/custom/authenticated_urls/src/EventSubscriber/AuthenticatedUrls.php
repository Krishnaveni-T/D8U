<?php

namespace Drupal\authenticated_urls\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Path\PathMatcher;
use Drupal\Core\Url;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Language\LanguageManagerInterface;

class AuthenticatedUrls implements EventSubscriberInterface {
  protected $request;
  protected $currentUser;
  protected $languageManager;
  protected $pathmatcher;
  protected $routeMatch;

  public function __construct(AccountProxyInterface $currentUser, 
                              RequestStack $request,
                              LanguageManagerInterface $language_manager,
                              PathMatcher $pathmatcher,
                              RouteMatchInterface $route_match) {
    $this->currentUser = $currentUser;
    $this->request = $request;
    $this->languageManager = $language_manager;
    $this->pathmatcher = $pathmatcher;
    $this->routeMatch = $route_match;
  }

  public function onRequest(GetResponseEvent $event) {
    $entity_types = [
      'taxonomy' => 'taxonomy',
      'node'=> 'node',
      'user' => 'user',
    ];
   //print_r( $entity_types ); 
    $redirect_url = '/home';
    $hasPermission = $this->currentUser->hasPermission('allow_all');
    $request_uri = $this->request->getCurrentRequest()->server->get('REQUEST_URI');
    $request_uri = $this->getCurrentPath($request_uri);
    $node = $this->pathmatcher->matchPath($request_uri, '/node/*');
    $taxonomy = $this->pathmatcher->matchPath($request_uri, '/taxonomy/*');
    $user = $this->pathmatcher->matchPath($request_uri, '/user/*');
    if (!$hasPermission && ($node || $taxonomy || $user) && $request_uri != $redirect_url) {
      $route_name = $this->routeMatch->getRouteName();
      $current_url = Url::fromRoute('<current>')->toString();
      $current_url = $this->getCurrentPath($current_url);
      $node = ($route_name === 'entity.node.canonical') ? 1 : 0;
      $taxonomy = ($route_name === 'entity.taxonomy_term.canonical') ? 1 : 0;
      $user = ($route_name === 'entity.user.canonical') ? 1 : 0;
      $flag_node = $flag_taxonomy = $flag_user = 0;
 
      if (!empty($entity_types)) {
        if ($node && $entity_types['node'] === 'node') {
          $flag_node = 1;
        }
        if ($taxonomy && $entity_types['taxonomy'] === 'taxonomy') {
          $flag_taxonomy = 1;
        }
        if ($user && $entity_types['user'] === 'user') {
          $flag_user = 1;
        }
      }

      if (($flag_node || $flag_taxonomy || $flag_user)) {
        $response = new TrustedRedirectResponse($redirect_url);
        $response->addCacheableDependency(CacheableMetadata::createFromRenderArray([])->addCacheTags(['rendered']));
        $event->setResponse($response);
      }
    }
  }

  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['onRequest'];
    // KernelEvents::REQUEST => ['onKernelRequest'],
    // KernelEvents::RESPONSE => ['onKernelResponse'],
    return $events;
  }
    
   public function getCurrentPath($path) {
        $languagecode = $this->languageManager->getCurrentLanguage()->getId();
        $url = preg_replace('/\?.*/', '', $path);
        $path = explode("/", $url);
        if (isset($path[1]) && $path[1] == $languagecode) {
            array_splice($path, 1, 1);
            $url = implode("/", $path);
        }
        return $url;
    }


}
