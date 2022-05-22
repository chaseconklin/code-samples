<?php

namespace Drupal\announcement\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides an example block.
 *
 * @Block(
 *   id = "announcement_block",
 *   admin_label = @Translation("Announcement"),
 *   category = @Translation("Components")
 * )
 */
class AnnouncementBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = \Drupal::config('announcement.settings');
    $message = $config->get('message');
    $url = $config->get('url');
    $build['content'] = [
      '#markup' => $this->t('<a class="announcement__message" href=@url>@message</a>', [
        '@url' => $url,
        '@message' => $message,
      ]),
    ];
    return $build;
  }

}
