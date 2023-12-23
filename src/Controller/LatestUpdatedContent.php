<?php

namespace Drupal\inlead_test_backend\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;

/**
 * Controller for LatestUpdatedContent.
 *
 * @package Drupal\inlead_test_backend\Controller
 */
class LatestUpdatedContent extends ControllerBase {

  /**
   * The file URL generator.
   *
   * @var \Drupal\Core\File\FileUrlGeneratorInterface
   */
  protected $fileUrlGenerator;

  /**
   * LatestUpdatedContent constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\File\FileUrlGeneratorInterface $file_url_generator
   *   The file URL generator.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, FileUrlGeneratorInterface $file_url_generator) {
    $this->entityTypeManager = $entity_type_manager;
    $this->fileUrlGenerator = $file_url_generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('file_url_generator')
    );
  }

  /**
   * Generate latest updated content json.
   *
   * @return JsonResponse
   */
  public function build() {
    return new JsonResponse(['data' => $this->getData(), 'method' => 'GET', 'status'=> 200]);
  }

  /**
   * Get data for lastest updated content
   *
   * @return array
   */
  public function getData() {

    $data=[];
    $query = $this->entityTypeManager->getStorage('node')->getQuery();
    $query->accessCheck(FALSE)
      ->sort('changed', 'DESC')
      ->range(0, 10);

    $nids = $query->execute();

    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);
    if ($nodes) {
      /** @var \Drupal\node\NodeInterface $node */
      foreach ($nodes as $node) {
        $data[] = [
          'title' => $node->getTitle(),
          'body' => $node->get('body')->getValue(),
          'image' => $this->fileUrlGenerator->generateAbsoluteString($node->field_image->entity->getFileUri()),
        ];
      }
    }
    return $data;
  }

}
