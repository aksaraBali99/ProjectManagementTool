<?php

namespace App\Support;

use RuntimeException;

/**
 * task #124: why the release notes file is invalid. Thrown and caught
 * inside ReleaseNotes only; its message is shown on the Release notes page.
 */
class InvalidReleaseNotes extends RuntimeException {}
