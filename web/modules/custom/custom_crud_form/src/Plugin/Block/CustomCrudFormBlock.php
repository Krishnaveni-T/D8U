<?php

namespace Drupal\custom_crud_form\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 *
 * @Block(
 *  id = "custom_crud_form_block",
 *  admin_label = @Translation("Custom CRUD Form block"),
 * )
 */
class CustomCrudBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\custom_crud_form\Form\CustomCrudForm');
    return $form;
  }

}
