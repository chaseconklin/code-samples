<?php

namespace Drupal\layout_builder_components\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Featured News' component block.
 *
 * @Block(
 *   id = "person_news_block",
 *   admin_label = @Translation("Person News"),
 *   category = @Translation("Components"),
 * )
 */
class PersonNewsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();

    $sfid = '';
    $articles_exist = '';
    $articles = [];

    $node = \Drupal::routeMatch()->getParameter('node');

    if ($node) {
      if ($staff_id = $node->get('field_per_staff_id')->value) {

        $client = \Drupal::service('http_client_factory')->fromOptions([
                  'base_uri' => 'https://feed.news.com',
                ]);
        $response = $client->get('news/person/' . $staff_id . '/json');

        $articles = Json::decode($response->getBody());

        if (!empty($staff_id) && count($articles) > 0) {
          $articles_exist = '1';
        }
        else {
          $articles_exist = '0';
        }
      }
    }

    return [
      '#title' => $config['label'],
      '#top_of_page' => $config['top_of_page'],
      '#button_color' => $config['button_color'],
      '#staff_id' => $staff_id,
      '#articles_exist' => $articles_exist,
      '#articles' => $articles
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();

    $form['top_of_page'] = [
      '#type' => 'select',
      '#title' => $this->t('Top of Page (Under Hero)'),
      '#description' => $this->t('Is this block directly under the hero element? Selecting <em>Yes</em> removes unnecessary margin.'),
      '#options' => [
        1 => $this->t('Yes'),
        0 => $this->t('No')
      ],
      '#default_value' => isset($config['top_of_page']) ? $config['top_of_page'] : 0,
    ];

    $form['button_color'] = [
      '#type' => 'select',
      '#title' => $this->t('Button Color'),
      '#description' => $this->t('Select which color you would like the <em>View all News</em> button to be.'),
      '#options' => [
        'navyToFrost' => $this->t('Navy to Frost'),
        'greenToFrost' => $this->t('Green to Frost'),
        'frostToGreen' => $this->t('Frost to Green'),
        'frostToMaroon' => $this->t('Frost to Maroon'),
      ],
      '#default_value' => isset($config['button_color']) ? $config['button_color'] : 'frosttoMaroon',
    ];


    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->configuration['top_of_page'] = $values['top_of_page'];
    $this->configuration['button_color'] = $values['button_color'];
  }
}

