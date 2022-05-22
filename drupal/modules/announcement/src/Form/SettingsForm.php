<?php

namespace Drupal\announcement\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Announcement settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'announcement_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['announcement.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['message'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Announcement Message'),
      '#description' => $this->t('This text of the alert that will be shown.'),
      '#default_value' => $this->config('announcement.settings')->get('message'),
      '#required' => TRUE,
    ];
    $form['url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('URL'),
      '#description' => $this->t('The page that the message will link to. Must be a valid url.'),
      '#default_value' => $this->config('announcement.settings')->get('url'),
      '#required' => TRUE,
    ];
    $form['activate'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Activate the Announcement'),
      '#description' => $this->t('The status is active when this box is checked. (Be sure to submit the form to save changes)'),
      '#default_value' => $this->config('announcement.settings')->get('activate'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (filter_var($form_state->getValue('url'), FILTER_VALIDATE_URL) == false) {
      $form_state->setErrorByName('url', $this->t('Please enter a valid url.'));
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('announcement.settings')
      ->set('activate', $form_state->getValue('activate'))
      ->set('url', $form_state->getValue('url'))
      ->set('message', $form_state->getValue('message'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
