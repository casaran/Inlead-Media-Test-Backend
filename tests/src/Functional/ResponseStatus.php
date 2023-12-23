<?php

namespace Drupal\Tests\inlead_test_backend\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests response status of an API call.
 */
class ResponseStatus extends BrowserTestBase {

  /**
   * Tests that response to correct path returns 200.
   */
  public function testResponseStatus() {
    $actual_json = $this->drupalGet('api/v1/latest_updated_content', ['query' => ['_format' => 'json']]);
    $this->assertSession()->statusCodeEquals(200);
  }

}
