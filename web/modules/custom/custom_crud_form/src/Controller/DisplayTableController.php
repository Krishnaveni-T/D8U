<?php

namespace Drupal\custom_crud_form\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;

/**
 * Class DisplayTableController.
 *
 * @package Drupal\custom_crud_form\Controller
 */
class DisplayTableController extends ControllerBase {


  public function getContent() {
    $build = [
      'list' => [
        '#theme' => 'form_list',
        '#description' => 'Desc',
        '#attributes' => [],
      ],
    ];
    return $build;
  }
 
  public function display() {
    //header
    $header_table = array(
     'id'=>    t('Id'),
        'fname' => t('First Name'),
        'lname' => t('Last Name'),
        'email' => t('Email'),
        'mobilenumber' => t('Mobile'),
        'country' => t('Country'),
        'state' => t('State'), 
        'comments' => t('Comments'), 
        'pincode' => t('Pincode'), 
        'opt' => t('Delete'),
        'opt1' => t('Edit'),
    );
 
    $query = \Drupal::database()->select('custom_crud_form', 'm');
      $query->fields('m', ['id','fname', 'lname','email','mobilenumber','country','state','comments','pincode']);
      $results = $query->execute()->fetchAll();
        $rows=array();
    foreach($results as $data){
        $delete = Url::fromUserInput('/contact-us/form/delete/'.$data->id);
        $edit   = Url::fromUserInput('/contact-us?num='.$data->id);

             $rows[] = array(
            'id' =>$data->id,
                'fname' => $data->fname,
                'lname' => $data->lname,
                'email' => $data->email,
                'mobilenumber' => $data->mobilenumber,
                'country' => $data->country, 
                'state' => $data->state,
                'comments' => $data->comments, 
                'pincode' => $data->pincode, 
                 \Drupal::l('Delete', $delete),
                 \Drupal::l('Edit', $edit),
            );

    }

    $form['table'] = [
            '#type' => 'table',
            '#header' => $header_table,
            '#rows' => $rows,
            '#empty' => t('No users found'),
        ];
        return $form;

  }

}
