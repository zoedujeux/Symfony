<?php

namespace OC\PlatformBundle\Antispam;

class OCAntispam extends \Twig_Extension
{
  private $mailer;
  private $locale;
  protected $nbForSpam;

  public function __construct(\Swift_Mailer $mailer, $nbForSpam)
  {
    $this->mailer    = $mailer;
    $this->nbForSpam = (int) $nbForSpam;
  }
  
    public function setLocale($locale)
  {
    $this->locale = $locale;
  }

  /**
   * Vérifie si le texte est un spam ou non
   *
   * @param string $text
   * @return bool
   */
  public function isSpam($text)
  {
    return strlen($text) < 50;
  }
  
  public function getName() 
    {
      return 'OCAntispam';
    }
  
  public function getFunctions()
  {
      return array(
//          new \Twig_SimpleFunction('checkIfSpam', [$this, 'isSpam'])
           'checkIfSpam' => new \Twig_Function_Method($this, 'isSpam')
      );
  }
  
    
}