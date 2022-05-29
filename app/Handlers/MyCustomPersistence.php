<?php

namespace App\Handlers\Persistence;

use Eljam\GuzzleJwt\JwtToken;
use Eljam\GuzzleJwt\Persistence\TokenPersistenceInterface;

class MyCustomPersistence implements TokenPersistenceInterface
{
  /**
   * Save the token data.
   *
   * @param JwtToken $token
   */
  public function saveToken(JwtToken $token)
  {
    return $token;
  }

  /**
   * Retrieve the token from storage and return it.
   * Return null if nothing is stored.
   *
   * @return JwtToken Restored token
   */
  public function restoreToken()
  {
    return null;
  }

  /**
   * Delete the saved token data.
   */
  public function deleteToken()
  {
    return;
  }

  /**
   * Returns true if a token exists (although it may not be valid)
   *
   * @return bool
   */
  public function hasToken()
  {
    return false;
  }
}
