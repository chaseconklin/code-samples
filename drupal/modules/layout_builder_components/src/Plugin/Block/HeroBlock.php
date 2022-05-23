<?php

namespace Drupal\layout_builder_components\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Hero' component block.
 *
 * @Block(
 *   id = "hero_component",
 *   admin_label = @Translation("Hero"),
 *   category = @Translation("Components"),
 * )
 */
class HeroBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    return [
      '#hero_size' => $config['hero_size'],
      '#hero_position' => array_key_exists('hero_position', $config) ? $config['hero_position'] : 'center',
      '#hero_use_video' => array_key_exists('hero_use_video', $config) ? $config['hero_use_video'] : 0,
      '#hero_video_ref' => array_key_exists('hero_video_ref', $config) ? $config['hero_video_ref'] : '',
      '#hero_xlarge_entity_ref' => $config['hero_xlarge_entity_ref'],
      '#hero_breadcrumb_display' => $config['hero_breadcrumb_display'],
      '#hero_subtitle_display' => $config['hero_subtitle_display'],
      '#hero_subtitle_override' => $config['hero_subtitle_override'],
      '#hero_pretitle_display' => $config['hero_pretitle_display'],
      '#hero_pretitle_override' => $config['hero_pretitle_override'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $hero_xlarge_entity_ref = '';
    if (!empty($config['hero_xlarge_entity_ref'])) {
      $hero_xlarge_entity_ref = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->load($config['hero_xlarge_entity_ref'][0]['target_id']);
    }

    $hero_video_ref = '';
    if (!empty($config['hero_video_ref'])) {
      $hero_video_ref = \Drupal::entityTypeManager()
        ->getStorage('media')
        ->load($config['hero_video_ref'][0]['target_id']);
    }

    $form['hero_size'] = [
      '#type' => 'select',
      '#title' => $this->t('Size'),
      '#description' => $this->t('What size hero graphic do you want?'),
      '#options' => [
        'small' => $this->t('Small'),
        'medium' => $this->t('Medium'),
        'large' => $this->t('Large'),
        'xlarge' => $this->t('Extra Large'),
      ],
      '#default_value' => isset($config['hero_size']) ? $config['hero_size'] : 'small',
    ];

    $form['hero_position'] = [
      '#type' => 'select',
      '#title' => $this->t('Image position'),
      '#options' => [
        'top' => $this->t('Top'),
        'center' => $this->t('Center'),
        'bottom' => $this->t('Bottom'),
        '25%' => $this->t('25%'),
        '75%' => $this->t('75%'),
      ],
      '#default_value' => array_key_exists('hero_position', $config) ? $config['hero_position'] : 'center',
    ];

    $form['hero_use_video'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use Video'),
      '#description' => $this->t('Use video for hero instead of an image.'),
      '#default_value' => isset($config['hero_use_video']) ? $config['hero_use_video'] : 0,
      '#states' => [
        'visible' => [
          ':input[name="settings[hero_size]"]' => ['value' => 'xlarge']
        ]
      ]
    ];

    $form['hero_video_ref'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'media',
      '#title' => $this->t('Hero Video'),
      '#description' => $this->t('Select the video that will be displayed.'),
      '#default_value' => $hero_video_ref,
      '#tags' => true,
      '#selection_settings' => [
        'target_bundles' => ['video']
      ],
      '#states' => [
        'visible' => [
          ':input[name="settings[hero_size]"]' => ['value' => 'xlarge'],
          ':input[name="settings[hero_use_video]"]' => ['checked' => true]
        ]
      ]
    ];

    $form['hero_xlarge_entity_ref'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'node',
      '#title' => $this->t('Content overlay'),
      '#description' => $this->t('This is the content that will overlay the hero with text or graphics.'),
      '#default_value' => $hero_xlarge_entity_ref,
      '#tags' => true,
      '#selection_settings' => [
        'target_bundles' => ['content_partial']
      ],
      '#states' => [
        'visible' => [
          ':input[name="settings[hero_size]"]' => ['value' => 'xlarge'],
        ]
      ]
    ];

    $form['hero_xlarge_content_position'] = [
      '#type' => 'select',
      '#title' => $this->t('Content overlay position'),
      '#options' => [
        'left' => $this->t('Left'),
        'center' => $this->t('Center'),
        'right' => $this->t('Right'),
        'full' => $this->t('Full'),
      ],
      '#default_value' => isset($config['hero_xlarge_content_position']) ? $config['hero_xlarge_content_position'] : 'right',
      '#states' => [
        'visible' => [
          ':input[name="settings[hero_size]"]' => ['value' => 'xlarge'],
        ]
      ]
    ];

    $form['hero_breadcrumb_display'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display breadcrumb'),
      '#default_value' => isset($config['hero_breadcrumb_display']) ? $config['hero_breadcrumb_display'] : 0,
      '#states' => [
        'visible' => [
          ':input[name="settings[hero_size]"]' => ['!value' => 'xlarge']
        ]
      ]
    ];

    $form['hero_pretitle_display'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display pretitle'),
      '#default_value' => isset($config['hero_pretitle_display']) ? $config['hero_pretitle_display'] : 0,
      '#states' => [
        'invisible' => [
          ':input[name="settings[hero_size]"]' => ['value' => 'xlarge']
        ]
      ]
    ];

    $form['hero_pretitle_override'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Pretitle override'),
      '#description' => $this->t('Leave blank if default pretitle is desired.'),
      '#default_value' => isset($config['hero_pretitle_override']) ? $config['hero_pretitle_override'] : '',
      '#states' => [
        'visible' => [
          ':input[name="settings[hero_pretitle_display]"]' => ['checked' => true],
          ':input[name="settings[hero_size]"]' => ['!value' => 'xlarge']
        ],
      ]
    ];

    $form['hero_subtitle_display'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display subtitle'),
      '#default_value' => isset($config['hero_subtitle_display']) ? $config['hero_subtitle_display'] : 0,
      '#states' => [
        'visible' => [
          ':input[name="settings[hero_size]"]' => ['!value' => 'xlarge']
        ]
      ]
    ];

    $form['hero_subtitle_override'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subtitle override'),
      '#description' => $this->t('Leave blank if default subtitle is desired.'),
      '#default_value' => isset($config['hero_subtitle_override']) ? $config['hero_subtitle_override'] : '',
      '#states' => [
        'visible' => [
          ':input[name="settings[hero_subtitle_display]"]' => ['checked' => true],
          ':input[name="settings[hero_size]"]' => ['!value' => 'xlarge']
        ]
      ]
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['hero_size'] = $values['hero_size'];
    $this->configuration['hero_position'] = $values['hero_position'];
    $this->configuration['hero_use_video'] = $values['hero_use_video'];
    $this->configuration['hero_video_ref'] = $values['hero_video_ref'];
    $this->configuration['hero_xlarge_entity_ref'] = $values['hero_xlarge_entity_ref'];
    $this->configuration['hero_xlarge_content_position'] = $values['hero_xlarge_content_position'];
    $this->configuration['hero_breadcrumb_display'] = $values['hero_breadcrumb_display'];
    $this->configuration['hero_subtitle_display'] = $values['hero_subtitle_display'];
    $this->configuration['hero_subtitle_override'] = $values['hero_subtitle_override'];
    $this->configuration['hero_pretitle_display'] = $values['hero_pretitle_display'];
    $this->configuration['hero_pretitle_override'] = $values['hero_pretitle_override'];
  }

}
