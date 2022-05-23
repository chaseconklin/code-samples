<?php

namespace Drupal\layout_builder_components\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Content partial' component block.
 *
 * @Block(
 *   id = "webform_component",
 *   admin_label = @Translation("Webform Block"),
 *   category = @Translation("Components"),
 * )
 */
class WebformBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $markup = '';

    $entity_type = 'webform';
    $entity_id = $config['webform_reference'][0]['target_id'];
    $view_mode = 'full';
    $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load($entity_id);

    if ($entity && $entity->get('status') == 'open') {
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
              <p class="message__item">The referenced webform is closed or missing. Please remove it from the <a href="' . $layout_url . '">Layouts panel</a>.</p>
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
    $default_webform = '';

    if (!empty($config['webform_reference'])) {
      $default_webform = \Drupal\webform\Entity\Webform::load($config['webform_reference'][0]['target_id']);
    }

    $form['webform_in_sidebar'] = [
      '#type' => 'select',
      '#title' => $this->t('In Sidebar'),
      '#description' => $this->t('Is this webform being placed in the sidebar of the page?'),
      '#options' => [
        true => $this->t('Yes'),
        false => $this->t('No'),
      ],
      '#default_value' => isset($config['webform_in_sidebar']) ? $config['webform_in_sidebar'] : 0,
    ];

    $form['webform_title_tag'] = [
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
      '#default_value' => (array_key_exists('webform_title_tag', $config) ? $config['webform_title_tag'] : 'h2'),
      '#states' => [
        'visible' => [
          ':input[name="settings[webform_in_sidebar]"]' => ['value' => 0]
        ]
      ]
    ];

    $form['webform_title_classes'] = [
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
      '#default_value' => (array_key_exists('webform_title_classes', $config) ? $config['webform_title_classes'] : '-section'),
      '#states' => [
        'visible' => [
          ':input[name="settings[webform_in_sidebar]"]' => ['value' => 0]
        ]
      ]
    ];

    $form['webform_reference'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'webform',
      '#title' => $this->t('Webform'),
      '#description' => $this->t('Webform that will be displayed'),
      '#tags' => TRUE,
      '#default_value' => $default_webform,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    // parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['webform_reference'] = $values['webform_reference'];
    $this->configuration['webform_title_tag'] = $values['webform_title_tag'];
    $this->configuration['webform_title_classes'] = $values['webform_title_classes'];
    $this->configuration['webform_in_sidebar'] = $values['webform_in_sidebar'];
  }
}

