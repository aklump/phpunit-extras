<?php

/**
 * @file
 * Create example code pages for all EasyMock test files.
 */

$example_files = $compiler->getFilesInDirectory(__DIR__ . '/../../tests/src/EasyMock', '/Test\.php$/');
foreach ($example_files as $example_file) {
  $markdown = $compiler->createMarkdownFromSourceCodeFile($example_file);
  $compiler->addSourceFile('em--' . $example_file->getFilename() . '.md', $markdown);
}
