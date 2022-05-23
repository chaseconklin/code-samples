<?php

namespace Drupal\layout_builder_components\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Department' component block.
 *
 * @Block(
 *   id = "department_component",
 *   admin_label = @Translation("Department"),
 *   category = @Translation("Components"),
 * )
 */
class DepartmentBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $markup = '';

    $entity_type = 'taxonomy_term';
    $entity_id = $config['department_reference'][0]['target_id'];
    $view_mode = 'card_medium';
    $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load($entity_id);

    if ($entity && $entity->get('status')->value == '1') {
      $view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity_type);
      $pre_render = $view_builder->view($entity, $view_mode);
      $markup = render($pre_render);

    } elseif (\Drupal::currentUser()->hasPermission('administer nodes')) {
      // If the user can edit nodes, show a warning to remove the reference
      $layout_url = \Drupal::service('path.current')->getPath() . '/layout';
      $markup = '
        <div class="message -warning" role="contentinfo" aria-label="Status message">
            <div class="message__body">
              <h2 class="message__header">Status message</h2>
              <p class="message__item">The referenced department is unpublished or missing. Please remove it from the <a href="' . $layout_url . '">Layouts panel</a>.</p>
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

    if (!empty($config['department_reference'])) {
      $default_entity = \Drupal\taxonomy\Entity\Term::load($config['department_reference'][0]['target_id']);
    }

    $form['department_reference'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'taxonomy_term',
      '#title' => $this->t('Department'),
      '#description' => $this->t('Department that will be displayed'),
      '#tags' => TRUE,
      '#default_value' => $default_entity,
      '#selection_settings' => array(
        'target_bundles' => array('department'),
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
    $this->configuration['department_reference'] = $values['department_reference'];
  }
}

