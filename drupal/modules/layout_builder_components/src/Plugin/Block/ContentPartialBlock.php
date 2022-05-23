<?php

namespace Drupal\layout_builder_components\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Content partial' component block.
 *
 * @Block(
 *   id = "content_partial_component",
 *   admin_label = @Translation("Content partial"),
 *   category = @Translation("Components"),
 * )
 */
class ContentPartialBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $markup = '';

    $entity_type = 'node';
    $entity_id = $config['content_partial_reference'][0]['target_id'];
    $view_mode = 'full';
    $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load($entity_id);

    if ($entity && $entity->get('status')->value == '1') {
      $view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity_type);
      $pre_render = $view_builder->view($entity, $view_mode);
      $markup = render($pre_render);
      
    } elseif (\Drupal::currentUser()->hasPermission('administer nodes')) {
      // If the user can edit nodes, show a warning to removed the missing reference
      $layout_url = \Drupal::service('path.current')->getPath() . '/layout';
      $markup = '
        <div class="message -warning" role="contentinfo" aria-label="Status message">
            <div class="message__body">
              <h2 class="message__header">Status message</h2>
              <p class="message__item">The referenced node is unpublished or missing. Please remove it from the <a href="' . $layout_url . '">Layouts panel</a>.</p>
          </div>
        </div>';
    }

    return [
      '#title' => $config['label'],
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

    if (!empty($config['content_partial_reference'])) {
      $default_entity = \Drupal\node\Entity\Node::load($config['content_partial_reference'][0]['target_id']);
    }

    $form['content_partial_tag'] = [
      '#type' => 'select',
      '#title' => $this->t('Title Tag'),
      '#options' => [
        'h2' => $this->t('H2'),
        'h3' => $this->t('H3'),
        'h4' => $this->t('H4'),
        'h5' => $this->t('H5'),
        'h6' => $this->t('H6'),
        'p' => $this->t('P'),
      ],
      '#default_value' => (array_key_exists('content_partial_tag', $config) ? $config['content_partial_tag'] : 'h2'),
    ];

    $form['content_partial_classes'] = [
      '#type' => 'select',
      '#title' => $this->t('Title Classes'),
      '#options' => [
        '-top' => $this->t('Top'),
        '-section' => $this->t('Section'),
        '-xlarge' => $this->t('XLarge'),
        '-large' => $this->t('Large'),
        '-medium' => $this->t('Medium'),
        '-small' => $this->t('Small'),
        ],
      '#default_value' => (array_key_exists('content_partial_classes', $config) ? $config['content_partial_classes'] : '-section'),
      ];

    $form['content_partial_reference'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'node',
      '#title' => $this->t('Entity'),
      '#description' => $this->t('Content partial that will be displayed in full mode'),
      '#tags' => TRUE,
      '#default_value' => $default_entity,
      '#selection_settings' => array(
        'target_bundles' => array('content_partial'),
      ),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    // parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['content_partial_reference'] = $values['content_partial_reference'];
    $this->configuration['content_partial_tag'] = $values['content_partial_tag'];
    $this->configuration['content_partial_classes'] = $values['content_partial_classes'];
  }
}

