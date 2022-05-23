<?php

namespace Drupal\layout_builder_components\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Quote' component block.
 *
 * @Block(
 *   id = "quote_component",
 *   admin_label = @Translation("Quote"),
 *   category = @Translation("Components"),
 * )
 */
class QuoteBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $markup = '';

    $entity_type = 'node';
    $entity_id = $config['quote_reference'][0]['target_id'];
    $view_mode = ($config['quote_orientation'] === 'stacked' ? 'quote_stacked' : 'full');
    $entity = \Drupal::entityTypeManager()
      ->getStorage($entity_type)
      ->load($entity_id);

    if ($entity && $entity->get('status')->value == "1") {
      $view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity_type);
      $pre_render = $view_builder->view($entity, $view_mode);
      $markup = render($pre_render);

    } elseif (\Drupal::currentUser()->hasPermission('configure any layout')) {
      // If the user can edit nodes, show a warning to remove the reference
      $layout_url = \Drupal::service('path.current')->getPath() . '/layout';
      $markup = '
        <div class="message -warning" role="contentinfo" aria-label="Status message">
            <div class="message__body">
              <h2 class="message__header">Status message</h2>
              <p class="message__item">The referenced entity is unpublished or missing. Please remove it from the <a href="' . $layout_url . '">Layouts panel</a>.</p>
          </div>
        </div>';

    }

    return [
      '#title' => '',
      '#markup' => $markup,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    // $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();
    $default_entity = '';
    $orientation = 'side';

    if (!empty($config['quote_reference'])) {
      $default_entity = \Drupal\node\Entity\Node::load($config['quote_reference'][0]['target_id']);
    }

    if (array_key_exists('quote_orientation', $config)) {
      $orientation = $config['quote_orientation'];
    }

    $form['quote_reference'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'node',
      '#title' => $this->t('Entity'),
      '#description' => $this->t('Quote that will be displayed'),
      '#tags' => TRUE,
      '#default_value' => $default_entity,
      '#selection_settings' => [
        'target_bundles' => ['quote'],
      ],
    ];

    $form['quote_orientation'] = [
      '#type' => 'select',
      '#title' => $this->t('Orientation'),
      '#options' => [
        'side' => 'Side-by-side (wide)',
        'stacked' => 'Stacked',
      ],
      '#default_value' => $orientation,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    // parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['quote_reference'] = $values['quote_reference'];
    $this->configuration['quote_orientation'] = $values['quote_orientation'];
  }
}

