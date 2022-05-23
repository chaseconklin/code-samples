<?php

namespace Drupal\layout_builder_components\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Featured News' component block.
 *
 * @Block(
 *   id = "featured_news_block",
 *   admin_label = @Translation("Featured News"),
 *   category = @Translation("Components"),
 * )
 */
class FeaturedNewsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();

    $client = \Drupal::service('http_client_factory')->fromOptions([
              'base_uri' => 'https://feed.news.com/',
            ]);
    $response = $client->get('featured-articles-feed.json');

    $articles = Json::decode($response->getBody());

    return [
      '#title' => $config['label'],
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
        'hero' => $this->t('Yes (Hero Section)'),
        'content' => $this->t('Yes (Content Section)'),
        'no' => $this->t('No')
      ],
      '#default_value' => isset($config['top_of_page']) ? $config['top_of_page'] : 'no',
    ];

    $form['title_color'] = [
      '#type' => 'select',
      '#title' => $this->t('Title Triangles Color'),
      '#description' => $this->t('Select which color you would like the triangles that are next to the title to be.'),
      '#options' => [
        'navy' => $this->t('Navy'),
        'orange' => $this->t('Orange'),
        'maroon' =>$this->t('Maroon'),
        'blue' =>$this->t('Blue'),
        'frost' => $this->t('Frost'),
        'green' => $this->t('Green'),
        'mint' =>$this->t('Mint'),
        'yellow' =>$this->t('Yellow')
      ],
      '#default_value' => isset($config['title_color']) ? $config['title_color'] : 'frost'
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

    $form['background_style'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Background Style')
    ];

    $form['background_style']['triangle_status'] = [
      '#type' => 'select',
      '#title' => $this->t('Show Triangle'),
      '#description' => $this->t('Displays a colored triangle in the background of the block.'),
      '#options' => [
        1 => $this->t('Yes'),
        0 => $this->t('No'),
      ],
      '#default_value' => isset($config['background_style']['triangle_status']) ? $config['background_style']['triangle_status'] : 1,
    ];

    $form['background_style']['triangle_position'] = [
      '#type' => 'select',
      '#title' => $this->t('Triangle Position'),
      '#description' => $this->t('Select which side of the block the triangle should be on.'),
      '#options' => [
        'left' => $this->t('Left'),
        'right' => $this->t('Right'),
      ],
      '#default_value' => isset($config['background_style']['triangle_position']) ? $config['background_style']['triangle_position'] : 'left',
      '#states' => [
        'visible' => [
          ':input[name="settings[triangle_status]"]' => ['value' => 1]
        ]
      ]
    ];

    $form['background_style']['triangle_color'] = [
      '#type' => 'select',
      '#title' => $this->t('Triangle Color'),
      '#description' => $this->t('Select which color you would like the triangle to be.'),
      '#options' => [
        'navy' => $this->t('Navy'),
        'orange' => $this->t('Orange'),
        'maroon' =>$this->t('Maroon'),
        'blue' =>$this->t('Blue'),
        'frost' => $this->t('Frost'),
        'green' => $this->t('Green'),
        'mint' =>$this->t('Mint'),
        'yellow' =>$this->t('Yellow')
      ],
      '#default_value' => isset($config['background_style']['triangle_color']) ? $config['background_style']['triangle_color'] : 'frost',
      '#states' => [
        'visible' => [
          ':input[name="settings[triangle_status]"]' => ['value' => 1]
        ]
      ]
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->configuration['top_of_page'] = $values['top_of_page'];
    $this->configuration['title_color'] = $values['title_color'];
    $this->configuration['button_color'] = $values['button_color'];
    $this->configuration['background_style']['triangle_status'] = $values['background_style']['triangle_status'];
    $this->configuration['background_style']['triangle_position'] = $values['background_style']['triangle_position'];
    $this->configuration['background_style']['triangle_color'] = $values['background_style']['triangle_color'];
  }
}

