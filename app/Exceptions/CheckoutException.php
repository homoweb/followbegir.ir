<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when a checkout request violates business rules (bad quantity,
 * unknown product, deactivated account, ...).
 */
class CheckoutException extends RuntimeException {}
