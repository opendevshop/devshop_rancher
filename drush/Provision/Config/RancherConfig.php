<?php

use Symfony\Component\Yaml\Dumper;

/**
 * Base class for HTTP config files.
 *
 * This class will publish the config files to remote
 * servers automatically.
 */
class Provision_Config_RancherConfig extends Provision_Config {

  /**
   * Generate a YML array for this config.
   * @return array
   */
  function generateYml(){
    return [];
  }

  /**
   * Generates YML string from the generateYml() method.
   * @return string
   */
  function getYmlDump() {
    $dumper = new Dumper();
    return $dumper->dump($this->generateYml(), 10);
  }

  /**
   * Write out this configuration.
   *
   * 1. Make sure parent directory exists and is writable.
   * 2. Load template with load_template().
   * 3. Process $data with process().
   * 4. Make existing file writable if necessary and possible.
   * 5. Render template with $this and $data and write out to filename().
   * 6. If $mode and/or $group are set, apply them for the new file.
   */
  function write() {
    $filename = $this->filename();
    // Make directory structure if it does not exist.
    if ($filename && !provision_file()->exists(dirname($filename))->status()) {
      provision_file()->mkdir(dirname($filename))
        ->succeed('Created directory @path.')
        ->fail('Could not create directory @path.');
    }
    $status = FALSE;
    if ($filename && is_writeable(dirname($filename))) {
      // Make sure we can write to the file
      if (!is_null($this->mode) && !($this->mode & 0200) && provision_file()->exists($filename)->status()) {
        provision_file()->chmod($filename, $this->mode | 0200)
          ->succeed('Changed permissions of @path to @perm')
          ->fail('Could not change permissions of @path to @perm');
      }
      $status = provision_file()->file_put_contents($filename, $this->getYmlDump())
        ->succeed('Generated Drupal Console aliases: ' . (empty($this->description) ? $filename : $this->description . ' (' . $filename. ')'), 'success')
        ->fail('Could not generate Drupal Console aliases: ' . (empty($this->description) ? $filename : $this->description . ' (' . $filename. ')'))->status();
      // Change the permissions of the file if needed
      if (!is_null($this->mode)) {
        provision_file()->chmod($filename, $this->mode)
          ->succeed('Changed permissions of @path to @perm')
          ->fail('Could not change permissions of @path to @perm');
      }
      if (!is_null($this->group)) {
        provision_file()->chgrp($filename, $this->group)
          ->succeed('Change group ownership of @path to @gid')
          ->fail('Could not change group ownership of @path to @gid');
      }
    }
    return $status;
  }

  function unlink() {
    parent::unlink();
//    $this->data['server']->sync($this->filename());
  }
}
