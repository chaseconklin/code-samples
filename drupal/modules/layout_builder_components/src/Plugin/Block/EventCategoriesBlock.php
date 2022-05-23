<?php

namespace Drupal\layout_builder_components\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a 'Event Categories' component block.
 *
 * @Block(
 *   id = "event_categories",
 *   admin_label = @Translation("Event Categories"),
 *   category = @Translation("Components"),
 * )
 */
class EventCategoriesBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();

    $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('event');

    return [
      '#negative_margin' => $config['negative_margin'],
      '#title' => $config['label'],
      '#terms' => $terms
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();

    $form['negative_margin'] = [
      '#type' => 'select',
      '#title' => $this->t('Use Negative Margin'),
      '#description' => $this->t('Should this block use the sidebar\'s negative margin?'),
      '#options' => [
        'yes' => $this->t('Yes'),
        'no' => $this->t('No')
      ],
      '#default_value' => isset($config['negative_margin']) ? $config['negative_margin'] : 'yes',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->configuration['negative_margin'] = $values['negative_margin'];
  }
}

