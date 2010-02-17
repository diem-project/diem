<?php

/**
 * Performs file operations on the project
 */
class dmAdminCodeEditor extends dmConfigurable
{
  protected
  $filesystem,
  $mimeTypeResolver,
  $fileBackup,
  $pathReplacements = array(
    '/' => '_-SLASH-_',
    '.' => '_-DOT-_',
    ' ' => '_-SPACE-_'
  );

  public function __construct(dmFilesystem $filesystem, dmMimeTypeResolver $mimeTypeResolver, dmFileBackup $fileBackup, array $options = array())
  {
    $this->filesystem       = $filesystem;
    $this->mimeTypeResolver = $mimeTypeResolver;
    $this->fileBackup       = $fileBackup;

    $this->initialize($options);
  }

  protected function initialize(array $options)
  {
    $this->configure($options);
  }

  public function getDefaultOptions()
  {
    return array(
      'path_replacements' => array(
        '/' => '_-SLASH-_',
        '.' => '_-DOT-_',
        ' ' => '_-SPACE-_'
      )
    );
  }

  protected function decodePath($path)
  {
    return dmProject::rootify(strtr($path, array_flip($this->getOption('path_replacements'))));
  }

  protected function encodePath($path)
  {
    return strtr(dmProject::unRootify($path), $this->getOption('path_replacements'));
  }

  public function saveFile($file, $data)
  {
    $file = $this->decodePath($file);

    if(!dmProject::isInProject($file))
    {
      throw new dmException('Can not save a file outside of the project');
    }

    if (!is_readable($file) || !is_file($file))
    {
      throw new dmException($file.' does not exist or is not readable');
    }

    $this->fileBackup->save($file);

    if(!file_put_contents($file, $data))
    {
      throw new dmException('Can not save file to '.$file);
    }
  }

  public function getFileAsArray($file)
  {
    $file = $this->decodePath($file);

    if (!is_readable($file) || !is_file($file))
    {
      throw new dmException($file.' does not exist or is not readable');
    }

    $mimeGroup = $this->mimeTypeResolver->getGroupByFilename($file);

    return array(
      'full_path'   => $file,
      'path'        => dmProject::unRootify($file),
      'is_writable' => is_writable($file) && !$this->fileBackup->isFileBackup($file),
      'is_image'    => 'image' === $mimeGroup,
      'code'        => 'image' !== $mimeGroup ? dmString::unixify(file_get_contents($file)) : ''
    );
  }

  public function getDirContent($dir)
  {
    $dir = $this->decodePath($dir);
    $content = array();

    $children = sfFinder::type('dir')->maxdepth(0)->not_name("/^\..*/")->follow_link()->in($dir);
    natcasesort($children);

    foreach( $children as $child )
    {
      $isReadable = is_readable($child);
      $isWritable = is_writable($child);

      $content[] = array(
        'attributes' => array(
          'id' => $this->encodePath($child),
          'class' => sprintf('dir %s %s',
             $isReadable ? 'readable_dir' : 'not_readable_dir',
             $isWritable ? 'writable_dir' : 'not_writable_dir'
          ),
          'rel' => 'folder'
        ),
        'state' => $isReadable ? 'closed' : '',
        'data' => basename($child)
      );
    }

    $files = sfFinder::type('file')->maxdepth(0)->not_name("/^\..*/")->follow_link()->in($dir);
    natcasesort($files);

    foreach( $files as $file )
    {
      $content[] = array(
        'attributes' => array(
          'id' => $this->encodePath($file),
          'class' => sprintf('file file_%s%s%s',
             strtolower(dmOs::getFileExtension($file, false)),
             is_readable($file) ? ' readable_file' : '',
             is_writable($file) ? ' writable_file' : ''
          ),
          'rel' => 'file'
        ),
        'data' => basename($file)
      );
    }

    return $content;
  }
}