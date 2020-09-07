<?php

namespace Drupal\custom_crud_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;

class CustomCrudForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'custom_crud_form';
  }

  /**
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $conn = Database::getConnection();
     $record = array();
    if (isset($_GET['num'])) {
        $query = $conn->select('custom_crud_form', 'm')
            ->condition('id', $_GET['num'])
            ->fields('m');
        $record = $query->execute()->fetchAssoc();
    }

    $form['fname'] = array(
      '#type' => 'textfield',
      '#title' => t('First Name'),
      '#required' => TRUE, 
      '#default_value' => (isset($record['fname']) && $_GET['num']) ? $record['fname']:'',
      );
    
      $form['lname'] = array(
        '#type' => 'textfield',
        '#title' => t('Last Name'),
        '#required' => TRUE, 
        '#default_value' => (isset($record['lname']) && $_GET['num']) ? $record['lname']:'',
        );

      $form['email'] = array(
          '#type' => 'email',
          '#title' => t('Email ID:'),
          '#required' => TRUE,
          '#default_value' => (isset($record['email']) && $_GET['num']) ? $record['email']:'',
          );

    $form['mobilenumber'] = array(
      '#type' => 'textfield',
      '#title' => t('Mobile Number:'),
      '#default_value' => (isset($record['mobilenumber']) && $_GET['num']) ? $record['mobilenumber']:'',
      '#required' => TRUE,
      ); 

    $form['country'] = [
      '#type' => 'select',
      '#title' => t('Country'),
      '#description' => t('Country'),
      '#required' => TRUE,
      '#options' => _fetch_countries(),
      '#suffix' => '<span class="getstates"></span>',
      '#ajax' => [
        'callback' => array($this, 'validateEmailAjax'),
        'event' => 'change',
        'progress' => array(
          'type' => 'throbber',
          'message' => t('Loading states...'),
        ), 
    ] ];
    
  // $options_first = _fetch_countries(); 
  // if(!empty($form_state->getValues('country'))){
  //   $country = $form_state->getValues('country');
  // }else{
  //   $country =  key(_fetch_countries);
  // }
  // $form['country'] = array(
  //   '#type' => 'select', 
  //   '#title' => 'Country', 
  //   '#options' => $options_first,
  //   '#default_value' => $form_state->getValue('country'),
  //   '#ajax' => array( 
  //     // 'event' => 'change',
  //     'callback' => 'ajax_example_dependent_dropdown_callback', 
  //     'wrapper' => 'dropdown-second-replace',
  //   ),
  // );
    
  // if(!empty($form_state->getValues('state'))){
  //   $state_select = $form_state->getValues('state');
  // }else{
  //   $state_select = '';
  // }
  // $form['state'] = array(
  //   '#type' => 'select', 
  //   '#title' => 'State',
  //   // The entire enclosing div created here gets replaced when dropdown_first
  //   // is changed. 
  //   '#prefix' => '<div id="dropdown-second-replace">', 
  //   '#suffix' => '</div>',
  //   // when the form is rebuilt during ajax processing, the $selected variable
  //   // will now have the new value and so the options will change 
  //   '#options' =>  _ajax_example_get_second_dropdown_options(), 
  //   '#default_value' => $state_select,
  // );

    $form['state'] = array(
      '#title' => t('State'),
      '#type' => 'select',
      '#description' => t('Select the state'),
      '#options' => ['tamilnadu' => "Tamilnadu",
                     'kerala' => "Kerala",
      ], //_fetch_states($id), 
      '#attributes' => ["class" => 'getstates'],
      '#multiple' => FALSE,
      '#validated' => TRUE
    );

    $form['comments'] = array (
      '#type' => 'textarea',
      '#rows' => 5,
      '#title' => t('Comments'),
      '#default_value' => (isset($record['comments']) && $_GET['num']) ? $record['comments']:'',
       );

    $form['pincode'] = array (
        '#type' => 'textfield',
        '#title' => t('Pincode'),
        '#default_value' => (isset($record['pincode']) && $_GET['num']) ? $record['pincode']:'',
         );

    $form['submit'] = [
        '#type' => 'submit',
        '#value' => 'save', 
    ];

    return $form;
  }

  /**
    * {@inheritdoc}
    */
  public function validateForm(array &$form, FormStateInterface $form_state) {

         $fname = $form_state->getValue('fname');
          if(preg_match('/[^A-Za-z]/', $fname)) {
             $form_state->setErrorByName('fname', $this->t('your name must in characters without space'));
          }

         $lname = $form_state->getValue('fname');
          if(preg_match('/[^A-Za-z]/', $lname)) {
            $form_state->setErrorByName('lname', $this->t('your name must in characters without space'));
         } 
              
         if (strlen($form_state->getValue('mobilenumber')) < 10 ) {
          $form_state->setErrorByName('mobile', $this->t('your mobile number must in 10 digits'));
         }

         if (!\Drupal::service('email.validator')->isValid($form_state->getValues()['email'])) {
          $form_state->setErrorByName('email', $this->t('Email address is not a valid one.'));
         }

        if (!intval($form_state->getValue('pincode'))) {
             $form_state->setErrorByName('pincode', $this->t('Pincode should be a number'));
        }
        
        if (empty($form_state->getValue('country'))) {
          $form_state->setErrorByName('country', $this->t('Select a country'));
        } 

        if (empty($form_state->getValue('state'))) {
          $form_state->setErrorByName('state', $this->t('Select a state'));
        } 

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $field=$form_state->getValues();
    $fname=$field['fname'];
    $lname=$field['lname'];
    $number=$field['mobilenumber'];
    $email=$field['email'];
    $country=$field['country'];
    $state=$field['state'];
    $comments=$field['comments'];
    $pincode=$field['pincode'];
 
    if (isset($_GET['num'])) {
          $field  = array(
              'fname'   => $fname,
              'lname'   => $lname,
              'mobilenumber' =>  $number,
              'email' =>  $email,
              'country' => $country,
              'state' => $state,
              'comments' => $comments,
              'pincode' => $pincode,
          );
          $query = \Drupal::database();
          $query->update('custom_crud_form')
              ->fields($field)
              ->condition('id', $_GET['num'])
              ->execute();
          drupal_set_message("succesfully updated");
         // $form_state->setRedirect('custom_crud_form.display_table_controller_display');
      }

       else
       {
           $field  = array(
              'fname'   => $fname,
              'lname'   => $lname,
              'mobilenumber' =>  $number,
              'email' =>  $email,
              'country' => $country,
              'state' => $state,
              'comments' => $comments,
              'pincode' => $pincode,
          );
           $query = \Drupal::database();
           $query ->insert('custom_crud_form')
               ->fields($field)
               ->execute();
           drupal_set_message("Saved!");

           $response = new RedirectResponse("/contact-us/list");
           $response->send();
       }
     }

}

/**
 * @return array of all vocabulary of country
 *
 */
function _fetch_countries(){
  $options = [];
  foreach (\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('country',0,1) as $item) {
    $options[$item->tid] = str_repeat('-', $item->depth) . $item->name;
  }
  return $options;
}


/**
 * @return array of all vocabulary of states
 *
 */
function _fetch_states($id){
  $options = [];
  foreach (\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($id) as $item) {
    $options[$item->tid] = str_repeat('-', $item->depth) . $item->name;
  }
  return $options; 
}