<?php

namespace Drupal\custom_crud_form\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * CustomCrudController
 */
class CustomCrudController extends ControllerBase {

  public function display() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('<h1> Link to the <a href="/contact-us"> <strong>Custom Form</strong> </h1> ')
    ];
  }

}
