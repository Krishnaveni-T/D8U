<?php

namespace Drupal\custom_crud_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;
use Drupal\Core\Render\Element;

class DeleteCustomCrudForm extends ConfirmFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'delete_custom_crud_form';
  }

  public $cid;

  public function getQuestion() { 
    return t('Do you want to delete %cid?', array('%cid' => $this->cid));
  }

  public function getCancelUrl() {
    return new Url('custom_crud_form.display_table_controller_display');
  }
    
  public function getDescription() {
    return t('Are you sure to delete this entry?');
  }
 
  public function getConfirmText() {
    return t('Delete it!');
  }

  /**
   * getCancelText
   */
  public function getCancelText() {
    return t('Cancel');
  }

  /**
   *  buildForm
   */
  public function buildForm(array $form, FormStateInterface $form_state, $cid = NULL) {
      $this->id = $cid;
      return parent::buildForm($form, $form_state);
  }

  /**
    * validateForm
    */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * submitForm
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
   $query = \Drupal::database();
    $query->delete('custom_crud_form')
          ->condition('id',$this->id)
        ->execute();
             drupal_set_message("The entry has deleted");
 
    $form_state->setRedirect('custom_crud_form.display_table_controller_display');
  } 
}
