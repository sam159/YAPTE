<?php

/**
 * Template renderer
 * 
 * @author Sam Stevens <sam@xnet.tk>
 * @license https://github.com/sam159/YAPTE/blob/master/LICENSE MIT License
 * @link https://github.com/sam159/YAPTE Find me on github
 */

if (!defined('DIR_TEMPLATES_COMPILED'))
  define('DIR_TEMPLATES_COMPILED', False);

class Template
{
  private static $currentTemplater = false;
  /**
   * @return Template
   */
  public static function GetInstance()
  {
    if (self::$currentTemplater != false)
      return self::$currentTemplater;
    return new self();
  }
  public static function ClearCompiled()
  {
    $files = glob(DIR_TEMPLATES_COMPILED.'/*.template.php');
    foreach($files as $file)
    {
      if (is_file($file))
        unlink($file);
    }
  }
  
  private $context = array();

  private $level = 0;
  
  private $sections = array();
  private $currentSection = false;
  private $master = false;
  
  private function __construct() {
  }
  
  private function GetCompiledFilename($tFile) {
    if (DIR_TEMPLATES_COMPILED == false) {
      return false;
    }
    $tFileSafe = preg_replace(array('%^'.preg_quote(DIR_TEMPLATES).'/%','/[^a-z0-9_\.]/i'), array('','_'), $tFile);
    return DIR_TEMPLATES_COMPILED.DS.$tFileSafe.'-'.md5($tFile).'.template.php';
  }
  private function GetCompiled($tFile) {
    $compiledFile = $this->GetCompiledFilename($tFile);
    if ($compiledFile === false) {
      return false;
    }
    if (file_exists($compiledFile) && filemtime($tFile) > filemtime($compiledFile)) {
      unlink($compiledFile);
      return False;
    }
    if (file_exists($compiledFile)) {
      return $compiledFile;
    }
  }
  private function SaveCompiled($tFile, $contents) {
    $compiledFile = $this->GetCompiledFilename($tFile);
    if ($compiledFile === false) {
      return false;
    }
    file_put_contents($compiledFile, '<?php if(!defined("DIR_TEMPLATES")) {echo "This file cannot be accessed directly";exit();} ?>'.$contents);
  }
  
  public function Render($file, array $context = array()) {
    self::$currentTemplater = $this;
    
    $tFile = str_replace('.', DS, $file);
    $tFile = DIR_TEMPLATES.DS.$tFile.'.php';
    if (!is_file($tFile))
      throw new Exception("Unknown Template ".$file);
    
    if (isset($this->context[$this->level]))
      $context = array_replace($this->context[$this->level], $context);
    $this->level++;
    $this->context[$this->level] = &$context;
    
    extract($this->context[$this->level], EXTR_REFS | EXTR_SKIP);
    
    $this->master = false;
    
    $compiledTemplate = $this->GetCompiled($tFile);
    if ($compiledTemplate == false) {
      $templateContents = file_get_contents($tFile);
      $templateContents = preg_replace(
          array('%{{t\s((?!}}).*?)\}}%', '%{{((?!}}).*?)}}%', '%{\%t\s((?!}}).*?)\%}%', '%{\%((?!}}).*?)\%}%'),
          array('<?php echo $this->$1 ;?>', '<?php echo $1 ;?>', '<?php $this->$1 ;?>', '<?php $1 ;?>'), 
          $templateContents);
      $this->SaveCompiled($tFile, $templateContents);
      $result = eval('?>'.$templateContents);
    } else {
      ob_start();
      include($compiledTemplate);
      $result = ob_get_clean();
    }
    
    self::$currentTemplater = false;
    if ($this->currentSection != False)
      $this->EndSection();
    
    if ($this->master != false) {
      return $this->Render($this->master);
    }
    
    unset($this->context[$this->level]);
    $this->level--;
    
    return $result;
  }
  
  private function SetMaster($file) {
    $this->master = $file;
  }
  private function StartSection($name) {
    $this->currentSection = $name;
    ob_start();
  }
  private function EndSection() {
    $this->sections[$this->currentSection] = ob_get_clean();
    $this->currentSection = false;
  }
  private function GetSection($name) {
    if (isset($this->sections[$name]))
      return $this->sections[$name];
    return '';
  }
}